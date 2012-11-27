<?php

$callback = !empty($_GET['callback'])?$_GET['callback']:false;

if ($callback) {
	echo $callback.'(';
}

$a = array();
foreach ($categories as $r) {
	$a[] = array('id'=>$r->id,'name'=>$r->name);
}

echo json_encode($a);

if ($callback) {
	echo ');';
}