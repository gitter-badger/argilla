<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.sitemap
 */
class ProductOneLocation extends LocationBase
{
  public function __construct(CController $controller)
  {
    parent::__construct($controller);

    $this->_modelSource = new CDataProviderIterator(new CActiveDataProvider('Product'));
  }

  /**
   * @return string
   */
  public function current()
  {
    /** @var $current Product */
    $current = $this->_modelSource->current();

    return Yii::app()->request->hostInfo.$current->url;
  }

  /**
   * @return string
   */
  public function getRoute()
  {
    return 'product/one';
  }
}