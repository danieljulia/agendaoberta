<?php

class EventController extends AdminController
{


	public function actionIndex()
	{
		$model=new Event('search');

		// Comment the following lines if using RememberFiltersBehavior
		/*
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Event']))
			$model->attributes=$_GET['Event'];
		*/

		$this->render('index',array(
			'model'=>$model,
		));
	}

	
	public function actionView($id)
	{
		
		if (Yii::app()->request->isAjaxRequest || isset($_GET['ajax'])) {
			$this->layout = '//layouts/dialog';
		}
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}
	

	public function actionCreate()
	{
		$model=new Event('create');
		
		if(isset($_POST['Event']))
		{
			$model->attributes=$_POST['Event'];
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

		if(isset($_POST['Event']))
		{
			$model->attributes=$_POST['Event'];
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
	
	public function actionUpdate_fields($id)
	{
		$model=$this->loadModel($id);
				
		if(isset($_POST['field']) && isset($_POST['value']) && $model->hasAttribute($_POST['field']))
		{
			$model->scenario = 'updateField';			
			$field = $_POST['field'];
			$oldValue = $model->$field;
			$model->$field=$_POST['value'];
			if($model->save()) {
				echo $model->$field;
			} else {
				echo $oldValue;
			}						
		}

	}	
	
	public function actionRestore_favorites($id) {
		$model=$this->loadModel($id);		
		$model->recalculateNumFavorites();
		$model->refresh();
		echo $model->num_favorites;		
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
		$model=Event::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='event-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	
	public function actionSet_default_category() {
		$rs = Event::model()->with('categories')->findAll();
		foreach ($rs as $r) {
			$cat = $r->categories ? $r->categories[0]->id : null;
			$r->saveAttributes(array('default_category_id'=>$cat));
		}
	}
	
	public function actionSet_photo_local() {
		$rs = Event::model()->findAll(array('condition'=>"photo != '' AND photo_local IS NULL"));
		$thumbs = Yii::app()->params['thumbs'];
		foreach ($rs as $r) {
			$ext = Utils::getExtension($r->photo);
			if($ext=="") $ext="jpg";
			$photo=$thumbs."thumbs/".$this->uid.".".$ext;
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$photo)){
				$r->saveAttributes(array('photo_local'=>$this->uid.".".$ext));
			}			
		}
	}	
}
