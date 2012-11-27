<?php

/*
 * 
 * parser especial per Google Calendar
 * l'arxiu ics no es pot filtrar per esdeveniments futurs, per això és molt millor fer servir el feed
 * amb paràmetres:
 * 
http://www.google.com/calendar/feeds/aka19o976l6obnof7k63d55f6g@group.calendar.google.com/public/full?alt=json-in-script&callback=insertAgenda&orderby=starttime&max-results=15&singleevents=true&sortorder=ascending&futureevents=true
 */

class GCalParser extends Parser
{

  public function parse($num) {


      $sc=new Scraper();
  
      $uri=$this->source->getFeed();
  
      $params = ScraperUtils::getParamsFromUrl($uri);    
      
      $id=$params['src'];
      if($id==null){
          print "error: $uri la url ha de ser del tipus https://www.google.com/calendar/embed?src=xxx";
          return false;
      }
      $url="http://www.google.com/calendar/feeds/$id/public/full?alt=json&orderby=starttime&singleevents=true&sortorder=ascending&futureevents=true";
      
     
      
      $json=  SimpleCurl::curl_download($url);
      $data=  json_decode($json);
     
       $c=0;
      //TODO extreure altres camps
       
     
      foreach($data->feed->entry as $ev){
          
          $euri=$ev->link[0]->href;
          $e=EventItem::getCache($euri);
        if($e==null){
            $e=new EventItem($euri);
        }else{

        }   
         
        if(!$e->cached){
        
        print_r($ev->title->{'$t'});
        
        $e->summary=$ev->title->{'$t'};
        $e->description=  $ev->content->{'$t'}; 
        
       
        $start= strtotime($ev->{'gd$when'}[0]->startTime);
        
        $end= strtotime($ev->{'gd$when'}[0]->endTime);

        
        $e->startdate=date('y-m-d',$start);
        $e->starttime=date('H:i',$start);
        $e->enddate=date('y-m-d',$end);
        $e->endtime=date('H:i',$end);
    
        $e->setLocation($ev->{'gd$where'}[0]->valueString); 
       $e->setAddress($e->location);
       
       // print_r($ev->{'gd$where'});
        }
        $this->items[]=$e;
        
        
        
           $c++;
           if($c==$num) break;
                     
      }
      
      
       return parent::parse($num);
  }

}

?>
