<?php


class GironaPageScraper extends Scraper
{
    
    /*
    public function findPhoto(&$e,$container=""){
         //imposar base path
            $this->basepath="http://cultura.gencat.cat/agenda/";
           
        parent::findPhoto($e,"//div[@class='contenidor']");
          
    }*/
    
    public function findDatesAndLocation(&$e){
   
         $res = $this->findXpath("//div[@class='descripcion']/p");
         $e->description=$res[0];
         parent::findDatesAndLocation($e);


        }
}
