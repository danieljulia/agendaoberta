<?php

/**
 * This is the model class for table "{{stats}}".
 *
 * The followings are the available columns in table '{{stats}}':
 * @property integer $id
 * @property string $label
 * @property integer $value
 * @property string $updated
 */
class Stats extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Stats the static model class
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
		return '{{stats}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('label, value, updated', 'required'),
			array('value', 'numerical', 'integerOnly'=>true),
			array('label', 'length', 'max'=>8),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, label, value, updated', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'label' => 'Label',
			'value' => 'Value',
			'updated' => 'Updated',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('label',$this->label,true);
		$criteria->compare('value',$this->value);
		$criteria->compare('updated',$this->updated,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        
        public function setValue($label,$value){
            $this->label=$label;
            $this->value=$value;
            
        }
        
        public static function saveValue($label,$value){
              $stat=new Stats;
              $stat->setValue($label,$value);
              $stat->save(false);
        }
        
        public static function lastValue($label){
            //todo potser cachejar aquests valors
            
            $options=array('order'=>'updated DESC',
                'condition'=>"label='$label'"
                );
            $e=self::model()->find($options);
            return $e->value;
        }
            
         /**
         * Retorna nombre de events existents entre 2 dates
         * @param type $period 
         */
	public static function EventsCount($startdate=0,$enddate=0){
            if($enddate==0){
                $enddate="2100-1-1";
            }
            $criteria = new CDbCriteria;
            
            
            
            $criteria->condition = '(startdate >="'.$startdate.'" AND startdate<="'.$enddate.'") or '.
                    '(enddate >="'.$startdate.'" AND enddate<="'.$enddate.'") ';
                    
         
            return Event::model()->count($criteria);
        }
        
        public static function EventsCreated($since=0){    
            $criteria = new CDbCriteria;
            $criteria->condition = 'created >="'.$since.'"';
            return Event::model()->count($criteria);
        }
        
        
         /**
         * Elements pendents de ser processats
         */
	public static function EventsNotProcessed(){

           return Event::model()->notProcessed()->count();
        
        }

         /**
         * Sources parsejats els ultims temps
         */
	public static function SourcesParsedSince($since=0){
            $criteria = new CDbCriteria;
            $criteria->condition = 'started >="'.$since.'"';
            return Source::model()->findAll($criteria);
        }
        
 
          /**
         * 
         */
	public static function SourcesWithErrors($last=0){
            $criteria = new CDbCriteria;
            $criteria->condition = 'error >='.Yii::app()->params['max_errors_per_source'];
            $criteria->order='error desc'; 
            return Source::model()->findAll($criteria);
        }
        
         /**
         * 
         */
	public static function EventsLast($last=20){
             $criteria = new CDbCriteria;
             $criteria->limit=$last;
            return Event::model()->with('source')->last_created()->findAll($criteria);
        }
        
         /**
         * 
         */
	public static function EventsProcessed($last=0){
       
        }
        
           /**
         * 
         */
	public static function superMaps(){
            $thumbs = Yii::app()->params['thumbs'];
           
            //24h
            $evs24h= Event::model()->inDays(1,false)->findAll();
            $kml=self::eventsToKml($evs24h);
            file_put_contents($thumbs."/24h.kml",$kml);
            
            //48h
            $evs48h=Event::model()->withCategories()->inDays(2,true)->findAll();
            $kml=self::eventsToKml($evs48h);
            file_put_contents($thumbs."/48h.kml",$kml);
            
            //una setmana
            $evs7days=Event::model()->withCategories()->inDays(7,true)->findAll();
            $kml=self::eventsToKml($evs7days);
            file_put_contents($thumbs."/7days.kml",$kml);
            
            
        }
        
        public static function eventsToKml($evs){
            
                 $kml='<kml xmlns="http://www.opengis.net/kml/2.2"
 xmlns:gx="http://www.google.com/kml/ext/2.2">  <Document>
    <name>Agenda Oberta</name>
<Style id="ao_precise">
 <IconStyle> <Icon> <href>http://www.oberta.cat/images/maps/marker.png</href> </Icon></IconStyle>
</Style>     

';
                 
                 
            foreach($evs as $ev){
                if($ev->geo_lat){
                    $photo="";
                    if($ev->photo!=""){
                        $photo="<img style='width:150px' src='".$ev->photo."'/>";
                        }
                            
                            
                    $kml.='
                <Placemark>
      <name><![CDATA['.$ev->summary.']]></name>
      <description>
        <![CDATA[
        '.CHtml::link($photo,'http://www.oberta.cat/event/'.$ev->id).'<br/>
            '.CHtml::link("Veure més",'http://www.oberta.cat/event/'.$ev->id).'<br/>On: '.$ev->location.'
      <br/>Quan:  '.$ev->getFullFormattedDate().'
        ]]>
      </description>
      <styleUrl>#ao_precise</styleUrl> 
      <Point>
        <coordinates>'.$ev->geo_lng.','.$ev->geo_lat.'</coordinates>
      </Point>
    </Placemark>';
                    
                }
                        
            }
            
            $kml.="</Document>
</kml>";
           return $kml;
        
            
        }
            
        
}