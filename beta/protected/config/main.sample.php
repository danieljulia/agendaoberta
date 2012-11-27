<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']),'\\/');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Agenda Oberta',
	'id'=>'ao-back',
	'language' => 'ca',
	'sourceLanguage' => '00',
    
	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.components.parsers.*',
		'application.components.scrapers.*',
		'application.components.bootstrap.*',
		'application.vendors.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'badalona4ever',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('*','127.0.0.1','::1'),
		),
		
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
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		
		
		/*
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),
		*/
		// uncomment the following to use a MySQL database
		
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=agendaoberta',
			'emulatePrepare' => true,
			'username' => 'oberta',
			'password' => 'aqw34vgd2',
			'charset' => 'utf8',
			'tablePrefix' => 'ao_',
			'enableParamLogging'=>true,
			//'schemaCachingDuration'=>24*3600,				
		),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'widgetFactory'=>array(
			'widgets'=>array(
				'CGridView'=>array(
					'baseScriptUrl'=>$baseUrl.'/admin/css/gridview',
					'itemsCssClass'=>'table table-striped table-condensed',
					'pagerCssClass'=>'pagination pagination-centered',
					'summaryText'=>'<span class="badge">{start}-{end} / {count}</span>',
					'pager'=>array('class'=>'LinkPager'),
				),
				'CListView'=>array(
					'pagerCssClass'=>'pagination pagination-centered',
					'summaryText'=>'<span class="badge">{start}-{end} / {count}</span>',
					'pager'=>array('class'=>'LinkPager'),
				),
				'LinkPager'=>array(
					'cssFile'=>false,
					'header'=>false,
					'hiddenPageCssClass'=>'disabled',
					'selectedPageCssClass'=>'active',
				),
				'CDetailView'=>array(
					'cssFile'=>false,
					'htmlOptions'=>array('class'=>'table table-bordered table-striped'),
				),
			),				
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
					'levels'=>'info',                                   
					'logFile'=>'info.log'
				),
				// uncomment the following to show log messages on web pages
				
				
				array(
					'class'=>'CWebLogRoute',
				),
                                 
                                 
				
			),
		),
		
            /*
		'CURL' =>array(
			'class' => 'application.extensions.curl.Curl',
			'options'=>array(
				'timeout'=>0,
			
				 //you can setup timeout,http_login,proxy,proxylogin,cookie, and setOPTIONS
				 
				 'setOptions'=>array(
					CURLOPT_FOLLOWLOCATION  => false,
					CURLOPT_USERAGENT => Yii::app()->params['agent']
					),
			)
		 ),
 
 */
		
	),
	
	//quin controlador es crida per defecte
	//'defaultController'=>'AoLocation',

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'dani@kiwoo.org',
		'cron_window'=>array(2 ,8),
		'parser_email_error'=>'daniel.julia@gmail.com',
		'max_items_per_cron'=>10,
		'postprocess_items_per_cron'=>10,
		'max_errors_per_source'=>3,
		'scraper_max_chars'=>50000,
		'thumbs_path'=>'../thumbs', //path per a guardar thumbs 
		'thumbs'=>'/thumbs', //base url per a visualitzar els thumbs
		'agent'=>'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.79 Safari/535.11',
		'classifyReadApiKey'=>'SRjonaGk1q3QH1ruhNL3mU2AzuA',
		'classifyWriteApiKey'=>'5M9YKONckvgdMyvbArc4aZZ9bQ',
		'default_source_id'=>1,
	),
);