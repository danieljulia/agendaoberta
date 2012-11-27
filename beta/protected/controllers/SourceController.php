<?php

class SourceController extends AdminController
{


	public function actionIndex()
	{
		$model=new Source('search');

		// Comment the following lines if using RememberFiltersBehavior
		/*
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Source']))
			$model->attributes=$_GET['Source'];
		*/

		$this->render('index',array(
			'model'=>$model,
		));
	}

	/*
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}
	*/

	public function actionCreate()
	{
		$model=new Source('create');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Source']))
		{
			$model->attributes=$_POST['Source'];
			if($model->save()) {
				$model->saveRelated('categories');
				Yii::app()->user->setFlash('success',true);
				$this->redirect(array('update','id'=>$model->id));
			}
		}

		$this->render('edit',array(
			'model'=>$model,
		));
	}
        
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Source']))
		{
			$model->attributes=$_POST['Source'];
			if($model->save()) {
				$model->saveRelated('categories');
				if (!empty($_POST['return']))	$this->redirect(array('index'));
				else {
					Yii::app()->user->setFlash('success',true);
					$this->refresh();
				}
			}
		}

		$this->render('edit',array(
			'model'=>$model,
		));
	}
	
	public function actionOptions($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Source']))
		{
			$model->attributes=$_POST['Source'];
			if($model->save()) {
				Yii::app()->user->setFlash('success',true);	
				$this->refresh();
			}
		}

		$this->render('options',array(
			'model'=>$model,
		));
	}
	
	public function actionTest($id,$raw=null)
	{
		$model=$this->loadModel($id);

		if (isset($_POST['Source'])) {
			$model->attributes = $_POST['Source'];
		}
		
		$error = false;
		
		$parser = $model->getParser();		
		if ($parser===null) throw new CHttpException(500,'Parser not found.');
		
		ob_start();
                EventItem::enableCache(false); //desactivar la cache al test
		$items = $parser->parse(3,false); //todo posar a configuració
		ob_end_clean();
		
		if ($raw) {
			var_dump($items);
			return;
		}
		
		//$this->layout = '//layouts/dialog';
		$this->renderPartial('test',array(
			'model'=>$model,
			'error'=>$error,
			'items'=>$items,
		));
	}	

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Source::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='source-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public function actionSlugify() {
		$n = Source::model()->slugifyAll();
		echo 'done: '.$n;
	}	
}
