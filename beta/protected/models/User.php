<?php

/**
 * This is the model class for table "{{user}}".
 */
class User extends CActiveRecord
{

	public $source; //per a cerques
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Source the static model class
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
		return '{{user}}';
	}


	public function scopes() {           
		$t = $this->getTableAlias();
		return array(
			'ordered'=>array(
				'order'=>"$t.created DESC",
			),
		);
	}


	/* named scopes?
	public function recently($limit=10) {
		$t = $this->getTableAlias();
		$this->getDbCriteria()->mergeWith(array(
			'order'=>"$t.created DESC",
			'limit'=>$limit,
		));
		return $this;
	}
	*/

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			
			array('id, username, fullname, email, source, num_favorites, num_friends, created, last_login', 'safe', 'on'=>'search'),
		);
	}
	

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'eventCount' => array(self::STAT, 'UserEvent', 'user_id'),
			'userEvents' => array(self::HAS_MANY, 'UserEvent', 'user_id'),			
		);
	}

	public function behaviors() {
		return array(
			'bRemember' => array(
				'class' => 'application.components.RememberFiltersBehavior',
			),
		);
	}

	protected function beforeSave() {
		return parent::beforeSave();
	}

	protected function afterSave() {
		parent::afterSave();
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('db', 'ID'),
			'username' => Yii::t('db', 'Username'),
			'fullname' => Yii::t('db', 'Full Name'),
			'fb_id' => Yii::t('db','Facebook ID'),
			'tw_id' => Yii::t('db','Twitter ID'),
			'email' => Yii::t('db', 'Email'),
			'description' => Yii::t('db', 'Description'),			
			'created' => Yii::t('db', 'Created'),
			'updated' => Yii::t('db', 'Updated'),
			'last_login' => Yii::t('db', 'Last Login'),
			'num_favorites'=> Yii::t('db', 'Num. favorites'),
			'num_friends'=> Yii::t('db', 'Num. friends'),
			'eventCount'=> Yii::t('db', 'Events'),
			'source'=> Yii::t('db', 'Source'),
		);
	}
	
	public static function label() {
		return Yii::t('db', 'Users');
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{

		$criteria=new CDbCriteria;

		$criteria->compare('t.id', $this->id, true);
		$criteria->compare('t.username', $this->username, true);
		$criteria->compare('t.fullname', $this->fullname, true);
		$criteria->compare('t.email', $this->email, false);
		
		if ($this->source == 'fb') {
			$criteria->addCondition('t.fb_id IS NOT NULL');
		} elseif ($this->source == 'tw') {
			$criteria->addCondition('t.tw_id IS NOT NULL');
		}
		
		$criteria->compare('t.num_favorites', $this->num_favorites, false);
		$criteria->compare('t.num_friends', $this->num_friends, false);
		$criteria->compare('t.created', $this->created, false);

		$criteria->with = array('eventCount');

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array(
				'pageSize' => 50,
			),
			'sort' => array('defaultOrder' => 't.created DESC'),
		));
	}

	public function getDisplayName() {
		return $this->username;
	}

}