<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.sitemap
 */
class ProductSectionLocation extends LocationBase
{
  /**
   * @param CController $controller
   */
  function __construct(CController $controller)
  {
    parent::__construct($controller);

    $this->_modelSource = new CDataProviderIterator(new CActiveDataProvider(ProductSection::model()));
  }

  /**
   * @return string
   */
  public function current()
  {
    /** @var $current ProductSection */
    $current = $this->_modelSource->current();

    return $this->_controller->createAbsoluteUrl($this->getRoute(), array('section' => $current->url));
  }

  /**
   * @return string
   */
  public function getRoute()
  {
    return 'product/section';
  }
}