<?php

$writer = new XMLWriter();
// Output directly to the user 
$writer->openURI('php://output');
$writer->startDocument('1.0');
$writer->setIndent(4);

$writer->startElement('response'); 
$writer->writeElement('next', $next);
$writer->writeElement('max_id', $maxId);
$writer->writeElement('completed_in', round(Yii::getLogger()->getExecutionTime(),3));

$writer->startElement('events'); 
foreach ($events as $r) {
		
	
	$writer->startElement('event'); 
	$writer->writeAttribute('id',$r->id);
	$writer->writeElement('start', $r->start);
	$writer->writeElement('end', $r->end);
	$writer->writeElement('summary', $r->summary);
	$writer->writeElement('description', $r->description);
	$writer->writeElement('img', $r->imgUrl);	
	$writer->writeElement('url', $r->url);
	if ($r->geo_lat && $r->geo_lng) {
		$writer->startElement('location'); 
		$writer->writeAttribute('lat',$r->geo_lat);
		$writer->writeAttribute('lng',$r->geo_lng);
		$writer->text($r->location);
		$writer->endElement();
	} else {
		$writer->writeElement('location', $r->location);
	}

	
	
	
	$writer->endElement();//event
}
$writer->endElement(); //events
$writer->endElement(); //response

$writer->endDocument();
$writer->flush(); 

