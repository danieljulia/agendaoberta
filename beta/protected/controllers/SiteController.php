<?php

class SiteController extends AdminController
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		
            $stats=array();
            
            $now=date('Y-m-d');
            $nowtime=time();
            $pastDay=strtotime ( '-1 day' , strtotime ( $now ) ) ; 
            $pastWeek = strtotime ( '-1 week' , strtotime ( $now ) ) ; 
            $pastMonth = strtotime ( '-1 month' , strtotime ( $now ) ) ;
            
            $nextDay=strtotime ( '+1 day' , strtotime ( $now ) ) ; 
            $nextWeek = strtotime ( '+1 week' , strtotime ( $now ) ) ; 
            $nextMonth = strtotime ( '+1 month' , strtotime ( $now ) ) ;

        
            $stats['total events']=Stats::EventsCreated();
            $stats['total created last 24h']=Stats::EventsCreated(date('Y-m-d',$pastDay));
            
             $stats['pending processing']=Stats::EventsNotProcessed();
            
            
            $stats['total events in period']=Stats::EventsCount();
            $stats['total next 24h']=Stats::EventsCount($now,date('Y-m-d',$nextDay));
            $stats['total last 24h']=Stats::EventsCount(date('Y-m-d',$pastDay),$now);
            $stats['total next month']=Stats::EventsCount($now,date('Y-m-d',$nextMonth));
            $stats['total last month']=Stats::EventsCount(date('Y-m-d',$pastMonth),$now);
            
            
            $sources=Stats::SourcesParsedSince(date('Y-m-d',$pastDay));
            
            $stats['sources parsed last 24h']= count($sources);
            
            $source_error=Stats::SourcesWithErrors();
            $events=Stats::EventsLast(50);
            
            
             $this->render('index',array('stats'=>$stats,'sources'=>$sources,'source_error'=>$source_error,'events'=>$events));
             
		
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new AdminLoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['AdminLoginForm']))
		{
			$model->attributes=$_POST['AdminLoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

}