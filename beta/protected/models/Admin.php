<?php

/**
 * This is the model class for table "t_admin".
 * 
 */
class Admin extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Test the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{admin}}';
	}
	
	public function hashPassword($p) {
		return md5(strlen($p).$p);
	}
	
}