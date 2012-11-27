<?php
$label = Source::label();
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
	'id'=>'source-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',			
		array(
			'name'=>'name',
			'type'=>'raw',
			'value'=>'CHtml::link(CHtml::encode($data->name), array(\'update\', \'id\'=>$data->id))',
		),
		array(
			'name'=>'city_id',
			'value'=>'$data->city?$data->city->name:""',
			'filter'=>City::listData(),
		),
		array(
			'name'=>'feed_type',
			'filter'=>Source::feedTypes(),
		),
		array(
			'name'=>'feed',
			'value'=>'Utils::truncate($data->feed,50,true)',
		),
		array(
			'name'=>'eventCount',
			'filter'=>false,
			'htmlOptions'=>array('class'=>'tac'),
		),
		array(
			'name'=>'active',
			'value'=>'$data->active?"Sí":"No"',
			'filter'=>array('1'=>'Sí','0'=>'No'),
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