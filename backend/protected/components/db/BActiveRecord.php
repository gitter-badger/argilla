<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components
 */
abstract class BActiveRecord extends CActiveRecord
{
  /**
   * @param string $className
   *
   * @return BActiveRecord|CActiveRecord
   */
  public static function model($className = __CLASS__)
  {
    return parent::model(get_called_class());
  }

  /**
   * Получение CHtml::listData по стандартным ключам id => name
   *
   * @param string $key
   * @param string $value
   * @param CDbCriteria $criteria
   *
   * @return array
   */
  public static function listData($key = 'id', $value = 'name', CDbCriteria $criteria = null)
  {
    if( $criteria === null )
      $criteria = new CDbCriteria();

    return CHtml::listData(static::model()->findAll($criteria), $key, $value);
  }

  /**
   * Получение имени таблицы по имени текущей модели в виде {{class_name}}
   *
   * @return string
   */
  public function tableName()
  {
    return Utils::camelToSnakeCase(BApplication::cutClassPrefix(get_class($this)));
  }

  /**
   * @param bool $runValidation
   * @param null $attributes
   * @return bool
   */
  public function save($runValidation = true, $attributes = null)
  {
    if( $this->isNestedSetModel() )
    {
      if( $this->isNewRecord )
      {
        $modelName = get_class($this);
        $parent    = $modelName::model()->findByPk($this->parent);
        $result    = $this->appendTo($parent);
      }
      else
        $result = $this->saveNode($runValidation, $attributes);

      return $result;
    }

    return parent::save($runValidation, $attributes);
  }

  /**
   * Сохраняем данные в моделях, связанных через отношение
   *
   * @param $relationName
   * @param array $relatedData
   * @param bool $ignoreEmptyItems
   *
   * @throws CHttpException
   */
  public function saveRelatedModels($relationName, array $relatedData, $ignoreEmptyItems = true)
  {
    $relation  = $this->getActiveRelation($relationName);
    $className = $relation->className;

    foreach($relatedData as $id => $item)
    {
      $value = trim(implode("", $item));
      if( empty($value) && $ignoreEmptyItems )
        continue;

      /**
       * @var BActiveRecord $model
       */
      $model = $className::model()->findByPk($id);

      if( !$model )
      {
        $model = new $className();
        $model->{$relation->foreignKey} = $this->getPrimaryKey();
      }

      $model->setAttributes(Arr::trim($item));

      if( !$model->save() )
      {
        throw new CHttpException(500, 'Не удается сохранить зависимую модель');
      }
    }
  }

  public function getFormId()
  {
    return get_called_class().'-form';
  }

  public function yesNoList()
  {
    return array(
      array('id' => 1, 'name' => 'Да'),
      array('id' => 0, 'name' => 'Нет'),
    );
  }

  public function getImageTypes()
  {
    return array();
  }

  public function relations()
  {
    return array(
      'associations' => array(self::HAS_MANY, 'BAssociation', 'src_id', 'on' => 'src="'.get_class($this).'"'),
    );
  }

  /**
   * @return BActiveDataProvider
   */
  public function search()
  {
    $criteria = $this->getSearchCriteria();
    $params = $this->getSearchParams();

    return new BActiveDataProvider($this, CMap::mergeArray(array('criteria' => $criteria), $params));
  }

  /**
   * @return CDbCriteria
   */
  public function getSearchCriteria()
  {
    return new CDbCriteria();
  }

  /**
   * @return array of search params
   */
  protected function getSearchParams()
  {
    return array();
  }

  public function attributeLabels()
  {
    return array(
      'id'               => '#',
      'section_id'       => 'Раздел',
      'category_id'      => 'Категория',
      'type_id'          => 'Тип',
      'collection_id'    => 'Коллекция',
      'country_id'       => 'Страна',
      'year_id'          => 'Год',
      'position'         => 'Позиция',
      'url'              => 'Url',
      'reference'        => 'Ссылка',
      'date'             => 'Дата',
      'notice'           => 'Анонс',
      'name'             => 'Заголовок',
      'articul'          => 'Артикул',
      'content'          => 'Полный текст',
      'img'              => 'Изображение',
      'upload'           => 'Изображения',
      'template'         => 'Шаблон',
      'visible'          => 'Вид',
      'main'             => 'На главной',
      'novelty'          => 'Новинка',
      'discount'         => 'Скидка',
      'delivery'         => 'Доставка',
      'dump'             => 'Склад',
      'siblings'         => 'Соседи',
      'children'         => 'Потомки',
      'menu'             => 'В меню',
      'sitemap'          => 'На карту сайта',
      'tag'              => 'Теги',
      'price'            => 'Цена',
      'price_old'        => 'Старая цена',
      'key'              => 'Ключ',
      'location'         => 'Расположение',
      'group'            => 'Группа',
      'date_from'        => 'Дата с...',
      'date_to'          => 'по ...',
      'comment'          => 'Комментарий',
      'sum'              => 'Сумма',
      'email'            => 'E-mail',
      'phone'            => 'Телефон',
      'address'          => 'Адрес',
      'date_create'      => 'Дата создания',
      'status'           => 'Статус',
      'subject'          => 'Тема',
      'message'          => 'Сообщение',
      'password'         => 'Пароль',
      'password_confirm' => 'Подтверждение пароля',
      'type'             => 'Тип',
      'sysname'          => 'Системное имя',
    );
  }

  /**
   * Проверка на поддержку моделью nested set
   * @return bool
   */
  private function isNestedSetModel()
  {
    return $this->asa('nestedSetBehavior') !== null;
  }
}