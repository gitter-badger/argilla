<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 */
Yii::import('frontend.models.product.filter.search.*');


/**
 * Class Filter
 * @property string $filterKey
 * @property FilterElement[] $elements
 * @property FilterRender $render
 * @property FilterState $state
 */
class Filter extends CComponent
{
  /**
   * тип элемнта по умолчанию.
   */
  public $defaultElementType = 'checkbox';

  public $emptyElementValue = array('' => 'Не задано');

  public $urlPattern;

  /**
   * @var FilterElement[]
   */
  private $elements = array();

  /**
   * @var FilterRender
   */
  private $render;

  private $filterKey;

  /**
   * @var FilterState
   */
  private $state;

  /**
   * @var array
   */
  private $defaultSelectedItems = array();

  /**
   * @param string $filterKey - уникальный ключ фильтра
   * @param bool $useSession - сохраняет состояние в сессию, также считывает оттуда
   */
  public function __construct($filterKey = null, $useSession = true)
  {
    if( !isset($filterKey) )
    {
      $filterKey = $this->getDefaultFilterKey();
    }

    $this->filterKey = $filterKey;
    $this->render = new FilterRender($this);

    $this->state = new FilterState($this->filterKey, $useSession);
    $this->state->processState(Yii::app()->request->getParam($this->filterKey, array()));
  }

  public function addElement(array $filterElement, $emptyValue = false)
  {
    $items = array();

    if( $emptyValue !== false )
    {
      $defaultItemOptions = array(
        'id' => key($this->emptyElementValue),
        'class' => 'FilterElementItem',
        'label' => reset($this->emptyElementValue),
      );

      if( is_array($emptyValue) )
        $defaultItemOptions = CMap::mergeArray($defaultItemOptions, $emptyValue);

      $items[key($this->emptyElementValue)] = Yii::createComponent($defaultItemOptions);
    }

    if( !isset($filterElement['id']) && !empty($filterElement['key']) )
      $filterElement['id'] = ProductParameterName::model()->findByAttributes(array('key' => $filterElement['key']))->id;
    else if( empty($filterElement['key']) )
      $filterElement['key'] = $filterElement['id'];

    if( empty($filterElement['type']) )
      $filterElement['type'] = $this->defaultElementType;

    if( class_exists('FilterElement'.ucfirst($filterElement['type'])) )
      $filterElement['class'] = 'FilterElement'.$filterElement['type'];
    else
      throw new CHttpException(500, "Не удалось найти класс 'FilterElement{$filterElement['type']}.");

    /**
     * @var $element FilterElement
     */
    $element = Yii::createComponent(CMap::mergeArray(array('parent' => $this, 'items' => $items), $filterElement), $this);
    $element->init($this);

    $this->elements[$filterElement['id']] = $element;
  }

  /**
   * @return string
   */
  public function getFilterKey()
  {
    return $this->filterKey;
  }

  /**
   * @return FilterRender
   */
  public function getRender()
  {
    return $this->render;
  }

  /**
   * @return FilterState
   */
  public function getState()
  {
    return $this->state;
  }

  /**
   * @param CDbCriteria $actionCriteria
   *
   * @return CDbCriteria
   */
  public function apply(CDbCriteria $actionCriteria)
  {
    $dataProvider = new FilterDataProvider($this->state, $actionCriteria, $this->elements);
    $criteria = $dataProvider->getFilteredCriteria();

    $this->sendAmountResponse($criteria);

    $itemAmounts = $dataProvider->getAmounts();
    $this->buildItems($itemAmounts);

    return $criteria;
  }

  /**
   * @return FilterElementItem[]
   */
  public function getSelectedItems()
  {
    $selected = $this->defaultSelectedItems;

    foreach($this->elements as $element)
      $selected = CMap::mergeArray($selected, $element->getSelectedItems());

    return $selected;
  }

  /**
   * @param FilterElementItem[] $items
   */
  public function setDefaultSelectedItems($items)
  {
    $this->defaultSelectedItems = $items;
  }

  /**
   * Устанавливаем выбранные элементы фильтра из массива моделей разводной страницы
   *
   * @param array $models
   */
  public function setSelectedModels(array $models)
  {
    $items = array_reduce($models, function ($result, $item)
    {
      if( !($item instanceof FActiveRecord) )
        return null;

      if( $element = $this->getElementByKey(strtolower(str_replace('Product', '', get_class($item)).'_id')) )
      {
        $element->disabled[$item->id] = $item->id;
      }

      $result[] = Yii::createComponent(array(
        'class' => 'FilterElementItemSelected',
        'parent' => $element ? $element : null,
        'label' => $item->name,
      ));

      return $result;
    }, array());

    $this->setDefaultSelectedItems($items);
  }

  public function removeElement($elementId)
  {
    if( isset($this->elements[$elementId]) )
      unset($this->elements[$elementId]);
  }

  /**
   * @param array $exclude
   *
   * @return FilterElement[]
   */
  public function getElements(array $exclude = array())
  {
    return array_filter($this->elements, function (FilterElement $element) use ($exclude)
    {
      return !in_array($element->id, $exclude) && count($element->getItems());
    });
  }

  public function getElementByKey($key)
  {
    foreach($this->elements as $element)
    {
      if( $element->key == $key )
        return $element;
    }

    return null;
  }

  /**
   * @param array $elementsIds
   */
  public function disableElements(array $elementsIds)
  {
    foreach($elementsIds as $id)
      if( isset($this->elements[$id]) )
        $this->elements[$id]->disableAll();
  }

  private function getDefaultFilterKey()
  {
    return preg_replace('/[^\w]/', '_', preg_replace('/\?.*/', '', Yii::app()->controller->getCurrentUrl()));
  }

  private function buildItems($itemAmounts)
  {
    foreach($this->elements as $element)
    {
      $availableValues = Arr::get($itemAmounts, $element->id, array());
      $element->buildItems($availableValues);
      $element->setSelected($this->state);
    }
  }

  /**
   * @param CDbCriteria $criteria
   */
  private function sendAmountResponse(CDbCriteria $criteria)
  {
    if( $this->getState()->isAmountOnly() )
    {
      echo CJSON::encode(array(
        'amount' => count($criteria->params),
      ));

      Yii::app()->end();
    }
  }
}