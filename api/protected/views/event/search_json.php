<?php


$callback = !empty($_GET['callback'])?$_GET['callback']:false;

if ($callback) {
	echo $callback.'(';
}

$a = array();

$a['next'] = $next;
$a['max_id'] = (string)$maxId;
$a['completed_in'] = round(Yii::getLogger()->getExecutionTime(),3);

$ae = array();
foreach ($events as $r) {
	$e = array(
		'id'=>$r->id,
		'summary'=>$r->summary,
		'description'=>$r->description,
		'location'=>$r->location,
		'url'=>$r->url,
		'start'=>$r->start,
		'end'=>$r->end,
		'img'=>$r->imgUrl,
		'lat'=>$r->geo_lat,
		'lng'=>$r->geo_lng,
	);
		
	
	$ae[] = $e;
}

$a['events'] = $ae;

echo json_encode($a);

if ($callback) {
	echo ');';
}