<?php

class Event extends CActiveRecord {

	public $distance;
	
	public $categoryList;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{event}}';
	}

	public function eventSearch($s) {
		/* @var $s EventSearch */
		
		$t = $this->getTableAlias();
		
		$c = new CDbCriteria();
		
		$c->limit = $s->limit + 1; //afegim 1 per saber si hi ha més pàgines
		if ($s->pag>1) {
			$c->offset=($s->pag - 1)*$s->limit;
		}
		
		$c->select = "$t.*";
		
		
		$c->order = "$t.startdate, $t.id";
		
		if ($s->withCat) {
			$c->with = array('categories'=>array('together'=>false));
			$c->select .= ", categories.*";
		}
		
		if ($s->after_id) {
			$c->addCondition(sprintf("$t.id > %.0f",$s->after_id));
		}
		
		if ($s->d) {
			$d = "CAST('{$s->d}' AS DATE)";
			if ($s->e) { //data fi?			
				$e = "CAST('{$s->e}' AS DATE)";
				$c->addCondition("$t.startdate BETWEEN $d AND $e 
					OR $t.enddate BETWEEN $d AND $e 
					OR ($t.startdate <= $d AND $t.enddate >= $e)
				");
			} else {
				$c->addCondition("$d BETWEEN $t.startdate AND $t.enddate");
			}
		} else {
			//esdeveniments puntuals, en les properes h hores
			$h = (int)$s->h;
			$date = new DateTime();
			$d1 = $date->format('Y-m-d H:i:s');
			$date->add(new DateInterval("PT{$h}H"));
			$d2 = $date->format('Y-m-d H:i:s');		
			$c->addCondition("$t.starttime IS NOT NULL AND CONCAT($t.startdate,' ',$t.starttime) BETWEEN '$d1' AND '$d2' ");
		}
		
		if ($s->q) {
			$keyword='%'.strtr($s->q,array('%'=>'\%', '_'=>'\_', '\\'=>'\\\\')).'%';
			$c->addCondition("$t.summary LIKE :keyword OR $t.description LIKE :keyword OR $t.location LIKE :keyword");
			$c->params[':keyword'] = $keyword;
		}

		if ($s->cat) {
			if (is_numeric($s->cat)) {
				$c->join = "INNER JOIN {{event_2_category}} e2c ON e2c.event_id = $t.id";
				$c->addCondition("e2c.category_id = :category");
				$c->params[':category'] = (int)$s->cat;
			} else {
				$c->join = "INNER JOIN {{event_2_category}} e2c ON e2c.event_id = $t.id INNER JOIN {{category}} c ON c.id=e2c.category_id";
				$c->addCondition("c.name LIKE :category");
				$c->params[':category'] = $s->cat;
			}
		}
		
		/*
		https://developers.google.com/maps/articles/phpsqlsearch
		Here's the SQL statement that will find the closest 20 locations that are within a radius of 25 miles to the 37, -122 coordinate.
		SELECT id, ( 3959 * acos( cos( radians(37) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(-122) ) + sin( radians(37) ) * sin( radians( lat ) ) ) ) AS distance FROM markers HAVING distance < 25 ORDER BY distance LIMIT 0 , 20;		 
		To search by kilometers instead of miles, replace 3959 with 6371. 
		*/
		
		if ($s->lat && $s->lng) {
			$lat = $s->lat;
			$lng = $s->lng;
			$rad = $s->rad;
			$formula = "(6371*acos(cos(radians($lat))*cos(radians($t.geo_lat))*cos(radians($t.geo_lng)-radians($lng))+sin(radians($lat))*sin(radians($t.geo_lat))))";
			
			//AIXÒ fa petar CActiveFinder a causa de les comes
			//$distance = "IF($t.geo_lat && $t.geo_lng, $formula, NULL)";
			//alternativa:
			$distance = "(CASE WHEN $t.geo_lat THEN $formula ELSE NULL END)";
			
			$c->select .= ", $distance AS distance";
			$c->having = " distance < $rad ";
			
			$c->order = "distance, $t.startdate, $t.id";
		}
		
		
		$this->getDbCriteria()->mergeWith($c);

		return $this;
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
		);
	}
	
	
	
	/* VIRTUAL FIELDS */
	
	
	public function getImgUrl() {
		if (!$this->photo) return null;
		
		if (strpos($this->photo,'http')===0) return $this->photo;
		
		return Yii::app()->request->getHostInfo().Yii::app()->baseUrl.'/images/'.$this->photo;		
	}
	
	public function getStart() {
		$start = $this->startdate;		
		if ($this->starttime) {
			$a = explode(':',$this->starttime);
			$start.=" {$a[0]}:{$a[1]}";
		}
		return $start;
	}
	
	public function getEnd() {
		$end = $this->enddate;		
		if ($this->endtime) {
			$a = explode(':',$this->endtime);
			$end.=" {$a[0]}:{$a[1]}";
		}
		return $end;
	}
	
	public function getCategoryList() {
		//if ($this->categoryList!==null) return $this->categoryList;		
		$a = array();
		foreach ($this->categories as $r) {
			$a[] = $r->name;
		}
		$this->categoryList = $a;
		return $a;
	}
	
}