<?php

class GuiaBcnScraper extends Scraper
{
    
    public function findPhoto(&$e){
         $this->basepath="";
         parent::findPhoto($e,"//div[@id='contenidor']");
         
        
    }
    public function findDatesAndLocation(&$e){

        /*
        amb guia bcn funciona aquest

        //div[@class='div-detall-1']/dl[1]/dd[2]

        i retorna coses com Del 10/10/2011 al 11/06/2012

        i el lloc //div[@class='div-detall-1']/dl[1]/dd[1]
         * */
         
        
        //text
       
        $resum=$this->findXPath("//div[@id='resum']/p");
        $text=$this->findXPath("//div[@id='texte']/p");
        $comments=$this->findXPath("//p[@class='comments']");
        
        if(count($resum)>0)
            $e->description.=$resum[0];
        if(count($text)>0)
            $e->description.=$text[0];
        if(count($comments)>0)
            $e->description.=$comments[0];
            
        if($e->description==""){
            $e->description=$this->findXPath("//div[@class='div-detall-1']/node()",true);
            
        }
        //div[@class='contenidor']/div[2]/div[2]/div[3]
         
        //foto
      
     
        $xpath="//div[@class='div-detall-1']/dl[1]/dd[2]";
        $res=$this->findXPath($xpath);

        if(count($res)>0){
          //  $dates=$this->scrapeDatesCat($res[0]);
           parent::findDates($e,$res[0])    ; 
           
           /*
            if(isset($dates[0])){
                    $e->startdate=$dates[0];
            }
                if(isset($dates[1])){
                    $e->enddate=$dates[1];
            }*/
        }
       //deduir horari
       //
       $res=$this->findXPath("//div[@id='horari']/node()",true);
       
       $e->schedule=$res;
       
       //si només hi ha startdate intentar deduir la hora 
       $times=$this->extractTime($e->schedule);
  
       if(count($times)==1) $e->starttime=$times[0];
        if(count($times)==2){
            $e->starttime=$times[0];
            $e->endtime=$times[1];
        }
        
       /*
        * 
        ON:

        //div[@id='contenidor-2']/div[@id='col-detall-0']/div[@class='div-detall-1']/dl[1]/dd[1]/a/span[@class='notranslate']


        //div[@id='contenidor-2']/div[@id='col-detall-0']/div[@class='div-detall-1']/dl[1]/dd[1]
    //div[@class='div-detall-1']/dl[1]/dd[1]
        * 

        Adreça

        /html/body/div[@id='marc-web']/div[@id='contenidor']/div[@id='contenidor-2']/div[@id='col-detall-0']/div[@class='div-detall-1']/dl[@class='adreca']/dd[1]/span[@class='notranslate']

        adreça  //dl[@class='adreca']/dd[1]
        districte //dl[@class='adreca']/dd[2]  
        zona //dl[@class='adreca']/dd[3]  
        codi postal //dl[@class='adreca']/dd[4] 

        exemple
        adreça + codi postal

        C Palau de la Música, 2 ,08003

        podria ser que ja geocodifica bé


        El nom es pot guardar..

        on + adreça + codi postal
        */
       
       $on=$this->findXPath("//div[@class='div-detall-1']/dl[1]/dd[1]");
       if(count($on)>0){
           $on=$on[0].", ";
       }else{
           $on="";
       }
        
        
       $ad=$this->findXPath("//dl[@class='adreca']/node()");
       
       $adreca=($ad[1]);
       
       
       $districte=utf8_decode($ad[3]);
       $zona=$ad[5];
       $cp=$ad[7];
       
       
       $e->setAddress($on.$adreca.", ".$districte);
       $e->setLocation(($adreca).", ".$cp);
       

       
       /*$on=$this->findXPath("//div[@class='div-detall-1']/dl[1]/dd[1]");
       $adreça=$this->findXPath("//dl[@class='adreca']/dd[1]");
       $codipostal=$this->findXPath("//dl[@class='adreca']/dd[4]");
       */
 
      /* if(count($on)>0){
        $e->setAddress($on[0]);

        //aquestes dades van molt bé per si soles per geolocalitzar
        if($adreça && $codipostal){
           
             $e->setLocation($adreça[0]."; ".$codipostal[0]);
            
                GeoCoder::encode($e->location,$e->address,$e);
            }
   
       }
       */
       
       
        //afegir a la descripció
        $more_description=$this->findXPath("//p[@class='comments']");
        if(count($more_description)>0){
            $e->description.=$more_description[0];
        }
        //
        //
       //print $on[0].$adreça[0].$codipostal[0];
       
       //todo, assignar starttime si es un event puntual
                
        //parent::findDatesAndLocation($e);
        
    }
      
    
    
}
