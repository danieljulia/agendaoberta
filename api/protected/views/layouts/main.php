<?php
	$baseUrl = Yii::app()->baseUrl;
	$cs = Yii::app()->clientScript;
	/* @var $cs CClientScript */
	$cs->registerCoreScript('jquery');
	$cs->registerScriptFile($baseUrl.'/bootstrap/js/bootstrap.js',  CClientScript::POS_END);
	
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">

    <!-- Le styles -->

    <link href="<?php echo $baseUrl ?>/bootstrap/css/bootstrap.css" rel="stylesheet">
		<link href="<?php echo $baseUrl ?>/css/main.css" rel="stylesheet">
    <link href="<?php echo $baseUrl ?>/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">

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
		
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">

          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="<?php echo $baseUrl ?>">Oberta API</a>
          <div class="nav-collapse">
<?php				
						
		$this->widget('application.components.bootstrap.BMenu',array(			
			'items'=>array(
				array('label'=>'Categories', 'url'=>array('/category/index'),'active'=>$this->id=='category'),		
				array('label'=>'Esdeveniments', 'url'=>array('/event/index'),'active'=>$this->id=='event'),

			),
			'htmlOptions'=>array('class'=>'nav'),
		));						
			?>			
						<div class="navbar-text pull-right"><?php echo CHtml::link('Contacte',array('/site/page','view'=>'contacte'))?></div>
						
          </div><!--/.nav-collapse -->										
        </div>				
      </div>

    </div>

    <div class="container">
			
      <?php echo $content; ?>

			
		<footer>
			<a href="http://oberta.cat">oberta.cat</a>
		</footer>
			
    </div> <!-- /container -->
	
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', <?php echo Yii::app()->params['ga_code']?>);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

  </body>
</html>
