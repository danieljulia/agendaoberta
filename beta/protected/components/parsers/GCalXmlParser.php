<?php

/**
 * parser per google calendar en format xml
 * millor que ical (ical manté tots els events antics i a més no es poden filtrar)
 * 
 * ATENCIÓ: No funciona perquè l'xml no separa les dates en camps independents!!
 */

class GCalXmlParser extends XMLParser
{
        
        public function parse($num=null){
   
          // print_r( $this->data );
               
            $sc=new Scraper();
        
        
         $c=0;
            foreach( $this->data['feed']['entry'] as $event ){
                              
                    //todo aqui poden haver categories que es podrien utilitzar al menys com a tags
                
                
                    $uri=$event['link']['0_attr']['href'];
                
                    $e=EventItem::getCache($uri);
                    if($e==null){
                        $e=new EventItem($uri);
                    }else{

                    }  
                            
                    if(!$e->cached){        
                
                        $e->summary=$event['title'];
                        $e->description=$event['content'];

                    }
                     
                    
                        $c++;
                     if($c==$num) break;
                }
              
            
              return parent::parse($num);
        }


        

    
}

?>
