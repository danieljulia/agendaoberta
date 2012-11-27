<?php 
	$this->layout = '//layouts/main';
	
$this->pageTitle=Yii::app()->name . ' - Error';

?>

<h2>Error</h2>

<div class="alert alert-error">
<?php echo CHtml::encode($message); ?>
</div>