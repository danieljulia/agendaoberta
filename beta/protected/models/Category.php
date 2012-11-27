<?php

/**
 * This is the model class for table "{{category}}".
 */
class Category extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Category the static model class
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
		return '{{category}}';
	}

	public function scopes() {
		$t = $this->getTableAlias();
		return array(
			'ordered'=>array(
				'order'=>"$t.name",
			),
		);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('name,slug', 'length', 'max'=>128),
			array('slug','SlugifyValidator','fromAttribute'=>'name'),
			array('slug','unique','allowEmpty'=>false),
			array('id, name, slug', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'events' => array(self::MANY_MANY, 'Event', 'ao_event_2_category(category_id,event_id)'),
			'sources'=>array(self::MANY_MANY,'Source','ao_source_2_category(category_id,source_id)'),
			'eventCount' => array(self::STAT, 'Event', 'ao_event_2_category(category_id,event_id)'),
			'sourceCount'=>array(self::STAT,'Source','ao_source_2_category(category_id,source_id)'),
		);
	}

	public function behaviors() {
		return array(
			/*
			'bRemember' => array(
				'class' => 'application.components.RememberFiltersBehavior',
			),
			*/
		);
	}

	protected function beforeSave() {
		/*
		if ($this->isNewRecord) {
			$this->created = $this->updated = date('Y-m-d H:i:s');
		} else {
			$this->updated = date('Y-m-d H:i:s');
		}
		*/
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
			'name' => Yii::t('db', 'Name'),
			'eventCount' => Yii::t('db', 'Event Count'),
		);
	}
	
	public static function label() {
		return Yii::t('db', 'Categories');
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
		$criteria->compare('t.slug', $this->slug, true);
		$criteria->with = array('eventCount');

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
	
	public static function listData() {
		$a = array();
		$rs = self::model()->ordered()->findAll(array('select'=>'id,name'));		
		foreach ($rs as $r) {
			$a[$r->id] = $r->name;
		}
		return $a;
	}
	
	public function slugifyAll() {
		$db = $this->getDbConnection();
		$rs = $db->createCommand("SELECT id,name FROM ".$this->tableName()." WHERE slug=''")->queryAll(true);
		$slugify = $db->createCommand("UPDATE ".$this->tableName()." SET slug = :slug WHERE id = :id");
		$n = 0;
		foreach ($rs as $r) {
			try {
				$n += $slugify->execute(array(':slug'=>Utils::slugify($r['name']),':id'=>$r['id']));
			} catch(Exception $e) {
				
			}
		}
		return $n;
	}	
}