<?php
$label = Source::label();
$name = $model->displayName;
$actionLabel = $model->isNewRecord ? Yii::t('admin','Create') : Yii::t('admin','Update').': '.$name;
$this->breadcrumbs=array(
	$label=>array('index'),
	$actionLabel =>
	( $model->isNewRecord ? array('create'):array('update','id'=>$model->id) ),
);
$this->pageTitle = $label.' - '.$actionLabel.' - '.$this->pageTitle;


$baseUrl = Yii::app()->request->baseUrl;

$cs = Yii::app()->getClientScript();
/* @var $cs CClientScript */



$cs->registerCssFile($baseUrl . '/admin/css/colorbox/colorbox.css');
$cs->registerScriptFile($baseUrl . '/admin/js/jquery.colorbox-min.js', CClientScript::POS_HEAD);

$js = '
  //$(".cbif").colorbox({iframe:true, width:"80%", height:"80%"});
	
	$(".cbif").click(function () {
		$.colorbox({
			open: true,
			width: "80%",
			height: "80%",
			href: $(this).attr("href"),
			data:$("#source-form").serialize(),			
		});    
		return false;
  });

';
$cs->registerScript('colorbox.init', $js, CClientScript::POS_READY);
?>

<div class="page-header">
<h2><?php echo $label,': ',$model->isNewRecord ? Yii::t('admin','Create') : Yii::t('admin','Update').' - <em>'.CHtml::encode($name).'</em>' ?></h2>
</div>

<?php if (Yii::app()->user->hasFlash('success')) echo B::alert('Opcions guardades correctament.','success')?>

<?php if (!$model->isNewRecord): ?>
<ul class="nav nav-pills">
<li>
<a href="<?php echo $this->createUrl('update', array('id'=>$model->id)) ?>">Edició</a>
</li>
<li class="active"><a href="<?php echo $this->createUrl('options', array('id'=>$model->id)) ?>">Opcions / Test</a></li>
</ul>
<?php endif ?>




<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'source-form',
	'enableAjaxValidation'=>false,
)); ?>

<?php echo B::startGroup($model,'xpath'); ?>
<?php echo $form->textArea($model,'xpath',array('class'=>'input-xlarge')) ?>
<?php echo B::endGroup(); ?>
	
<div class="form-actions">
<a class="btn cbif" href="<?php echo $this->createUrl('test',array('id'=>$model->id))?>">Test</a>

<a class="btn cbif" href="<?php echo $this->createUrl('test',array('id'=>$model->id,'raw'=>true))?>">Test (Raw)</a>

<a class="btn cbif" target="parser" href="<?php echo $this->createUrl('cron/parse/',array('source_id'=>$model->id))?>">Parseja</a>

<?php echo B::submit(Yii::t('admin','Save Options')); ?>

</div>

<?php $this->endWidget(); ?>

</div><!-- form -->