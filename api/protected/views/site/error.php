<?php

$this->pageTitle=Yii::app()->name . ' - Error';

?>

<div class="page-header">
<h2>Error <?php echo $code; ?></h2>
</div>

<div class="alert alert-error">
<?php echo CHtml::encode($message); ?>
</div>