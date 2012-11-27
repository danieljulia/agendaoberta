<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Agenda Oberta',
	'id'=>'ao-api',
	'language' => 'ca',
	'sourceLanguage' => '00',
    
	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
			'application.vendors.*',
	),

 
	// application components
	'components'=>array(
		'messages'=>array(
			'class'=>'CPhpMessageSource',
		),
		'coreMessages'=>array(
			'basePath'=>null,
		),
            
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
				'pags/<view:\w+>'=>'site/page',
				'<controller:\w+>'=>'<controller>/index',
				'<controller:\w+>/<action:[a-z0-9_-]+><out:\.[a-z]{3,4}>'=>'<controller>/<action>',
				'<controller:\w+>/<action:[a-z0-9_-]+>'=>'<controller>/<action>',
			),
		),
		
		
		/*
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),
		*/
		// uncomment the following to use a MySQL database
		
		'db'=>array(			
			'connectionString' => 'mysql:host=localhost;dbname=xxxxx',			
			'username' => 'xxxxx',
			'password' => 'xxxxxx',
			'charset' => 'utf8',
			'tablePrefix' => 'ao_',
			'emulatePrepare'=>true,
			'enableParamLogging'=>true,
			//'schemaCachingDuration'=>24*3600,
		),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
				'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',                                       
				),
				array(
					'class'=>'CFileLogRoute',
					'categories'=>'app',
					'logFile'=>'info.log'
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
		'cache'=>array(
			'class'=>'CFileCache',
		),
	),
	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		'ga_code'=>'xxxxxx',
        'ga_domain'=>'xxxxxx',
	),
);