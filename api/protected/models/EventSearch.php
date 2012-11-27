<?php

class EventSearch extends CFormModel
{
	
	public $limit = 20;
	
	public $h;
	public $q;   //text lliure
	public $loc; //localitat
	public $d;   //data
	public $e;   //data (fins a)
	public $geo; //geo localització: "latitude,longitude" 37.781157,-122.398720
	public $rad; //radi en km
	public $cat; //categoria
	public $tag; //tags
	public $pag; //pàgina
	public $after_id;
	

	public $withCat; //ens permet definir si volem fer with('categories')
	
	private $lat;
	private $lng;
	
	public function rules()
	{

		$msgRad = Yii::t('api',"Especifiqueu el radi de cerca en km. amb un valor numèric entre 1 i 100.");
		$msgH = Yii::t('api',"Especifiqueu les hores un valor numèric entre 1 i 12.");
		
		return array(			
			//array('d', 'required', 'message'=>Yii::t('api','El paràmetre {attribute} és obligatori.')),
			array('d, e', 'date', 'format'=>'yyyy-MM-dd', 'message'=>Yii::t('api','Especifiqueu el paràmetre {attribute} en format aaaa-mm-dd.')),
			array('geo', 'geo'),
			array('rad','numerical','min'=>1,'max'=>100,'message'=>$msgRad,'tooBig'=>$msgRad,'tooSmall'=>$msgRad),
			array('rad','default','value'=>3),
			array('h','numerical','integerOnly'=>true,'min'=>1,'max'=>12,'message'=>$msgH,'tooBig'=>$msgH,'tooSmall'=>$msgH),
			array('h','default','value'=>8),				
			array('pag','numerical','min'=>1,'max'=>100,'message'=>Yii::t('api',"Especifiqueu un valor numèric entre 1 i 100 per al número de pàgina.")),
			array('pag','default','value'=>1),			
			array('q,loc,cat,tag,after_id','safe'),
			
		);
	}


	public function attributeLabels()
	{
		return array(
			'd'=>'d',
			'e'=>'e',
				
		);
	}

	/**
	 * Authenticates the geo param.
	 */
	public function geo($attribute,$params)
	{
		if (!$this->$attribute) return;
		$a = explode(",",$this->$attribute);
		if (count($a)==2) {
			if ( !is_numeric($a[0]) || !is_numeric($a[1]) || abs($a[0])>90 || abs($a[1])>180 ) {
				$this->addError($attribute, Yii::t('api','Especifiqueu el paràmetre {attribute} com "latitud,longitud", per exemple: 41.789368,0.802808',array('{attribute}'=>'geo')));
			} else {
				$this->lat = $a[0];
				$this->lng = $a[1];
			}
		} else {
			$this->addError($attribute, Yii::t('api','Especifiqueu el paràmetre {attribute} com "latitud,longitud", per exemple: 41.789368,0.802808',array('{attribute}'=>'geo')));
		}		
	}

	
	public function getLat() {
		return $this->lat;
	}
	
	public function getLng() {
		return $this->lng;
	}
}