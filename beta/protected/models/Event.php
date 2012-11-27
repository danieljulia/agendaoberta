<?php
Yii::import('application.extensions.image.*');
Yii::import('application.helpers.CArray');

class Event extends CActiveRecord {

	
	public $category_id; //used in search
	public $user_id; //used in search;
	
	public $uploadImg;
	public $removeImg;
	
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
		return '{{event}}';
	}

	public function forCategory($id) {

		$t = $this->getTableAlias();

		$this->getDbCriteria()->mergeWith(array(
				'join' => "INNER JOIN {{event_2_category}} e2c ON e2c.event_id = $t.id AND e2c.category_id = :category_id",
				'params' => array(':category_id' => $id),
		));
		return $this;
	}

	public function scopes() {
		$t = $this->getTableAlias();
		$d = date('Y-m-d');
		return array(
			'last_created' => array(
				'order' => "$t.created DESC",
			),
			'next' => array(
				'condition' => "$t.startdate>=:cur_date",
				'params'=>array(':cur_date'=>$d),
				'order' => "$t.startdate",
			),
                    'withCity' => array(
				'with'=>array('city'),	
			),
			'withCategories' => array(
				'with'=>array('categories'),	
			),
                        'notProcessed' => array(
				'condition' => "$t.processed=0",
				'order' => "$t.updated",
			),
                    
                        'notClassified' => array(
                                    'condition' => "$t.category_classified=0",
                                   
                            ),
                    
		);
	}

	public function forUser($id) {
		$t = $this->getTableAlias();
		$this->getDbCriteria()->mergeWith(array(
			'join'=>"INNER JOIN {{user_events}} ue ON ue.event_id = $t.id ",
			'condition'=>"ue.user_id = :user_id",
			'params'=>array(':user_id'=>$id),
		));
		return $this;
	}
	
	public function init() {
		$this->source_id = Yii::app()->params['default_source_id'];
	}
	
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('summary,location,startdate,enddate','required', 'on'=>'create,update'),
			array('summary,url', 'length', 'max'=>255, 'on'=>'create,update'),
			array('location,address', 'length', 'max'=>128, 'on'=>'create,update'),
			array('description', 'safe', 'on'=>'create,update'),
			
			array('startdate,enddate', 'date', 'format' => 'yyyy-MM-dd', 'on'=>'create,update'),
			array('enddate', 'compare', 'compareAttribute'=>'startdate', 'operator'=>'>=', 'on'=>'create,update'),
			array('categories','default','value'=>array(),'on'=>'create,update'),
			array('starttime,endtime','TimeValidator','on'=>'create,update'),
			array('city_id', 'exist', 'className'=>'City', 'attributeName'=>'id', 'allowEmpty'=>true, 'on'=>'create,update'),
			
			array('uploadImg','file','types'=>array('gif','jpg','png'),'maxSize'=>2*1024*1024,	'allowEmpty'=>true,'on'=>'create,update'),
			
			array('score','in','range'=>array('0','1','2','3','4','5','6','7','8','9','10'),'on'=>'create,update'),
			
			array('geo_lat,geo_lng','numerical','integerOnly'=>false,'on'=>'create,update'),
			array('removeImg', 'boolean', 'on'=>'create,update'),
			array('promoted', 'boolean', 'on'=>'create,update,updateField'),
			array('num_favorites,num_flagged', 'numerical', 'integerOnly'=>true, 'min'=>0, 'on'=>'updateField'),
			
			array('city_id,starttime,endtime,geo_lat,geo_lng','default','value'=>null,'on'=>'create,update'),
			array('id, summary, url, location_id, description, startdate, enddate, starttime, endtime, schedule, geo_lat, geo_lng, photo, location, address, source_id, city_id, created, updated, lang, score, uid, category_id, num_favorites, num_flagged, promoted', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
				'categories' => array(self::MANY_MANY, 'Category', 'ao_event_2_category(event_id,category_id)'),
				'tags'=>array(self::MANY_MANY, 'Tag', 'ao_event_2_tag(event_id,tag_id)'),
				'source' => array(self::BELONGS_TO, 'Source', 'source_id'),
				'city' => array(self::BELONGS_TO, 'City', 'city_id'),
		);
	}

	
        /* VIRTUAL FIELDS */
	
	
	/* see http://www.unicode.org/reports/tr35/#Date_Format_Patterns */
	public function getFullFormattedDate() {
                $tw = time() + (1 * 24 * 60 * 60);
                $twdate=date('Y-m-d', $tw);
                    
		$df = Yii::app()->getDateFormatter();
		if ($this->startdate == $this->enddate) {
			if ($this->starttime) {
                               
                                $d=$this->formatDate($this->startdate,$this->starttime);
                                
				if ($this->endtime && $this->endtime!=$this->starttime) {
					$d.= ' - '.$df->format("HH:mm", $this->startdate.' '.$this->endtime);
				}
				return $d;
			} else {
				
                                return $this->formatDate($this->startdate);
			}
		} else {
			if ($this->starttime) {
                                $d=$this->formatDate($this->startdate,$this->starttime);
                            
				$d = $df->format("EEEE, d MMMM | HH:mm", $this->startdate.' '.$this->starttime);		
				if ($this->endtime) {
					$d.= ' - '.$this->formatDate($this->enddate,$this->endtime);
				} else {
					$d.= ' - '.$this->formatDate($this->enddate);		
				}
				return $d;
			} else {
				return $this->formatDate($this->startdate).' - '.$this->formatDate($this->enddate);
			}
		}		
	}
	
        private function formatDate($sdate,$stime=""){
             $df = Yii::app()->getDateFormatter();
            $stoday=date('Y-m-d');
            
            $tomorrow = time() + (1 * 24 * 60 * 60);
            $stomorrow=date('Y-m-d', $tomorrow);
                
            if($stime==""){
                if($sdate==$stoday) return "Avui";
                if($sdate==$stomorrow) return "Demà";
                return $df->format("EEEE, d MMMM", $sdate);
                
            }else{
                if($sdate==$stoday) return "Avui ".$df->format("| HH:mm", $sdate.' '.$stime);
                if($sdate==$stomorrow) return "Demà ".$df->format("| HH:mm", $sdate.' '.$stime);
                return $df->format("EEEE, d MMMM | HH:mm", $sdate.' '.$stime);
            }
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
					'categories'=>array('ao_event_2_category','event_id','category_id','default_category_id'),					
				),
			),
		);
	}
	
	protected function beforeSave() {
		if ($this->isNewRecord) {
			$this->created = $this->updated = date('Y-m-d H:i:s');
			if ($this->scenario=='create') {
				$this->uid = $this->source_id.'_'.time();
			}
		} else {
			$this->updated = date('Y-m-d H:i:s');
		}
		if (!$this->enddate) $this->enddate = $this->startdate;
		if ($this->startdate) {
			$s = new DateTime($this->startdate);
			$e = new DateTime($this->enddate);
			$interval = $e->diff($s,true);
			// %a will output the total number of days.
			$this->duration = $interval->format('%a days');			
		}
		
		if ($this->removeImg) {
			$this->deleteImage();
			$this->photo_local='';
		}
		if (!$this->uploadImg instanceof CUploadedFile) {
			$this->uploadImg = CUploadedFile::getInstance($this, 'uploadImg');
		}
		if ($this->uploadImg instanceof CUploadedFile) {
			$this->photo_local = $this->uid.'.'.$this->uploadImg->getExtensionName();
		}
		return parent::beforeSave();
	}

	
	protected function afterSave() {
		parent::afterSave();
		if ($this->uploadImg instanceof CUploadedFile) {
			$path = Yii::app()->params['thumbs_path'];
			$filename = $path.'/originals/'.$this->photo_local;
			$success = $this->uploadImg->saveAs($filename);
			if ($success) {
				$image = new Image($filename);
				$image->resize(250, 325);
				$thumb = $path.'/thumbs/'.$this->photo_local;
        if (!$image->save($thumb, false)) {
					copy($filename,$thumb);
				}
			}			
		}
		
	}
	
	protected function afterDelete() {
		parent::afterDelete();
		//TO DO: reajustar favorits a la taula d'usuaris
		$this->deleteImage();
	}
	
	protected function deleteImage() {
		if ($this->photo_local) {
			$path=str_replace('//','/',Yii::app()->params['thumbs_path'].'/');
			@unlink($path.'originals/'.$this->photo_local);
			@unlink($path.'thumbs/'.$this->photo_local);
		}
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
				'id' => 'ID',
				'summary' => Yii::t('db','Summary'),
				'url' => Yii::t('db','Url'),
				'location_id' => Yii::t('db','Location'),
				'description' => Yii::t('db','Description'),
				'startdate' => Yii::t('db','Start Date'),
				'enddate' => Yii::t('db','End Date'),
				'starttime' => Yii::t('db','Start Time'),
				'endtime' => Yii::t('db','End Time'),
				'schedule' => Yii::t('db','Schedule'),
				'geo_lat' => Yii::t('db','Geo Lat'),
				'geo_lng' => Yii::t('db','Geo Lng'),
				'geo_pre' => Yii::t('db','Geo Precision'),
				'photo' => Yii::t('db','Photo'),
				'photo_local' => Yii::t('db','Photo Local'),
				'uploadImg' => Yii::t('db','Photo Local'),
				'location' => Yii::t('db','Location'),
				'address' => Yii::t('db','Address'),
				'source_id' => Yii::t('db','Source'),
				'city_id' => Yii::t('db','City'),
				'created' => Yii::t('db','Created'),
				'updated' => Yii::t('db','Updated'),
				'lang' => Yii::t('db','Lang'),
				'score' => Yii::t('db','Score'),
				'uid' => Yii::t('db','Uid'),
				'categories'=> Yii::t('db','Categories'),
				'category_id'=> Yii::t('db','Categoria'),
				'num_favorites'=> Yii::t('db','Num. favorites'),
				'num_flagged'=> Yii::t('db','Num. flagged'),
				'promoted' =>  Yii::t('db','Promoted'),
		);
	}

	public static function label() {
		return Yii::t('db', 'Events');
	}
	
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.summary', $this->summary, true);
		$criteria->compare('t.url', $this->url, true);

		$criteria->compare('t.startdate', $this->startdate, false);
		$criteria->compare('t.enddate', $this->enddate, false);

		$criteria->compare('t.location', $this->location, true);
		
		$criteria->compare('source.name', $this->source_id, true);
		$criteria->compare('t.city_id', $this->city_id, false);
		$criteria->compare('t.created', $this->created, false);
		$criteria->compare('t.updated', $this->updated, false);
		$criteria->compare('t.score', $this->score);
		
		$criteria->compare('t.num_favorites', $this->num_favorites, false);
		$criteria->compare('t.num_flagged', $this->num_flagged, false);
		$criteria->compare('t.promoted', $this->promoted, false);
		
		$criteria->compare('t.uid', $this->uid, true);

		$criteria->with = array('source','city','categories');
		
		if ($this->category_id) {												
			$criteria->join = " INNER JOIN {{event_2_category}} cat ON cat.event_id=t.id ";
			$criteria->addColumnCondition(array('cat.category_id'=>$this->category_id));
		}
		
		if ($this->user_id) {
			$criteria->join = " INNER JOIN {{user_event}} ue ON ue.event_id=t.id ";
			$criteria->addColumnCondition(array('ue.user_id'=>$this->user_id));
		}

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array(
				'pageSize' => 100,
			),
			'sort' => array('defaultOrder' => 't.id DESC'),
		));
	}

	
	public function getDisplayName() {
		return Utils::truncate($this->summary,50);		
	}
	
	public function recalculateNumFavorites() {
		$n = $this->getDbConnection()->createCommand(
				"UPDATE {{event}} SET num_favorites = (SELECT COUNT(*) FROM {{favorite}} WHERE event_id=:event_id)
				WHERE id=:event_id"
		)->execute(array(':event_id'=>$this->id));
		return $n;
	}	
              
	public static function createFromEventItem($item){


	}
        
            public function isGeoEncoded(){
            if($this->geo_lat!=null && $this->geo_lng!=null){
                return true;
            }
            return false;

        }
    
        public function createThumb(){

            if($this->photo!=""){
                
                    $thumbs=Yii::app()->params['thumbs_path'];
                try{

                    $ext=ScraperUtils::getExtension($this->photo);
                    if(!$ext){
                        $ext="jpg";
                    }else{
                        $this->photo=  ScraperUtils::removeAllParams($this->photo);
                    }


               // $im=  ScraperUtils::http_get_file($this->photo);
                $im=SimpleCurl::curl_download($this->photo);        
             
                
                if(!$im || $im=="" || strlen($im)<500){
                    print "error";
                    //error a la foto.. millor esborrar-la
                    $this->photo="";
                    return false;
                }else{
                   
                    
                    $imguri=$thumbs.'/originals/'.$this->uid.".".$ext;
                    file_put_contents($imguri, $im);
                    if(strtolower($ext)=="bmp"){
                        try{
                            $bmp=ScraperUtils::ImageCreateFromBMP($imguri);
                            if($bmp){
                                // file_put_contents("c:/temp/ole.jpg", $bmp);
                                $ext="jpg";
                                $imguri=$thumbs.'/originals/'.$this->uid.".".$ext;
                                if(!imagejpeg($bmp, $imguri)){
                                    print "error saving $imguri file";
                                    return false;
                                }
                            }else{
                                return false;

                            }
                         }catch(Exception $e){
                                print "error in ImageCreateFromBMP";
                                return false;
                          }

                    }else{

                    }

                    $image = Yii::app()->image->load($imguri);
                    $image->resize(300, 300);
                    $image->crop(250, 250);
                    if(!$image->save($thumbs.'/thumbs/'.$this->uid.".".$ext)){ // or $image->save('images/small.jpg');
                        print "unable to save";
                        return false;
                    }
                $this->photo_local = $this->uid.".".$ext;
                    return true;
                }
                /*
                $image->resize(300, 300);
                if(!$image->save('c:/temp/thumbs/'.$e->uid.".jpg")){ // or $image->save('images/small.jpg');                
                    print "unable to save";
                }

                    */
                }catch(Exception $ex){
                    print "error creating thumb".$this->photo;
                    print_r($ex->getMessage());
                    return false;

                }
            }
    }
    
    public function postProcess($categorizer){
        //geocode
        if(!$this->isGeoEncoded()){
                print "<br>geocoding ".$this->location;
                GeoCoder::encode( ($this->location), ($this->address),$this);
                if(!$this->isGeoEncoded()){
                    print "<br>no s'ha geolocalitzat, assignant a la ciutat de la font";
                if($this->source->city){
                    $this->geo_lat=$this->source->city->lat;
                    $this->geo_lng=$this->source->city->lng;
                    $this->geo_pre=3;
                }
                }
        }

        if($this->photo && !$this->photo_local){
            print "creating thumb";
            //create thumb
            try{
                $this->createThumb();   
            }catch(Exception $e ){
                print "exception creating thumb ".$e->getMessage();
            }
        }
				
        //categorize
        print "categorizing";

        //if has categories already do nothing

        $cat_added=false;

        if(count($this->categories)==0){

            $thiscats=array();

           
            $cats=$categorizer->classify($this);


            foreach($cats[0]['classification'] as $it){
                if($it['p']>=0.85){ //todo a config
                    $thiscats[]=$it['class'];

                        $cat_added=true;


                }

            }
            $this->categories=$thiscats;

            $this->category_classified=1;
        }

        print "<br><br>categories.. ";
				
        //score
        $score=10;
        if($this->summary=="") $score-=10;
        if($this->description=="")  $score-=5;
        if($this->startdate==null)  $score-=2;
        if($this->starttime==null) $score-=1;
        if($this->enddate==null) $score-=1;
        if($this->photo=="") $score-=2;
        if($this->location=="") $score-=2;
        if($this->geo_pre<6) $score-=2;
        if($this->geo_pre>=9) $score++;
        if($score<0) $score=0;
        if($score>10) $score=10;
         
        $this->score=$score;

        // 
        // 
        //save



        $this->processed=date('Y-m-d H:i:s');
        $this->save(false); //without validation
        //insertar categories després
        if($cat_added){

            $this->saveRelated('categories');
        }
    }
    
    
    //scopes copiats de web
    /**
	 * Scope per a cercar esdeveniments que comencen en X hores
	 * @param int $hours Nombre d'hores 
	 */
	public function startsInHours($hours) {				
		$t = $this->getTableAlias();
		
		$date = new DateTime();
		$d1 = $date->format('Y-m-d H:i:s');
		$date->add(new DateInterval("PT{$hours}H"));
		$d2 = $date->format('Y-m-d H:i:s');

		$condition = "$t.starttime IS NOT NULL AND CONCAT($t.startdate,' ',$t.starttime) BETWEEN '$d1' AND '$d2' ";
	
		$this->getDbCriteria()->mergeWith(array('condition'=>$condition));
		return $this;	
	}
        
        /**
	 * Scope per a cercar esdeveniments que comencen en X dies
	 * @param int $days Nombre de dies 
	 * @param boolean $checkStartOnly Cercar només basant-se en la data d'inici de l'esdeveniment (true), o tenir en compte la data d'inici i fi (false)
	 */
	public function inDays($days,$checkStartOnly=false) {		
		$date = new DateTime();
		$d1 = $date->format('Y-m-d');
		$date->add(new DateInterval("P{$days}D"));
		$d2 = $date->format('Y-m-d');		
		return $this->betweenDates($d1, $d2, $checkStartOnly);		
	}
        
        /**
	 * Scope per a cercar esdeveniments que comencen el proper cap de setmana (l'actual si estem en dissabte).
	 * @param boolean $checkStartOnly Cercar només basant-se en la data d'inici de l'esdeveniment (true), o tenir en compte la data d'inici i fi (false)
	 */
	public function nextWeekend($checkStartOnly=false) {
		$date = new DateTime('Saturday');
		$d1 = $date->format('Y-m-d');
		$date->add(new DateInterval("P1D"));
		$d2 = $date->format('Y-m-d');
		return $this->betweenDates($d1, $d2, $checkStartOnly);	
	}
	
	/**
	 * Scope per a cercar esdeveniments entre dues dates
	 * @param string $d1 Data 1, en format Y-m-d
	 * @param string $d2 Data 2, en format Y-m-d
	 * @param boolean $checkStartOnly Cercar només basant-se en la data d'inici de l'esdeveniment (true), o tenir en compte la data d'inici i fi (false)
	 */	
	public function betweenDates($d1,$d2,$checkStartOnly=false) {
		$t = $this->getTableAlias();
		if ($checkStartOnly) {
			$condition = "$t.startdate BETWEEN '$d1' AND '$d2' ";
		} else {
			$condition = "$t.startdate BETWEEN '$d1' AND '$d2' 
					OR $t.enddate BETWEEN '$d1' AND '$d2' 
					OR ($t.startdate <= '$d1' AND $t.enddate >= '$d2')
				";
		}
		$this->getDbCriteria()->mergeWith(array('condition'=>$condition));
		return $this;
	}
}