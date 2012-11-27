<?php
$label = Event::label();
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

$cs->registerScriptFile($baseUrl.'/admin/js/jquery.jeditable.mini.js');
$js = '	

	$(".editable").editable("'.$this->createUrl('event/update_fields',array('id'=>$model->id)).'", {
		submitdata : function (value,settings){
			return {field:$(this).attr("id")};
		},
		select : true,
		indicator : "Saving...",
		tooltip   : "Click to edit...",	
		submit   : "Ok",
		name : "value",
		width: 40
	});
	
$("#restore_favorites").click(function () {
		$.post("'.$this->createUrl('event/restore_favorites',array('id'=>$model->id)).'", function(data) {			
			$("#num_favorites").text(data);
		}, "json");
		return false;
	});


';
$cs->registerScript('edit_numbers',$js, CClientScript::POS_READY);


$df = Yii::app()->getDateFormatter();
?>

<div class="page-header">
<h2><?php echo $label,': <em>'.CHtml::encode($name).'</em>' ?></h2>
</div>

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
				array(						
           'name'=>'summary',           
        ),
				array(						
          'name'=>'url',
					'type'=>'raw',
					'value'=>$model->url?'<a href="'.$model->url.'" target="_blank">'.$model->url.'</a>':'',
        ),
				array(
					'name'=>'startdate',
					'value'=>$df->format('d MMMM yyyy',$model->startdate).($model->starttime?', '.$model->starttime:''),
				),
				array(
					'name'=>'enddate',
					'value'=>$df->format('d MMMM yyyy',$model->enddate).($model->endtime?', '.$model->endtime:''),
				),	
				array(
					'name'=>'schedule',
					
				),
				array(
					'name'=>'description',
					
				),
				array(
					'name'=>'photo',
					'type'=>'raw',
					'value'=>$model->photo?'<a href="'.$model->photo.'" target="_blank"><img width="150" src="'.$model->photo.'" /></a>':''
				),
				array(
					'name'=>'photo_local',
					'type'=>'raw',
					'value'=>$model->photo_local?'<img width="150" src="'.Yii::app()->params['thumbs'].'/thumbs/'.$model->photo_local.'" />':''
				),				
				array(
					'name'=>'location',
					'type'=>'raw',
					'value'=>$model->location.($model->geo_lat && $model->geo_lng?"<br/>[{$model->geo_lat},{$model->geo_lng}]":''),
				),
				array(
					'name'=>'address',
					
				),
				array(
					'name'=>'source.name',
					'label'=>$model->getAttributeLabel('source_id'),
					'type'=>'raw',
					'value'=>CHtml::link($model->source->name,array('source/update','id'=>$model->source_id)),
					
				),
				array(
					'name'=>'city.name',
					'label'=>$model->getAttributeLabel('city_id'),					
				),
				array(
					'name'=>'created',
					
				),
				array(
					'name'=>'updated',
					
				),
				array(
					'name'=>'num_favorites',
					'type'=>'raw',
					'value'=>'<span id="num_favorites" href="#" class="editable">'.$model->num_favorites.'</span> <a id="restore_favorites" href="#">[recalcular]</a>',
				),
				array(
					'name'=>'num_flagged',
					'type'=>'raw',
					'value'=>'<span id="num_flagged" href="#" class="editable">'.$model->num_flagged.'</span>',
				),									
    ),
));
?>

<?php if ($model->geo_lat && $model->geo_lng): ?>

<div id="map_canvas" style="width:500px;height:300px;margin:20px auto;"></div>


<?php
$script ="

var mymarker=new google.maps.LatLng('".$model->geo_lat."','".$model->geo_lng."');
var mysummary='".CJavaScript::quote($model->summary)."';

function initialize() {
	var latlng = new google.maps.LatLng(41, 2);
	var myOptions = {
		zoom: 15,
		center: latlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP   //MapTypeId.SATELLITE,
	};
	var map = new google.maps.Map(document.getElementById( 'map_canvas'), myOptions);
	//createMarker(map,new google.maps.LatLng(41.1,2.1),'esto es otra prueba');
	if(window.mymarker!=null){    
		createMarker(map,mymarker,mysummary);
		map.setCenter(mymarker);
	}
}

//crea un marker con una burbuja de texto, y una imagen personalizada
function createMarker(map,point, txt) {
	var image = '".Yii::app()->baseUrl."/images/marker.png';
	var marker = new google.maps.Marker({
		position: point,
		map: map,
		icon: image
	});
	var infowindow = new google.maps.InfoWindow({
		content: txt
	});
	google.maps.event.addListener(marker, 'click', function() {
		infowindow.open(map,marker);
	});
	return marker;
}
 
initialize();
";

Yii::app()->clientScript->registerScriptFile('http://maps.google.com/maps/api/js?sensor=true');
Yii::app()->clientScript->registerScript('map', $script,CClientScript::POS_END);

?>

<?php endif ?>