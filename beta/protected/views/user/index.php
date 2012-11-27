<?php
$label = User::label();
$gender = 0;
$this->breadcrumbs=array(
	$label=>array('index'),
);
$this->pageTitle = $label.' - '.$this->pageTitle;
?>

<div class="page-header">
<h2><?php echo $label ?></h2>
</div>

<!-- <a class="btn" href="<?php echo $this->createUrl('create') ?>">
<i class="icon-plus"></i> <?php echo Yii::t('admin','Add new',$gender) ?></a> -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',			
		array(
			'name'=>'username',
			'type'=>'raw',
			'value'=>'CHtml::link(CHtml::encode($data->username), array(\'view\', \'id\'=>$data->id))',
		),
		array(
			'name'=>'fullname',
			'type'=>'raw',
			'value'=>'CHtml::link(CHtml::encode($data->fullname), array(\'view\', \'id\'=>$data->id))',
		),
		array(
			'name'=>'source',			
			'filter'=>array('tw'=>'tw','fb'=>'fb'),
			'value'=>'$data->fb_id?"fb": ($data->tw_id?"tw":"")',
			'htmlOptions'=>array('class'=>'tac'),
		),
		array(
			'name'=>'num_favorites',
			'htmlOptions'=>array('class'=>'tac'),
		),
		array(
			'name'=>'num_friends',
			'htmlOptions'=>array('class'=>'tac'),
		),
		array(
			'name'=>'eventCount',
			'htmlOptions'=>array('class'=>'tac'),
			'filter'=>false,
		),
		array(
			'name'=>'created',
			'htmlOptions'=>array('class'=>'tac'),
		),
		array(
			'name'=>'last_login',
			'htmlOptions'=>array('class'=>'tac'),
		),		
		array(
			'class'=>'CButtonColumn',
			'template' => '{view}',
		),
	),
)); ?>


<!-- <a class="btn" href="<?php echo $this->createUrl('create') ?>">
<i class="icon-plus"></i> <?php echo Yii::t('admin','Add new',$gender) ?></a> -->