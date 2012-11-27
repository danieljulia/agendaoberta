<?php

class CategoryController extends Controller
{


	public function actionIndex()
	{
		$this->layout='//layouts/main';
		$this->render('index');
	}

	
	public function actionList() {
		
		$rs = Category::model()->findAll();
		
		$this->sendResponse('list',array(
			'categories' => $rs,
		));
	}
	

}
