<?php

/*
parser de RSS
 * todo
 * 
 * -buscar info a namespaces (dates, geo, imatges, etc..)
 */

class RssParser extends Parser
{

    /*
    function __construct($source) {
    
       parent::__construct($source);
    
   }*/
        public function parse($num=null){
            
            //http://www.simplepie.org/api/class-SimplePie_Item.html
            $feed=new SimplePie();
            //todo activar caché?
            $feed->enable_cache(false);
            $feed->set_feed_url($this->source->getFeed());
            $feed->init();

           
            
            $c=0;
            foreach ($feed->get_items() as $post){
                  
                //print_r($post);
                
                $uri=htmlspecialchars_decode($post->get_permalink());
              
                
                $e=EventItem::getCache($uri);
                if($e==null){
                    $e=new EventItem($uri);
                }
                
                     
                $e->summary=$post->get_title();
                $e->description=$post->get_description();    
                
                
                //geo rss
                if( $post->get_latitude()){
                 $e->geo_lat=$post->get_latitude();   
                }
                if( $post->get_longitude()){
                 $e->geo_lng=$post->get_longitude();   
                 $e->geo_pre=9;
                }
             

                 //has namespace for events?
                 $startdate=$post->get_item_tags("http://purl.org/rss/1.0/modules/event/","startdate");
                 if(isset($startdate[0]['data'])){
                     $s=strtotime($startdate[0]['data']);
                        $e->startdate=date('y-m-d',$s);
                        $e->starttime=date('H:i',$s);
        
                  
                 }
                 
                  $enddate=$post->get_item_tags("http://purl.org/rss/1.0/modules/event/","enddate");
                 if(isset($enddate[0]['data'])){
                     $s=strtotime($enddate[0]['data']);
                        $e->enddate=date('y-m-d',$s);
                        $e->endtime=date('H:i',$s);
        
                  
                 }
                 
                 
                     print("la data es *".$startdate[0]['data']."*");
                 
                 print "la data convertida es ".strtotime( $startdate[0]['data']);
                 
                 $location=$post->get_item_tags("http://purl.org/rss/1.0/modules/event/","location");
                  if(isset($location[0]['data'])){
                      $e->location=$location[0]['data'];
                       $e->address=$location[0]['data'];
                  }
                 $organizer=$post->get_item_tags("http://purl.org/rss/1.0/modules/event/","organizer");
                  if(isset($organizer[0]['data'])){
                        //todo
                      $e->description.="<br/>Organitza: ".$organizer[0]['data'];
                      }
                      
                $type=$post->get_item_tags("http://purl.org/rss/1.0/modules/event/","type");
                    if(isset($type[0]['data'])){
                        //todo
                        $e->description.="<br/>Tipus d'activitat: ".$type[0]['data'];
                      }
               // print $post->get_type();
               //  print $post->get_startdate();
                
                $this->items[]=$e;
                $c++;
                if($c==$num) break;
               
            }
            
        print "*********************<br> a RssParser tinc ".count($this->items)." total";
            
            return parent::parse($num);
        }
  
}
?>
