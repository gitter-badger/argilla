<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.rbac.controllers
 */
class BRbacOperationController extends BController
{
  public $name = 'Действия';

  public $modelClass = 'BRbacOperation';

  public $position = 40;

  public $enabled = false;
}
