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

$df = Yii::app()->getDateFormatter();
?>

<div class="page-header">
<h2><?php echo $label,': <em>'.CHtml::encode($name).'</em>' ?></h2>
</div>


<ul class="nav nav-pills">
<li class="active"><a href="<?php echo $this->createUrl('view', array('id'=>$model->id)) ?>">Dades</a></li>
<li><a href="<?php echo $this->createUrl('events', array('id'=>$model->id)) ?>">Esdeveniments (<?php echo $model->eventCount ?>)</a></li>
</ul>

<div class="row">
<div class="span6">
<?php

$this->widget('zii.widgets.CDetailView', array(
    'data'=>$model,
    'attributes'=>array(
				/* SAMPLE
				array(						
            'name'=>'city.name',
            'type'=>'raw',
            'value'=>CHtml::link(CHtml::encode($model->city->name),array('city/view','id'=>$model->city->id)),
        ),
				*/
				'id',
				array(
          'name'=>'username',
				),
				array(
          'name'=>'fullname',
				),
				array(
          'name'=>'email',
				),
				array(
          'name'=>'newsletter',
					'value'=>$model->newsletter?'Sí':'No',
				),
				array(
					'name'=>'created',
					'value'=>$df->format('d MMMM yyyy HH:mm:ss',$model->created),
				),
				array(
					'name'=>'last_login',
					'value'=>$model->last_login?$df->format('d MMMM yyyy HH:mm:ss',$model->last_login):'-',
				),
			),
	));
?>
</div>
<div class="span6">
<?php
$this->widget('zii.widgets.CDetailView', array(
    'data'=>$model,
    'attributes'=>array(
				array(
					'name'=>'fb_id',	
				),
				array(
					'name'=>'tw_id',
				),				
				array(
					'name'=>'image_url',
					'type'=>'raw',
					'value'=>$model->image_url?'<a href="'.$model->image_url.'" target="_blank"><img height="100" src="'.$model->image_url.'" /></a>':''
				),				
				array(
          'name'=>'url',
					'type'=>'raw',
					'value'=>$model->url?'<a href="'.$model->url.'" target="_blank">'.$model->url.'</a>':'',
        ),

				array(
          'name'=>'description',
				),
    ),
));

?>
</div>
</div>
<div class="row">
	<div class="span12"><hr class="separator"/></div>
</div>
<div class="row">
<div class="span6">
<h4>Favorits</h4>

<?php

$this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$favorites,
    'itemView'=>'_fav',
    'enableSorting'=>false,
));

?>
</div>
<div class="span6">

<h4>Amics</h4>


<?php

$this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$friends,
    'itemView'=>'_friend',
    'enableSorting'=>false,
));

?>

</div>
</div>