<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', true);

$backend = realpath(__DIR__.'/..');
$frontend = realpath(__DIR__.'/../../../protected');

return array_replace_recursive(array(
  'name' => 'backend',
  'basePath' => $backend,
  'controllerPath' => $backend.'/controllers',
  'viewPath' => $backend.'/views',
  'runtimePath' => $backend.'/runtime',
  'defaultController' => 'base',

  'aliases' => array(
    'backend' => $backend,
    'frontend' => $frontend,
    'ext' => 'frontend.extensions',
    'nestedset' => 'ext.nested-set-behavior',
    'upload' => 'ext.upload',
    'share' => 'frontend.share',
    'bootstrap' => 'ext.bootstrap',
  ),

  'preload' => array(
    'log',
  ),

  'import' => array(
    'backend.models.*',
    'backend.models.behaviors.*',
    'backend.components.*',
    'backend.components.actions.*',
    'backend.components.auth.*',
    'backend.components.db.*',
    'backend.components.test.*',
    'backend.components.interfaces.*',
    'backend.controllers.*',
    'backend.widgets.*',
    'backend.widgets.grid.*',
    'backend.widgets.form.*',
    'backend.modules.rbac.components.*',
    'backend.modules.rbac.models.*',
    'backend.modules.menu.components.*',

    'share.*',
    'share.validators.*',
    'share.formatters.*',

    'ext.*',
    'ext.jtogglecolumn.*',
    'ext.wysiwyg.*',
    'ext.upload.components.*',
    'ext.upload.models.*',
    'ext.upload.*',
    'ext.onflyedit.*',
  ),

  'modules' => array(
    'gii' => array(
      'generatorPaths' => array('bootstrap.gii', 'backend.gii.generators'),
      'class' => 'system.gii.GiiModule',
      'password' => '123',
      'ipFilters' => array('192.168.*', '127.0.0.1', '::1'),
    ),
  ),

  'components' => array(

    'user' => array(
      'allowAutoLogin' => true,
      'loginUrl' => array('/'),
    ),

    'authManager' => array(
      'class'           => 'CDbAuthManager',
      'connectionID'    => 'db',
      'itemTable'       => '{{auth_item}}',
      'itemChildTable'  => '{{auth_item_child}}',
      'assignmentTable' => '{{auth_assignment}}',

    ),

    'clientScript' => array(
      'class' => 'ext.mainscript.components.SClientScript',
    ),

    'assetManager' => array(
      'class' => 'ext.mainscript.components.SAssetManager',
    ),

    'mainscript' => array(
      'class' => 'ext.mainscript.ScriptsFactory',
      'debug' => YII_DEBUG,
      'mode'  => 'backend',
    ),

    'urlManager' => array(
      'urlFormat' => 'path',
      'showScriptName' => false,
      'rules' => array(
        '<module:\w+>/<controller:\w+>/<action:\w+>/<id:(\w+|(\w+)\:(\w+)|(\w+)\:(\w+)\:(\w+))>' => '<module>/<controller>/<action>',
        '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
        '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
        '<controller:\w+>/<id:\d+>' => '<controller>/view',
      ),
    ),

    'db' => array_replace_recursive(require($frontend.'/config/db.php'),
      array(
        'enableProfiling'    => YII_DEBUG,
        'enableParamLogging' => YII_DEBUG,
      )
    ),

    'bootstrap' => array(
      'class' => 'ext.bootstrap.components.Bootstrap',
    ),

    'menu' => array(
      'class' => 'BMenu'
    ),

    'breadcrumbs' => array(
      'class' => 'BBreadcrumbsManager',
    ),

    'phpThumb' => array(
      'class' => 'ext.phpthumb.EPhpThumb',
      'options' => array()
    ),

    'errorHandler' => array(
      'errorAction' => 'base/error',
    ),

    'format' => array(
      'class'          => 'SFormatter',
      'datetimeFormat' => 'd.m.Y H:i:s',
      'dateFormat'     => 'd.m.Y',
      'timeFormat'     => 'H:i:s',
      'numberFormat'   => array(
        'decimals'          => 0,
        'decimalSeparator'  => ',',
        'thousandSeparator' => ' '
      )
    ),

    'log' => array(
      'class' => 'CLogRouter',
      'enabled' => true,
      'routes' => array(
        array(
          'class' => 'CFileLogRoute',
          'levels' => 'error, warning',
        ),
        array(
          'class' => 'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
          'ipFilters' => array('192.168.*'),
        ),
      ),
    ),

    'email' => array(
      'class'    => 'ext.email.Email',
      'delivery' => 'php',
    ),

    'notification' => array(
      'class' => 'share.SNotification',
    ),
  ),

  'params' => array(
    'adminEmail' => 'glagolev@shogo.ru',
    // Уникальная соль, формируемая при разворачивании приложения
    'salt' => 'backup',
    // backend|console|test
    'mode' => 'backend',
  ),

  'sourceLanguage' => 'ru_ru',
  'language' => 'ru',
), require($frontend.'/config/share.php'));
?>