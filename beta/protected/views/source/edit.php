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
$cs = Yii::app()->getClientScript();
/* @var $cs CClientScript */

if (!$model->isNewRecord) {
$js = '
	$("#save, #save-return").click(function(){
		var f = $("#source-form").get(0);
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

<?php if (!$model->isNewRecord && $model->feed_type): ?>
<ul class="nav nav-pills">
<li class="active">
<a href="<?php echo $this->createUrl('update', array('id'=>$model->id)) ?>">Edició</a>
</li>
<li><a href="<?php echo $this->createUrl('options', array('id'=>$model->id)) ?>">Opcions / Test</a></li>
</ul>
<?php endif ?>




<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'source-form',
	'enableAjaxValidation'=>false,
	//'htmlOptions'=>array('class'=>'form-horizontal'),
)); ?>

<p class="note"><?php echo Yii::t('admin','Fields with {*} are required.',array('{*}'=>'<span class="required">*</span>')) ?></p>

<?php echo B::errorSummary($model) ?>

<div class="row">
<div class="span6">

<div class="row"><div class="span3">
<?php echo B::startGroup($model,'name'); ?>
<?php echo $form->textField($model,'name',array('class'=>'span3')) ?>
<?php echo B::endGroup(); ?>
</div><div class="span3">
<?php echo B::startGroup($model,'slug'); ?>
<?php echo $form->textField($model,'slug',array('class'=>'span3')) ?>
<p class="help-block">(en blanc per generar-lo automàticament)</p>
<?php echo B::endGroup(); ?>		
</div></div>

<?php echo B::startGroup($model,'feed'); ?>
<?php echo $form->textField($model,'feed',array('class'=>'span6')) ?>
<?php echo B::endGroup(); ?>

<?php echo B::startGroup($model,'feed_type'); ?>
<?php echo $form->dropDownList($model,'feed_type',array(''=>'')+Source::feedTypes()) ?>
<?php echo B::endGroup(); ?>
	
<?php echo B::startGroup($model,'parser'); ?>
<?php echo $form->textField($model,'parser') ?>
<p class="help-block">Deixeu en blanc per a parser genèric.</p>
<?php echo B::endGroup(); ?>

<?php echo B::startGroup($model,'scrape'); ?>
<?php echo B::radioButtonList($model, 'scrape', array('1'=>'Sí','0'=>'No'), array('labelOptions'=>array('class'=>'inline'))); ?>
<?php echo B::endGroup(); ?>

<?php if (!$model->isNewRecord): ?>
	<?php echo B::startGroup($model,'active'); ?>
	<?php echo B::radioButtonList($model, 'active', array('1'=>'Sí','0'=>'No'), array('labelOptions'=>array('class'=>'inline'))); ?>
	<p class="help-block">Activeu-la un cop hagueu comprovat que funciona bé.</p>
	<?php echo B::endGroup(); ?>
<?php endif ?>

</div>
<div class="span6">

<?php echo B::startGroup($model,'city_id'); ?>
<?php echo $form->dropDownList($model,'city_id',array(''=>'')+City::listData()) ?>
<?php echo B::endGroup(); ?>
	
<?php echo B::startGroup($model,'city2events'); ?>
<?php echo B::radioButtonList($model, 'city2events', array('1'=>'Sí','0'=>'No'), array('labelOptions'=>array('class'=>'inline'))); ?>
<p class="help-block">Activeu aquesta opció si la font només proporciona esdeveniments de la seva ciutat.</p>
<?php echo B::endGroup(); ?>


<?php echo B::startGroup($model,'categories'); ?>
<?php echo $form->dropDownList($model, 'categories', Category::listData(), array('multiple'=>'multiple', 'size'=>6))?>
<?php echo B::endGroup(); ?>


<?php echo B::startGroup($model, 'description') ?>
<?php echo $form->textArea($model,'description',array('class'=>'input-xlarge')) ?>
<?php echo B::endGroup() ?>

</div>	
</div>

<div class="form-actions">
<a href="<?php echo $this->createUrl('index')?>" class="btn"><?php echo Yii::t('admin','Cancel') ?></a>

<?php if (!$model->isNewRecord) echo B::submit('Guardar i tornar',array('id'=>'save-return'));  ?>

<?php echo B::submit($model->isNewRecord?Yii::t('admin','Create'):Yii::t('admin','Save')); ?>
<input type="hidden" name="return" value=""/>
</div>


<?php $this->endWidget(); ?>

