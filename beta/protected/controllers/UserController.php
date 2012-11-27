<?php

class UserController extends AdminController
{


	public function actionIndex()
	{
		$model=new User('search');

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
		
		$model = $this->loadModel($id);
		
		$favorites = new CActiveDataProvider('Favorite', array(
			'criteria'=>array(
				'condition'=>'t.user_id = :user_id',
				'params'=>array(':user_id'=>$id),
				'order'=>'t.created DESC',
				'with'=>array('event'),
			),
			'pagination'=>array(
				'pageSize'=>10,
			),
		));
		
		$friends = new CActiveDataProvider('Friend', array(
			'criteria'=>array(
				'condition'=>'t.user_id = :user_id',
				'params'=>array(':user_id'=>$id),
				'order'=>'t.created DESC',
				'with'=>array('friend'),
			),
			'pagination'=>array(
				'pageSize'=>10,
			),
		));
		
		
		$this->render('view',compact('model','favorites','friends'));
	}
	
	public function actionEvents($id)
	{
		
		if (Yii::app()->request->isAjaxRequest || isset($_GET['ajax'])) {
			$this->layout = '//layouts/dialog';
		}
		
		$events = new Event('search');
		$events->unsetAttributes();  // clear any default values
		if(isset($_GET['Event']))	$events->attributes=$_GET['Event'];
		$events->user_id = $id;
						
		$this->render('events',array(
			'model'=>$this->loadModel($id),
			'events'=>$events,
		));
	}
	
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=User::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	
	
}
