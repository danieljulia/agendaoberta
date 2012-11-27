<?php

/**
 * This is the model class for table "{{tag}}".
 */
class Tag extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Event the static model class
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
		return '{{tag}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(			
			array('id, name', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'events' => array(self::MANY_MANY, 'Event', 'ao_event_2_tag(tag_id,event_id)'),
		);
	}

	public function behaviors() {
		return array(
			'bRemember' => array(
				'class' => 'application.components.RememberFiltersBehavior',
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('db', 'ID'),
			'name' => Yii::t('db', 'Name'),			
		);
	}
	
	public static function label() {
		return Yii::t('db', 'Tags');
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{

		$criteria=new CDbCriteria;

		$criteria->compare('t.id', $this->id, true);
		$criteria->compare('t.name', $this->name, true);
		
		//$criteria->with = array('whatever');

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array(
				'pageSize' => 20,
			),
			'sort' => array('defaultOrder' => 't.name'),
		));
	}

	public function getDisplayName() {
		return $this->name;		
	}
}