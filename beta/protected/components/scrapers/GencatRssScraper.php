<?php


class GencatRssScraper extends Scraper
{
    
    public function findPhoto(&$e,$container=""){
         //imposar base path
            $this->basepath="http://cultura.gencat.cat/agenda/";
           
        parent::findPhoto($e,"//div[@class='contenidor']");
          
    }
    
    public function findDatesAndLocation(&$e){
        // $e=new Event;

  
          
           
            //contenidor per la foto
          

            //$res = $this->xpath->query("//div[@class='contenidor']/div[2]/div[2]/*");
             $res = $this->findXpath("//div[@class='contenidor']/div[2]/div[2]/*");

            setlocale(LC_TIME, "spanish");
            $status="";

  

            foreach ($res as $n){
                $val=trim($n);

          
                switch($status){
                    case "dates":
                        /*
                        $val=str_replace("Del", "", $val);
                        $val=str_replace("De l'", "", $val);
                        $val=str_replace("al", "", $val);
                        $val = preg_replace('/\s+/', ' ', ($val));
                        $val=trim($val);

                        $dates=explode(" ",$val);
                        $isodates=array();

                        foreach($dates as $date){
                            $date = preg_replace("/(\d+)\D+(\d+)\D+(\d+)/","$3-$2-$1",$date);
                            //  print "trobada aquesta data".$date;
                            $t=strtotime($date);
                            print date('d-m-Y',$t)."<br/>";
                            $isodates[]=date('Y-m-d',$t);
                        }
                        $status="";
                        */

                        $status="";
                        $isodates=$this->scrapeDatesCat($val);
                        
             

                        $e->startdate= $isodates[0];
                        if(count($isodates)>1) $e->enddate= $isodates[1];

                        break;
                    case "horari":
                        $val=str_replace("h", "", $val);
                        $val=trim($val);

                       
                        if(strlen($val)==2) $val.=":00";

                        //substituir . per :
                        $val=str_replace(".",":",trim($val));

                     
                        
                        //buscar hores.. per exemple pot ser "De 10 a 20"
                       // $times=$this->scrapeTimesCat($val);
                        $times=$this->extractTime($val);
                        
                        print "<br/>";
                        print_r($times);
                    
                        
                        if(count($times)>1){
                             $e->starttime=$times[0];
                             $e->endtime=$times[1];
                        }else{
                            //if( $e->enddate==""){
                                //todo error
                                $e->starttime=$times[0];// trim($val);
                              

                           // }
                        }
                        
                          $e->schedule=$val;

                            $status="";
                        break;
                    case "lloc":
                        print "<br/>lloc:".$val;
                            $status="";
                            

                             //eliminar part de Web
                             $res=explode("Web",$val);
                             if(count($res)>1) $val=$res[0];
                             
                             //eliminar la comarca que està entre () al final
                             //ex. (Barcelonès)
                             $val = trim(preg_replace('/\s*\([^)]*\)/u', '', $val));
                             
                            
                             $val=trim($val);									
                             $val = str_replace("\t",'',$val);	
                             //afegir comes després de tots els números! amb això millora molt
                             $val = preg_replace('/(?<=\d)(?![\d,])/u', ',', $val);
                             $e->setLocation($val);
                                $e->setAddress($e->location);
                            //todo millorar
                             
                             GeoCoder::encode($e->location,$e->address,$e);
                            

                        break;

                }
                switch($val){
                    case "Dates":
                    case "Data":
                            $status="dates";
                            break;
                    case "Horari":
                        $status="horari";
                            break;
                    case "Lloc de celebració":
                            $status="lloc";
                        break;

                }
            //$txt=trim ($n->nodeValue);
            }



        }
}

      