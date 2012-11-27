<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class AdminController extends CController {

	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout = '//layouts/column1';

	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu = array();

	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs = array();

	/**
	 * @return array action filters
	 */
	public function filters() {
		return array(
			//'checkLoginKey', //per si volem evitar que usuaris diferents es connectin amb el mateix login				
			'accessControl', // perform access control for CRUD operations
		);
	}

	
	public function filterCheckLoginKey($filterChain) {
		$user = Yii::app()->user;
		if (!$user->isGuest && $key=$user->getState('admin_key')) {		
			$expectedKey = Yii::app()->db->createCommand("SELECT login_check FROM {{admin}} WHERE id=:id")->queryScalar(array(':id'=>$user->id));
			if ($key!=$expectedKey) {
				$user->logout();
			}
		}
		$filterChain->run();
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules() {
		return array(
				array('allow',
						'actions' => array('login'),
						'users' => array('*'),
				),
				array('allow',
						'controllers' => array('cron'),
						'users' => array('*'),
				),
				array('allow', // allow authenticated users to access all actions
						'users' => array('@'),
				),
				array('deny', // deny all users
						'users' => array('*'),
				),
		);
	}

}