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
		
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">

          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="<?php echo $baseUrl ?>">Oberta Web</a>
          <div class="nav-collapse">
						<?php
						$this->widget('application.components.bootstrap.BMenu',array(			
							'items'=>array(
								array('label'=>'Inici', 'url'=>array('/site/index'), ),
								array('label'=>'Poblacions', 'url'=>array('/city/index'), 'active'=>$this->id=='city'),
								array('label'=>'Fonts', 'url'=>array('/source/index'), 'active'=>$this->id=='source'),
								array('label'=>'Categories', 'url'=>array('/category/index'), 'active'=>$this->id=='category'),
								array('label'=>'Esdeveniments', 'url'=>array('/event/index'), 'active'=>$this->id=='event'),
								array('label'=>'Usuaris', 'url'=>array('/user/index'), 'active'=>$this->id=='user'),
							),
							'htmlOptions'=>array('class'=>'nav'),
						));						
					?>
			<?php if (!Yii::app()->user->isGuest): ?>
				<div class="navbar-text pull-right">
					<?php echo Yii::t('admin','Hello, {name}.',array('{name}'=>Yii::app()->user->name)) ?>
		[<?php echo CHtml::link(Yii::t('admin','logout'), array('/site/logout')) ?>]

				</div>
			<?php endif ?>
						
          </div><!--/.nav-collapse -->										
        </div>				
      </div>

    </div>

    <div class="container">
		<?php $this->widget('application.components.bootstrap.BBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
			'homeLink'=>CHtml::link(Yii::t('admin','Home'),Yii::app()->homeUrl),
		));?>
			
      <?php echo $content; ?>

			
		<footer>
			<p><?php echo Yii::powered(); ?></p>
			<p><a target="_blank" href="http://twitter.github.com/bootstrap/">Bootstrap</a> designed and built with all the love in the world <a target="_blank" href="http://twitter.com/twitter">@twitter</a> by <a target="_blank" href="http://twitter.com/mdo">@mdo</a> and <a target="_blank" href="http://twitter.com/fat">@fat</a>.</p>
			<p>Icons from <a href="http://glyphicons.com" target="_blank">Glyphicons</a>, <a href="http://p.yusukekamiyamane.com" target="_blank">Yusuke Kamiyamane</a>.</p>
		</footer>
			
    </div> <!-- /container -->


  </body>
</html>