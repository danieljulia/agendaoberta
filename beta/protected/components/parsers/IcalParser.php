<?php

/*
*
*
*/

class IcalParser extends Parser
{

  public function parse($num) {

	//TO DO cal buscar links, per trobar imatges per exemple i altres coses
        
      $sc=new Scraper();
  
	$ical = new SG_iCal( $this->source->getFeed() );
    
        
	 foreach( $ical->getEvents() As $ev) {

            
             
            $uri="ical:".$ev->getProperty('uid');
            
          
           
             
                    $e=EventItem::getCache($uri);
                    if($e==null){
                        $e=new EventItem($uri);
                    }else{

                    }  
                 if(!$e->cached){   
                    
                     
                       //facebook event?
            if(strpos($uri,"@facebook")){
                preg_match("/e(.*?)\@/",$uri,$matches);
                $id=$matches[1];
                        
                $e->photo="https://graph.facebook.com/".$id."/picture?type=large";
               $uri="https://www.facebook.com/events/".$id;
            }
            
            $e->summary=$ev->getProperty('summary');
            $e->description=$ev->getProperty('description');    
            $e->startdate=date('y-m-d',$ev->getStart());
            $e->starttime=date('H:i',$ev->getStart());
            $e->enddate=date('y-m-d',$ev->getEnd());
            $e->endtime=date('H:i',$ev->getEnd());
            $e->setLocation($ev->getProperty('location'));
                  
            //todo fer servir el mateix que a la resta
            //si no hi ha camp location (a vegades passa), buscar al text
            if($e->location==""){
                $lloc=ScraperUtils::getNextText("Lloc:",$e->description,array("Organitza",":"));
                if($lloc)
                  $e->setLocation($lloc);
                //sino hi ha ciutat al location afegir a partr de la font
                if($this->source->city){
                    if(strpos($e->location,",")===false){
                        if($e->location!="") $e->location.=", ";
                        $e->location.=$this->source->city->name;
                    }
                }
                //address es l'adreça de cara a l'usuari
                $e->setAddress($e->location);
               
               
             
            }
                 }
            $this->items[]=$e;

           
      
	 }
       return parent::parse($num);
  }

}

?>
