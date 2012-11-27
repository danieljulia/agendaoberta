<?php
$label = Category::label();
$gender = 1;
$this->breadcrumbs=array(
	$label=>array('index'),
);
$this->pageTitle = $label.' - '.$this->pageTitle;
?>

<div class="page-header">
<h2><?php echo $label ?></h2>
</div>

<a class="btn" href="<?php echo $this->createUrl('create') ?>">
<i class="icon-plus"></i> <?php echo Yii::t('admin','Add new',$gender) ?></a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'category-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		array(
			'name'=>'name',
			'type'=>'raw',
			'value'=>'CHtml::link(CHtml::encode($data->name), array(\'update\', \'id\'=>$data->id))',
		),
		'slug',
		array(
			'name'=>'eventCount',
			'filter'=>false,
			'htmlOptions'=>array('class'=>'tac'),
		),
		array(
			'class'=>'CButtonColumn',
			'template' => '{update} {delete}',
		),
	),
)); ?>


<a class="btn" href="<?php echo $this->createUrl('create') ?>">
<i class="icon-plus"></i> <?php echo Yii::t('admin','Add new',$gender) ?></a>