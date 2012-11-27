<?php
$label = Category::label();
$name = $model->displayName;
$actionLabel = $model->isNewRecord ? Yii::t('admin','Create') : Yii::t('admin','Update').': '.$name;
$this->breadcrumbs=array(
	$label=>array('index'),
	$actionLabel =>
	( $model->isNewRecord ? array('create'):array('update','id'=>$model->id) ),
);
$this->pageTitle = $label.' - '.$actionLabel.' - '.$this->pageTitle;
$cs = Yii::app()->getClientScript();
/* @var $cs CClientScript */
?>

<div class="page-header">
<h2><?php echo $label,': ',$model->isNewRecord ? Yii::t('admin','Create') : Yii::t('admin','Update').' - <em>'.CHtml::encode($name).'</em>' ?></h2>
</div>



<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'category-form',
	'enableAjaxValidation'=>false,
)); ?>

<p class="note"><?php echo Yii::t('admin','Fields with {*} are required.',array('{*}'=>'<span class="required">*</span>')) ?></p>

<?php echo B::errorSummary($model) ?>

<?php echo B::startGroup($model,'name'); ?>
<?php echo $form->textField($model,'name') ?>
<?php echo B::endGroup(); ?>

	
<?php echo B::startGroup($model,'slug'); ?>
<?php echo $form->textField($model,'slug') ?>
<p class="help-block">(en blanc per generar-lo automàticament)</p>
<?php echo B::endGroup(); ?>	


<div class="form-actions">
<a href="<?php echo $this->createUrl('index')?>" class="btn"><?php echo Yii::t('admin','Cancel') ?></a>
<?php echo B::submit($model->isNewRecord?Yii::t('admin','Create'):Yii::t('admin','Save')); ?>
</div>

<?php $this->endWidget(); ?>

