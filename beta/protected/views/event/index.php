<?php
$label = Event::label();
$gender = 0;
$this->breadcrumbs=array(
	$label=>array('index'),
);
$this->pageTitle = $label.' - '.$this->pageTitle;


$baseUrl = Yii::app()->request->baseUrl;

$cs = Yii::app()->getClientScript();
/* @var $cs CClientScript */

$cs->registerCssFile($baseUrl . '/admin/css/colorbox/colorbox.css');
$cs->registerScriptFile($baseUrl . '/admin/js/jquery.colorbox-min.js', CClientScript::POS_HEAD);

$js = '
  //$(".view-detail").colorbox({iframe:true, width:"80%", height:"80%"});
	$("body").on("click", "a.view-detail", function(e){
			e.preventDefault();
			$.colorbox({href:$(this).attr("href"),iframe:true, width:"80%", height:"80%"});
	});
';
$cs->registerScript('colorbox.init', $js, CClientScript::POS_READY);

$cs->registerScriptFile($baseUrl.'/admin/js/jquery.jeditable.mini.js');
$js = '	
	$("body").on("click", "a.promoted", function(e){
		e.preventDefault();
		var id = this.id.split("-").pop(),
				$a = $(this),
				val = $a.hasClass("on")?0:1;
		$.post("'.$this->createUrl('event/update_fields').'/"+id, {field:"promoted",value:val}, function(data) {			
			$a.removeClass("on off").addClass(data=="1"?"on":"off");
			//$.fn.yiiGridView.update("event-grid");
		});
	});
';
$cs->registerScript('edit_promo',$js, CClientScript::POS_READY);


function helper_format_categories($rs) {
	$a = array();
	foreach ($rs as $r) {
		$a[] = $r->name;
	}
	return implode(', ',$a);	
}

function helper_format_promoted($id,$promoted) {	
	return '<a id="promoted-'.$id.'" href="#" class="promoted '.($promoted?'on':'off').'" title="toggle"></a>';
}

?>

<div class="page-header">
<h2><?php echo $label ?></h2>
</div>

<a class="btn" href="<?php echo $this->createUrl('create') ?>">
<i class="icon-plus"></i> <?php echo Yii::t('admin','Add new',$gender) ?></a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'event-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',			
		array(
			'name'=>'summary',
			'type'=>'raw',
			'value'=>'CHtml::link(CHtml::encode($data->summary), array("view", "id"=>$data->id, "ajax"=>1), array("class"=>"view-detail"))',
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
			'name'=>'category_id',
			'value'=>'helper_format_categories($data->categories)',
			'filter'=>Category::listData(),
		),
		array(
			'name'=>'city_id',
			'value'=>'$data->city_id?$data->city->name:null',
			'filter'=>City::listData(),
		),
		array(
			'name'=>'source_id',
			'value'=>'CHtml::link(CHtml::encode($data->source->name), array("source/update", "id"=>$data->source_id), array("target"=>"_blank"))',
			'type'=>'raw',
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
			'name'=>'promoted',
			'type'=>'raw',
			'value'=>'helper_format_promoted($data->id,$data->promoted)',
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