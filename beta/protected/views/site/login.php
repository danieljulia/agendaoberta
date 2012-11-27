<?php
$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);

$cs = Yii::app()->getClientScript();
$js = 'document.getElementById("AdminLoginForm_username").focus()';
$cs->registerScript('username.focus',$js,CClientScript::POS_END);

?>

<div class="page-header">
<h2><?php echo Yii::t('admin','Login')?></h2>
</div>

<p><?php echo Yii::t('admin','Please fill out the following form with your login credentials:')?></p>

<?php if ($model->hasErrors()) echo B::alert(Yii::t('admin','Incorrect username or password.')); ?>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'admin-login-form',
	'enableClientValidation'=>false,
)); ?>


<?php echo B::startGroup($model,'username'); ?>
<?php echo $form->textField($model,'username'); ?>
<?php echo B::endGroup(); ?>

<?php echo B::startGroup($model,'password'); ?>
<?php echo $form->passwordField($model,'password'); ?>
<?php echo B::endGroup(); ?>		

<?php echo B::startGroup($model,'rememberMe',array('label'=>false)); ?>
<label class="checkbox"><?php echo $form->checkBox($model,'rememberMe',array('uncheckValue'=>null)); ?>
<?php echo $model->getAttributeLabel('rememberMe') ?>
</label>
<?php echo B::endGroup(); ?>		


<div class="form-actions">
<?php echo B::submit(Yii::t('admin','Login')); ?>
</div>

<?php $this->endWidget(); ?>
