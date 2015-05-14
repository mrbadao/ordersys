<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Thức ăn cú đêm',
	'language' => 'vi',
    'defaultController' =>'Content',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.vendors.*',
		'application.widgets.*',
	),

	'modules'=>array(
		'category',
		'product',
		'cart',
	),

	// application components
	'components'=>array(

		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),

		'viewRenderer'=>array(
			'class'=>'ext.PHPTALViewRenderer',
			'fileExtension' => '.html',
		),

		'session' => array (
			'autoStart' => true,
			'class' => 'system.web.CDbHttpSession',
			'connectionID' => 'db',
			'sessionTableName' => 'orsersys_sessions',
		),

		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
				'content/<action:\w+>'=> 'content/<action>',

                array(
                    'class' => 'application.components.ContentUrlRule',
                    'connectionID' => 'db',
                ),
                array(
                    'class' => 'application.components.CategoryUrlRule',
                    'connectionID' => 'db',
                ),
                array(
                    'class' => 'application.components.CartUrlRule',
                    'connectionID' => 'db',
                ),
                '<module:\w+>/<action:\w+>'=>'<module>/default/<action>',
			),
		),

		// database settings are configured in database.php
		'db'=>require(dirname(__FILE__).'/database.php'),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'content/error',
		),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			),
		),

	),

	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
        'appName' => 'Owl Food Shop',
        'shopLocation' => array('lat' => '10.8172357', 'lng' => '106.63364409999997'),
	),
);
