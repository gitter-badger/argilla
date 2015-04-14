<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * public function behaviors()
 * {
 *   return array(
 *     'commonAssociationBehavior' => array('class' => 'backend.modules.product.modules.commonAssociation.frontend.CommonAssociationBehavior'),
 *   );
 * }
 */
Yii::import('backend.modules.product.modules.commonAssociation.frontend.CommonAssociation');
/**
 * Class AssociationBehavior
 *
 * @property FActiveRecord $owner
 */
class CommonAssociationBehavior extends SActiveRecordBehavior
{
  /**
   * Возвращает привязки к текушей модели
   * @return Association
   */
  public function getCommonAssociation()
  {
    return CommonAssociation::model()->setTagByModel($this->owner);
  }
}