<?php

/**
 * This is the model class for table "{{city}}".
 */
class City extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return City the static model class
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
		return '{{city}}';
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
			array('lat, lng', 'numerical'),
			array('lat, lng', 'default', 'value'=>null),
			array('id, name, slug, lat, lng', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			//sample relations
			'sourceCount' => array(self::STAT, 'Source', 'city_id'),
			'sources' => array(self::HAS_MANY, 'Source', 'city_id'),
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
		/*if ($this->isNewRecord) {
			$this->created = $this->updated = date('Y-m-d H:i:s');
		} else {
			$this->updated = date('Y-m-d H:i:s');
		}*/
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
			'lat' => Yii::t('db', 'Lat'),
			'lng' => Yii::t('db', 'Lng'),
			'sourceCount'=>Yii::t('db', 'Source Count'),
		);
	}
	
	public static function label() {
		return Yii::t('db', 'Cities');
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
		$criteria->compare('t.lat', $this->lat, false);
		$criteria->compare('t.lng', $this->lng, false);
	

		$criteria->with = array('sourceCount');

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