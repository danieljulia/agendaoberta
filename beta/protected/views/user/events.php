<?php
$label = User::label();
$name = $model->displayName;
$actionLabel = Yii::t('admin','View').': '.$name;
$this->breadcrumbs=array(
	$label=>array('index'),
	$actionLabel =>
	( $model->isNewRecord ? array('create'):array('update','id'=>$model->id) ),
);
$this->pageTitle = $label.' - '.$actionLabel.' - '.$this->pageTitle;
$cs = Yii::app()->getClientScript();
/* @var $cs CClientScript */

$baseUrl = Yii::app()->baseUrl;


$df = Yii::app()->getDateFormatter();
?>

<div class="page-header">
<h2><?php echo $label,': <em>'.CHtml::encode($name).'</em>' ?>: esdeveniments</h2>
</div>


<ul class="nav nav-pills">
<li><a href="<?php echo $this->createUrl('view', array('id'=>$model->id)) ?>">Dades</a></li>
<li class="active"><a href="<?php echo $this->createUrl('events', array('id'=>$model->id)) ?>">Esdeveniments (<?php echo $model->eventCount ?>)</a></li>
</ul>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'user-events-grid',
	'dataProvider'=>$events->search(),
	'filter'=>$events,
	'columns'=>array(
		'id',
		array(
			'name'=>'summary',
			'type'=>'raw',
			'value'=>'CHtml::link(CHtml::encode($data->summary), array("event/update", "id"=>$data->id), array("target"=>"_blank"))',
		),
		array(
			'name'=>'startdate',
			'htmlOptions'=>array('class'=>'tac'),
		),
		array(
			'name'=>'enddate',
			'htmlOptions'=>array('class'=>'tac'),
		),
		array(
			'name'=>'city_id',
			'value'=>'$data->city_id?$data->city->name:null',
			'filter'=>City::listData(),
		),
		array(
			'name'=>'location',
		),			
		array(
			'name'=>'created',
		),
		array(
			'name'=>'num_favorites',
			'htmlOptions'=>array('class'=>'tac'),
		),
		array(
			'name'=>'num_flagged',
			'htmlOptions'=>array('class'=>'tac'),
		),
		array(
			'class'=>'CButtonColumn',
			'template' => '{delete}',
			'deleteButtonUrl'=>'Yii::app()->controller->createUrl("event/delete",array("id"=>$data->id))'
		),			
	),
)); ?>
