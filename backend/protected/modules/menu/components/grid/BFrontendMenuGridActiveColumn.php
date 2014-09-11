<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.menu.components.grid
 */
class BFrontendMenuGridActiveColumn extends JToggleColumn
{
  public $menu_id;

  /**
   * @param array $button
   * @param int $row
   * @param BFrontendMenuGridAdapter $data
   */
  protected function renderButton($button, $row, $data)
  {
    $button['url'] = 'Yii::app()->controller->createUrl("menu/switchEntry", array(
      "type" => $data->getType(),
      "item_id" => $data->getId(),
      "menu_id" => $this->menu_id,
    ))';

    parent::renderButton($button, $row, $data); // TODO: Change the autogenerated stub
  }
}