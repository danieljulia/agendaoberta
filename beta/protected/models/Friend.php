<?php

/**
 * This is the model class for table "t_friend".
 * 
 */
class Friend extends CActiveRecord
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
		return '{{friend}}';
	}
	
	public function scopes() {
		$t = $this->getTableAlias();
		return array(
			'ordered' => array(
				'order'=>"$t.created DESC",
			),
			
		);
	}	

	public function relations() {
		return array(
			'user' => array(self::BELONGS_TO, 'User', 'user_id', 'joinType'=>'INNER JOIN'),
			'friend' => array(self::BELONGS_TO, 'User', 'friend_id', 'joinType'=>'INNER JOIN'),
		);
	}
	

}