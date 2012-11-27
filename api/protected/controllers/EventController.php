<?php

class EventController extends Controller
{
	private $analytics;

	public function actionIndex()
	{
		$this->layout='//layouts/main';
		$this->render('index');
	}

	
	public function actionSearch() {
		
		//track event in analytics
		$this->analytics=new Analytics(Yii::app()->params['ga_code'],Yii::app()->params['ga_domain']);
		$this->analytics->trackEvent("api","search");
		   
		$s = new EventSearch();
		$s->attributes = $_GET;
		
		//per a la versió web volem mostrar les categories
		if (!$this->detectFormat()) $s->withCat = true;		
		
		if (!$s->validate()) {
			$error = current($s->getErrors());
			if (is_array($error)) $error = current($error);
			$this->sendResponse('//error/index',array('message'=>$error),400);
		}
		
		$rs = Event::model()->eventSearch($s)->findAll();
		
		$next = false;
		if (count($rs) > $s->limit) {
			$rs = array_slice($rs,0,$s->limit);						
			$next = $this->getNextUrl();
		}
		
		$maxId = null;
		foreach ($rs as $r) {
			if (!$maxId || $r->id>$maxId) $maxId = $r->id; 
		}
				
		$this->sendResponse('search',array(
			'events' => $rs,
			's'=>$s,
			'next'=>$next,
			'maxId'=>$maxId,			
		));
	}
	
	
	
	function getNextUrl() {
		$params = $_GET;
		if (isset($params['pag'])) {
			$params['pag'] = 1+(int)$params['pag'];
		} else {
			$params['pag'] = 2;
		}		
		return $this->createUrl('',$params);		
	}
	

}
