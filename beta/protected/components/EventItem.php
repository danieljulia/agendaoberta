<?php
/**
 * Intenta fer un scrape d'una pàgina generìca
 * es crida des del parser
 * el punt d'entrada es el métode scrape al qual se li passa un objecte event
 * 
 *  
 */
class EventItem 
{

    public $url;
    public $uid;
    
    public $source_id;
    public $summary;
    public $description;
    public $startdate;
    public $starttime;
    public $enddate;
    public $endtime;
    public $location; //nomes a nivell de geoposicionament
    public $location_id;
    public $address; //a nivell de l'usuari
    public $photo;
     public $geo_lat;
     public $geo_lng;
     public $geo_pre;
     
     public $city_id;
     
    public $categories;
    public $tags;
    
    
    //if is old then is not saved
    public $old=false;
        
    public $pending=false;
       
       
    private static $cache=true; //cached system
    
    public $cached=false; //cached item
 
    
    public function EventItem($url){
        $this->url=$url;
        
        $this->uid=  self::makeId($url);
        
       
        
        $this->categories=array();
        $this->tags=array();
    }
    
    public  static function enableCache($b){
        self::$cache=$b;
    }
    
    private static function makeId($url){
        return md5($url);
    }
    
    public static function getCache($url){
        

        
           if(!self::$cache) return null;
           
            //$cache->cachePath="c:/temp";
            
             $res=  Yii::app()->cacheParser->get(self::makeId($url));
             
     
            
     
             if($res){
                 $ev=  unserialize($res);
                 $ev->cached=true;
                 return $ev;
             }       
             return null;
    }
    
    public static function setCache($e){
    
        $s=serialize($e);
        Yii::app()->cacheParser->set($e->uid,$s);

    }
    
    public static function flushCache(){
        
         Yii::app()->cacheParser->flush();
        
       
    }
    
    
    public static function GetCityId($pob){
             //TODO filtrar possibles duplicacions
            //crear població sino existeix
        
            $pob=  (trim($pob));
        
            $criteria=new CDbCriteria(array(
			'condition'=> "LOWER(name)=LOWER(:value)",
			'params'=>array(':value'=>$pob),
		));
            
            $pobs=City::model()->findAll($criteria);

            if(count($pobs)==0){
                $l=new City;
                $l->name=$pob;
                $l->lat=$e->geo_lat;
                $l->lng=$e->geo_lng;

                $l->save();
                $pob_id=$l->id;
            }else{
                $pob_id=$pobs[0]->id;
            }
            return $pob_id;
    }
    
    //només es fa servir per geolocalitzar
    public function setLocation($loc){
        //$loc=trim($loc);
      // $loc=strip_tags($loc);
      //  $loc=ScraperUtils::cutUntilZipCode($loc);
        $this->location=$loc;
    }

     public function setAddress($address){
      //  $address=trim($address);
      //  $address=strip_tags($address);

        $this->address=$address;
    }
    
    public function isOld(){
        if($this->startdate==null) return false; //no ho sabem
        
        if($this->isOldDate($this->startdate) && $this->isOldDate($this->enddate)){
       
               return true;
        }
                 return false;
                 
    }
    
    private function isOldDate($d){
        print "mirant si es antiga la data ".$d." resultat ".(strtotime($d) < (time()-60*60*24));
        
       if( strtotime($d) < (time()-60*60*24)) return true;
       return false;
        
    }

    public function dump(){
        $txt="<br/><br/>".$this->summary;
        if($this->startdate) $txt.=": ".$this->startdate;
        if($this->starttime) $txt.=" ".$this->starttime;
        if($this->enddate) $txt.=": ".$this->enddate;
        if($this->endtime) $txt.=" ".$this->endtime;
        if($this->photo) $txt.=" ".$this->photo;
        foreach($this->categories as $cat){
            $txt.=" | ".$cat;
        
        }
        if($this->geo_lat) $txt.=" ".$this->geo_lat;
        if($this->geo_lng) $txt.=" ".$this->geo_lng;
        if($this->geo_pre) $txt.=" ".$this->geo_pre;
        return $txt;
    }
    
    public function copy(&$event){
        $event->url=$this->url;
        
        $event->source_id=$this->source_id;
        $event->uid=$this->uid;
        
        $event->summary=$this->summary;
        $event->description=$this->description;
        
        $event->startdate=$this->startdate;
        $event->starttime=$this->starttime;
        $event->enddate=$this->enddate;
        $event->endtime=$this->endtime;
        
        $event->location=$this->location;
        $event->address=$this->address;
        $event->location_id=$this->location_id;
         $event->geo_lat=$this->geo_lat;
        $event->geo_lng=$this->geo_lng;
        $event->geo_pre=$this->geo_pre;
        
        $event->photo=$this->photo;
        
         $event->city_id=$this->city_id;
        /*
        $event->categories=array();
        foreach($this->categories as $cat){
             $event->categories[]=$cat;
        }
        print "***";        
        
        print_r($event->categories);
           */     
           $event->categories=$this->categories;
                

        
    }
    /*
    public function _set($k,$v){
        $this->$k = $v;
    }
    public function __get($k){
        return $this->$k;
    }*/
    
}

?>
