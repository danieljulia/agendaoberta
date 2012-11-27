<?php
$label = Event::label();
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

if (!$model->isNewRecord) {
$js = '
	$("#save, #save-return").click(function(){
		var f = $("#event-form").get(0);
		f["return"].value = this.id=="save"?"":"1";
		return true;
	});
';
$cs->registerScript("clicks", $js, CClientScript::POS_READY);
}

$js = '	
setTimeout(function() {$(".flash-success").fadeOut();}, 1000);
';
$cs->registerScript("timeout", $js, CClientScript::POS_READY);
?>

<div class="page-header">
<h2><?php echo $label,': ',$model->isNewRecord ? Yii::t('admin','Create') : Yii::t('admin','Update').' - <em>'.CHtml::encode($name).'</em>' ?></h2>
</div>


<?php if (Yii::app()->user->hasFlash('success')) echo B::alert('Les dades han estat guardades correctament.','success')?>


<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'event-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
)); ?>

<p class="note"><?php echo Yii::t('admin','Fields with {*} are required.',array('{*}'=>'<span class="required">*</span>')) ?></p>

<?php echo B::errorSummary($model) ?>

<div class="row">
<div class="span6">

<?php echo B::startGroup($model,'summary'); ?>
<?php echo $form->textField($model,'summary',array('class'=>'span6')) ?>
<?php echo B::endGroup(); ?>


<?php echo B::startGroup($model, 'description') ?>
<?php echo $form->textArea($model,'description',array('class'=>'span6', 'rows'=>10)) ?>
<?php echo B::endGroup() ?>
	
<?php echo B::startGroup($model,'location'); ?>
<?php echo $form->textField($model,'location',array('class'=>'span6')) ?>
<?php echo B::endGroup(); ?>
	
<?php echo B::startGroup($model,'address'); ?>
<?php echo $form->textField($model,'address',array('class'=>'span6')) ?>
<?php echo B::endGroup(); ?>
	
<?php echo B::startGroup($model,'url'); ?>
<?php echo $form->textField($model,'url',array('class'=>'span6')) ?>
<?php echo B::endGroup(); ?>	
	
<div class="row"><div class="span3">
<?php echo B::startGroup($model,'startdate'); ?>
<?php
		$this->widget('zii.widgets.jui.CJuiDatePicker', array(
				'model' => $model,
				'attribute' => 'startdate',
				'options'=>array('dateFormat'=>'yy-mm-dd','firstDay'=>1),
		));
		?>
<?php echo B::endGroup(); ?>
</div><div class="span3">
<?php echo B::startGroup($model,'starttime'); ?>
<?php echo $form->textField($model,'starttime') ?>
<?php echo B::endGroup(); ?>
</div></div>

<div class="row"><div class="span3">
<?php echo B::startGroup($model,'enddate'); ?>
<?php
		$this->widget('zii.widgets.jui.CJuiDatePicker', array(
				'model' => $model,
				'attribute' => 'enddate',
				'options'=>array('dateFormat'=>'yy-mm-dd','firstDay'=>1),
		));
		?>
<?php echo B::endGroup(); ?>
</div><div class="span3">
<?php echo B::startGroup($model,'endtime'); ?>
<?php echo $form->textField($model,'endtime') ?>
<?php echo B::endGroup(); ?>
</div></div>


</div>
<div class="span6">

<?php echo B::startGroup($model,'city_id'); ?>
<?php echo $form->dropDownList($model,'city_id',array(''=>'')+City::listData()) ?>
<?php echo B::endGroup(); ?>
	

<?php echo B::startGroup($model,'categories'); ?>
<?php echo $form->dropDownList($model, 'categories', Category::listData(), array('multiple'=>'multiple', 'size'=>6))?>
<?php echo B::endGroup(); ?>
	
<?php echo B::startGroup($model,'geo_lat'); ?>
<?php echo $form->textField($model,'geo_lat') ?>
<?php echo B::endGroup(); ?>
	
<?php echo B::startGroup($model,'geo_lng'); ?>
<?php echo $form->textField($model,'geo_lng') ?>
<?php echo B::endGroup(); ?>

<?php echo B::startGroup($model,'promoted'); ?>
<?php echo B::radioButtonList($model, 'promoted', array('1'=>'Sí','0'=>'No'), array('labelOptions'=>array('class'=>'inline'))); ?>
<?php echo B::endGroup(); ?>
	
<?php echo B::startGroup($model,'score'); ?>
<?php echo $form->dropDownList($model, 'score', range(0,10))?>
<?php echo B::endGroup(); ?>

	
<?php echo B::startGroup($model,'uploadImg'); ?>
<?php if ($model->photo_local): ?>
<p>
<img src="<?php echo Yii::app()->params['thumbs'].'/thumbs/'.$model->photo_local ?>" />
<label class="checkbox"><?php echo $form->checkBox($model,'removeImg',array('uncheckValue'=>null)); ?>
Eliminar
</label>	
</p>
<?php endif ?>
<?php echo $form->fileField($model,'uploadImg') ?>
<?php echo B::endGroup(); ?>
	

</div>	
</div>

<div class="form-actions">
<a href="<?php echo $this->createUrl('index')?>" class="btn"><?php echo Yii::t('admin','Cancel') ?></a>

<?php if (!$model->isNewRecord) echo B::submit('Guardar i tornar',array('id'=>'save-return'));  ?>

<?php echo B::submit($model->isNewRecord?Yii::t('admin','Create'):Yii::t('admin','Save')); ?>
<input type="hidden" name="return" value=""/>
</div>


<?php $this->endWidget(); ?>

