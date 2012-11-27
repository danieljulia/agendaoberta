<?php
	$baseUrl = Yii::app()->baseUrl;
	$cs = Yii::app()->clientScript;
	/* @var $cs CClientScript */
	$cs->registerCoreScript('jquery');
	$cs->registerScriptFile($baseUrl.'/admin/bootstrap/js/bootstrap.js',  CClientScript::POS_END);
	
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">

    <!-- Le styles -->

    <link href="<?php echo $baseUrl ?>/admin/bootstrap/css/bootstrap.css" rel="stylesheet">
		<link href="<?php echo $baseUrl ?>/admin/css/main.css" rel="stylesheet">
    <link href="<?php echo $baseUrl ?>/admin/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="<?php echo $baseUrl ?>/favicon.ico">

		<title><?php echo CHtml::encode($this->pageTitle); ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">

  </head>

  <body>

    <div class="container">

			
      <?php echo $content; ?>
			
			
    </div> <!-- /container -->


  </body>
</html>