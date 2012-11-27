<?php
/**
 * This is the configuration for generating message translations
 * for the Yii framework. It is used by the 'yiic message' command.
 */
return array(
	'sourcePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'messagePath'=>dirname(__FILE__),
	'languages'=>array('ca'),
	'fileTypes'=>array('php'),
    'overwrite'=>true,
	'exclude'=>array(
		'.svn',
		'.git',
		'yiilite.php',
		'yiit.php',
		'/i18n/data',
		'/messages',
		'/vendors',
		'/web/js',
		'/gii',
	),
);
