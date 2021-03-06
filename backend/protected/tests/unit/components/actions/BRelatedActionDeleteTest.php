<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BRelatedActionDeleteTest extends CDbTestCase
{
  protected $fixtures = array(
    'seo_link_section' => 'BLinkSection',
    'seo_link' => 'BLink',
  );

  public function setUp()
  {
    Yii::app()->setUnitEnvironment('seo', 'BLinkSection');
    Yii::app()->setAjaxRequest();

    parent::setUp();
  }

  /**
   * @expectedException CHttpException
   */
  public function testPostOnlyException()
  {
    $_SERVER['HTTP_X_REQUESTED_WITH'] = '';
    Yii::app()->setUnitEnvironment('seo', 'BLink');
    $action = new BRelatedActionDelete(Yii::app()->controller, 'delete');
    $action->run();
  }

  public function testRun()
  {
    $_POST['id']       = 1;
    $_POST['relation'] = 'links';

    $model = BLink::model()->findByPk(1);
    $this->assertNotNull('BActiveRecord', $model);

    $action = new BRelatedActionDelete(Yii::app()->controller, 'delete');
    $action->run();

    $model = BLink::model()->findByPk(1);
    $this->assertNull($model);
  }
}