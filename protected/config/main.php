<?php
define(
	'URL',
	'http' . ((empty($_SERVER['HTTPS']) === false) ? 's' : '') . '://'
		. $_SERVER['SERVER_NAME'] . (((int)$_SERVER['SERVER_PORT'] !== 80) ? ':'
	. $_SERVER['SERVER_PORT'] : '')
	. preg_replace('/\/[^\/]+\.php.*/', '', $_SERVER['SCRIPT_NAME'])
);

define('DATE', time());

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Accommodation Today',

    // set the default controller
    // 'defaultController' => 'Dashboard',

	// preloading 'log' component
	'preload' => array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
        'application.helpers.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool

		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'pass',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),

	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		
		'clientScript'=>array(
			'class'=>'ext.minScript.components.ExtMinScript',
			'minScriptDebug' => false,
        ),
		
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
		    'showScriptName' => true, // hides the index.php if false (set to true on production)
// 			'rules'=>array(
// 				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
// 				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
// 				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
// 			),
// 			'rules'=>array(
// 				'gii'=>'gii',
// 				'gii/<controller:\w+>'=>'gii/<controller>',
// 				'gii/<controller:\w+>/<action:\w+>'=>'gii/<controller>/<action>',
// 			)
		),

		'db'=>array(
		    'class' => 'CDbConnection',
			'connectionString' => 'mysql:host=localhost;dbname=accommo_today',
			'emulatePrepare' => true,
			'username' => 'root', // accommo_admin
			'password' => 'rjfrank', // sh@Z@@M55
			'charset' => 'utf8',
		),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),

        // image resizing class
        'image'=>array(
          'class' => 'application.extensions.image.CImageComponent',
            // GD or ImageMagick
            'driver'=>'GD',
            // ImageMagick setup path
            'params'=>array('directory'=>'/opt/local/bin'),
        ),
        
 		// 'log'=>array(
 			// 'class'=>'CLogRouter',
 			// 'routes'=>array(
 				// array(
 					// 'class'=>'CFileLogRoute',
 					// 'levels'=>'error, warning',
 				// ),
 				// // uncomment the following to show log messages on web pages
 				// array(
 					// 'class'=>'CWebLogRoute',
 				// ),
// 
 			// ),
 		// ),
	),
	
	'controllerMap'=>array
	(
		'min'=>array
		(
			'class'=>'ext.minScript.controllers.ExtMinScriptController',
		),
    ),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'briangouws@gmail.com',
	),

);