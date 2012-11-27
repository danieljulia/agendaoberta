<?php

 class Categorizer 
{
     
     //investigar aixo http://pecl.php.net/package/stem
     private  $uclassify;
     private static $classifiername="agendaoberta";
     
     public  function __construct(){
         //eliminar paraules curtes
         //quedar-se amb l'arrel de les paraules llargues (stemming)
         //http://stemmer-es.sourceforge.net/
         //http://tartarus.org/~martin/PorterStemmer/php.txt
         //en català (c) http://snowball.tartarus.org/algorithms/catalan/stemmer.html
         //per ex  musical, musiques, music  -> music
         // array( array('music','musiq'),'musica')
         //veure si hi ha match de les paraules amb les categories corresponents
         //etiquetar també amb aquella paraula p ex musica
         
         //primer buscar al titol (més importancia) i després primeres paraules del text
         
         $this->uclassify = new uClassify();
	
	// Set these values here
	$this->uclassify->setReadApiKey(Yii::app()->params['classifyReadApiKey']);
	$this->uclassify->setWriteApiKey(Yii::app()->params['classifyWriteApiKey']);
        
         
     }
     
     public function createClassifier(){
         $this->uclassify->create(self::$classifiername);
         
     }
     
      public function removeClassifier(){
         $this->uclassify->remove(self::$classifiername);
         
     }
     
     public function createClasses(){
         
         //get categories
         $cats=Category::model()->findAll();
         foreach($cats as $cat){
             print $cat->id;
             //get texts for this category
             $this->uclassify->addClass( $cat->id,self::$classifiername);

             /*
             $length=500;
                
             $events=Event::model()->forCategory($cat->id)->findAll();
             foreach($events as $ev){
                 $txt=htmlentities($ev->summary,ENT_QUOTES);
                 $desc=preg_replace('/\s+?(\S+)?$/', '', substr($ev->description, 0, $length));
                  
                 $txt.=htmlentities($desc,ENT_QUOTES);
                 //print "<br><br>".$txt;
               
             }*/
         }
     }
     
     /*
     public function untrain(){
         $this->uclassify->untrain(array()),$cat->id,self::$classifiername);
         
     }*/
     public function train($catid){
        //get categories
       //  $cats=Category::model()->findAll();
        // foreach($cats as $cat){
       
          
              $texts=array();  
             $events=Event::model()->forCategory($catid)->notClassified()->findAll();
             foreach($events as $ev){
                
                 //print "<br><br>".$txt;
                $texts[]=$this->constructText($ev);
             }
       
             
             if(count($texts)>0){
                 $this->uclassify->train($texts,$catid,self::$classifiername);
             }
       //  }
         
     }
     
     public function classify($ev){
         print "classifying...";
        return $this->uclassify->classify($this->constructText($ev),self::$classifiername);
        
        
     }
     
      public function classifyKeywords($ev){
         print "classifying...";
        return $this->uclassify->classifyKeywords($this->constructText($ev),self::$classifiername);
        
        
     }
     
     public function getInformation(){
        return $this->uclassify->getInformation(self::$classifiername);
         
     
     }
     
     private function constructText($ev){
         
         
          $txt=Yii::app()->format->text($ev->summary);
            
       // $txt=htmlentities(html_entity_decode ($ev->summary),ENT_QUOTES);
        $length=200;
        $desc=preg_replace('/\s+?(\S+)?$/', '', substr( ($ev->description), 0, $length));

        $txt.=" ".$desc;
        return $txt;
     }
     
     



}




