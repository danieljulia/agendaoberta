<?php

/**
 * This is the model class for table "{{source}}".
 */
class Source extends CActiveRecord
{
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
		return '{{source}}';
	}


	public function scopes() {
            $mx= Yii::app()->params['max_errors_per_source'];
            if(!$mx) $mx=1;
            
		$t = $this->getTableAlias();
		return array(
			'active'=>array(
				'condition'=>"$t.active = 1 and $t.error < ".$mx,
			),
			'parsable'=>array(
				'condition'=>"$t.feed_type IS NOT NULL",
			),
      'hanged'=>array(
				'condition'=>"$t.updated = 0 and $t.error < ".$mx,
			),
			'pending'=>array(
				'order'=>"$t.next",
			),
			'updated'=>array(
				'order'=>"$t.updated DESC",
				'condition'=>"$t.active=1",
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
			array('feed, name', 'required', 'on'=>'create,update'),			
			array('feed', 'length', 'max'=>256),
			array('name,slug', 'length', 'max'=>256),
			array('slug','SlugifyValidator','fromAttribute'=>'name'),
			array('slug','unique','allowEmpty'=>false),
			array('xpath', 'length', 'max'=>512),	
			array('parser', 'length', 'max'=>12),			
			array('parser', 'validParser'),
			array('description', 'length', 'max'=>255),
			array('feed_type','in','range'=>array_keys(self::feedTypes()), 'allowEmpty'=>true),
			array('feed','url','defaultScheme'=>'http'),
			array('city_id', 'exist', 'className'=>'City', 'attributeName'=>'id', 'allowEmpty'=>true),
			array('active, scrape, city2events','boolean'),
			array('categories','default','value'=>array()),
			array('city_id,feed_type','default','value'=>null),
			array('id, feed, feed_type, name, description, city_id, active, scrape, created, updated, lang', 'safe', 'on'=>'search'),
		);
	}
	
	public function validParser($attribute,$params) {
		if (!$this->$attribute) return;
		
		$className = $this->$attribute."Parser";
		$filePath = Yii::getPathOfAlias("application.components.parsers");
		if (!file_exists($filePath.DIRECTORY_SEPARATOR.$className.'.php')) {
			$this->addError($attribute, "No s'ha pogut trobat el component $className.");
		}
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			//sample relations
			'city'=>array(self::BELONGS_TO, 'City', 'city_id'),
			'eventCount' => array(self::STAT, 'Event', 'source_id'),
			'events' => array(self::HAS_MANY, 'Event', 'source_id', 'order'=>'events.id DESC'),
			'categories'=>array(self::MANY_MANY,'Category','ao_source_2_category(source_id,category_id)'),
		);
	}

	public function behaviors() {
		return array(
			'bRemember' => array(
				'class' => 'application.components.RememberFiltersBehavior',
			),
			'bSaveRelated' => array(
				'class' => 'application.components.SaveRelatedBehavior',
				'autoSaveRelated'=>false, //cridarem explícitament el mètode saveRelated
				'config'=>array(
					'categories'=>array('ao_source_2_category','source_id','category_id'),					
				),
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
			'feed' => Yii::t('db', 'Feed'),
			'feed_type' => Yii::t('db', 'Feed Type'),
			'parser' => Yii::t('db','Parser'),
			'xpath' => Yii::t('db','XPath'),
			'name' => Yii::t('db', 'Name'),
			'description' => Yii::t('db', 'Description'),
			'city_id' => Yii::t('db', 'City'),
			'created' => Yii::t('db', 'Created'),
			'updated' => Yii::t('db', 'Updated'),
			'lang' => Yii::t('db', 'Lang'),
			'eventCount' => Yii::t('db', 'Events'),
			'active' => Yii::t('db', 'Active', 1),
			'categories'=>Yii::t('db', 'Categories'),
			'city2events'=>Yii::t('db', 'City to events'),
		);
	}
	
	public static function label() {
		return Yii::t('db', 'Sources');
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
		$criteria->compare('t.active', $this->active, false);
		$criteria->compare('t.feed_type', $this->feed_type, false);
		$criteria->compare('t.city_id', $this->city_id, false);
		$criteria->compare('t.updated', $this->updated, false);

		$criteria->with = array('city','eventCount');

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array(
				'pageSize' => 20,
			),
			'sort' => array('defaultOrder' => 't.id DESC'),
		));
	}

	public function getDisplayName() {
		return $this->name;		
	}
	
	
	public static function feedTypes() {
		$a = array('ical'=>'iCal','rss'=>'RSS','xml'=>'XML','page'=>'Page','gcal'=>'GCalendar');
		
		return $a;
	}
	
	
	
	public function getParser() {
		
		if ($this->parser) {
			$class=$this->parser."Parser";
		} else {
			if ($this->feed_type===null) return null;
			$class=ucwords($this->feed_type)."Parser";
		}
		$parser=new $class($this);				
		return $parser;
		
	}
        
        public function getFeed(){
            $feed=$this->feed;
            //substitueix si val dates
            //TODO fer-ho generalitzable
            $feed=str_replace(array("{j}","{n}","{Y}"), array(date("j"),date("n"),date("Y")), $feed);
           
            $feed=str_replace(array("{Y+}"), array(date("Y")+1), $feed);
            
           
        
           return $feed;
           
            
        }
        
        
        // separats per |  ex:   //page:xpath|content:xpath|photo:xpath|...
        // 
        //page: xpath per parsejar una pagina (llista d'href
        //content: 1 xpath que apunta al contingut, dates i localitzacio incloses
        //photo xpath que apunta a la foto (si és possible)
        public function getXPaths(){
         
                    
            if(strpos($this->xpath,"|")===false){
                return array("page"=>$this->xpath);
            }else{
              $xp= explode("|",$this->xpath);  
              $res=array();
              foreach($xp as $r){
                  $it=explode(":",$r,2);
                  $res[$it[0]]=$it[1];
              }
            
              return $res;
            }

                       
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