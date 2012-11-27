<?php

class UserEvent extends CActiveRecord {

	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Event the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{user_event}}';
	}

	
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(								
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'owner' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	
}