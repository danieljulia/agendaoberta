<?php


$writer = new XMLWriter();
// Output directly to the user 
$writer->openURI('php://output');
$writer->startDocument('1.0');
$writer->setIndent(4);


$writer->startElement('response'); 
$writer->startElement('categories'); 
foreach ($categories as $r) {
	
	//$writer->writeElement('category', $r->name);
	
	$writer->startElement('category'); 
	$writer->writeAttribute('id',$r->id);
	$writer->text($r->name);
	$writer->endElement();
}
$writer->endElement(); //categories
$writer->endElement(); //response

$writer->endDocument();
$writer->flush(); 
