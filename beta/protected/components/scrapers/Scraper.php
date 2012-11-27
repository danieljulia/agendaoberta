<?php
/**
 * Intenta fer un scrape d'una pàgina generìca
 * es crida des del parser
 * el punt d'entrada es el métode scrape al qual se li passa un objecte event
 * 
 *  
 */
class Scraper 
{

   /*
    * todo meta keys
    * //meta[lower-case(@name)='Keywords']/@content
    * 
    */
    
    protected $basepath;
    protected $html;
    protected $purified_html;
    protected $text;
    protected $text_separator;
    protected $container="";
    
    private $separator="<!-- -->"; //separa elements de l'html
    private $xpath;
    private $xpaths;
    private $uri;

    private $purifier;
    
    private $delimeters=array(
        //todo eliminar espais abans del punt
        "location"=>array(
            array("Lloc:","Adreça:","On:","Localització:","Localització :","Municipi:"),array("Adreça ","Lloc ","On ")),
        "date"=>array(
            array("Data:","Inici:"),array("Data ")
        ),
        "price"=>array(
            array("Preu:"),array("Preu ")
        ),
        "hour"=>array(
            array("Horari:","Hora:"),array("Horari ")
        ),
        "organizer"=>array(
            array("Organitza:","Organitzador:"),array("Organitza ")
        ),  
        "obs"=>array(
            array("Observacions:"),array("Observacions ")
        ),  
         "tef"=>array(
            array("Telèfon:"),array("Telèfon ")
        ), 
         "other"=>array(
            array("Temàtica:","Durada:","Hora:"),array("Temàtica ")
        ), 
    );
    
      public function scrape(&$e,$source){
        
       $this->xpaths=$source->getXpaths();
        
  
       //if title in xpath get from there
       if(isset($this->xpaths['title'])){
           $res=$this->findXPath($this->xpaths['title'],true);
 
                 $e->summary=$res;
           
       }
        //si no te titol extreure'l de la url
        if($e->summary=="") $e->summary=$this->getPageTitle();
        //todo
    
       
        //intentar extreure la informació de la descripció sense anar a l'html
        $this->preScrape($e);
        
      
       //todo, opcional a la font (per no sobrecargar servidor)
       $this->scrapeMD($e);
        
       //todo, opcional a la font (per no sobrecargar servidor)
       $this->scrapeMF($e);

        
       
       
       
        if( ($e->description==null ||  strlen($e->description)<20) || isset($this->xpaths['content'])){
            
   
                if(isset($this->xpaths['content'])){

                    $res=$this->findXPath($this->xpaths['content'],true);
               
                    if($res){
                      
                    
                        
                        $e->description=$this->purify($res);
                   
                        //buscar si la data esta aqui
                        if(!isset($this->xpaths['date'])){
                            
                            if($this->findDates($e, $e->description)){
                                $this->findHours($e, $e->description);
                            }
                        }
                   //div[@class='contingut']/node()     
                   
                        $this->findLocation($e, $e->description);
                     
                       
                    }else{
                       
                    
                    }
                }else{
                     $this->findDescription($e);
                }
        }
        
        
        
         //dates
       //
       if(isset($this->xpaths['date'])){
           $res=$this->findXPath($this->xpaths['date'],true);
        
           if( $this->findDates($e,$res)){
               $this->findHours($e,$res);
           }
  
       }
       
       //hour
       if(isset($this->xpaths['hour'])){
           $res=$this->findXPath($this->xpaths['hour'],true);
           $this->findHours($e,$res,false);

       }
       
       //buscar  foto
        if(isset($this->xpaths['photo'])){
             $res=$this->findXPath($this->xpaths['photo']);
        
            if(count($res)>0){
                //TODO falta normalitzar ruta..
              
                $foto=ScraperUtils::fixPath($e->url,$res[0]);
                $e->photo=$foto;
              
            }
        }else if(isset($this->xpaths['content'])){
            $this->findPhoto($e,$this->xpaths['content']);
            
        }else{
            $this->findPhoto($e);
        }
        

       //if location in xpath get from there
       
       if(isset($this->xpaths['location'])){
         
           $res=$this->findXPath($this->xpaths['location'],true);
            print "\r\n trobat location al xpath location: ".$this->xpaths['location']." *".$res."*";
             if($res){
                 
                 //extreure lloc: si cal...
                 if(!$this->findLocation($e,$res)){
                     $e->location=$res;
                 }
                // $e->location=  ScraperUtils::cutUntilZipCode($res);
             }
       }
       
       
       
        $this->findDatesAndLocation($e); 
        
        $this->findHours($e,$this->purify($this->text),true); 
      
    
     //desactivat , no funciona gaire be
      // $res=$this->parseFields($e,$e->description);
         
       // $res=$this->parseFields($e,$this->text_separator);

        
        return true;
     
    }
    
    public function init($uri){
        
        $this->uri=$uri;
        //guardar contingut
        $data=SimpleCurl::curl_download($uri,true);
        $this->html=$data->output;
        
        if(strlen($this->html)==0 || !$this->html){
            print "error, la url $uri is incorrect or is empty";
            return false;
        }
        
         
         
        //guardar dom per fer xpath
        $html = new DOMDocument();
      
        
        //http://stackoverflow.com/questions/1154528/how-to-force-xpath-to-use-utf8
        
        //todo-error aquest ´se el motiu pel qual a vegades es perden els accents!
        
        //ISO-8859-1 estava en UTF-8 però llavors elimina accents a vegades
        
        //cal detectar encoding
        
    $b=strpos($this->html,"<body");
        $chunk=substr($this->html,$b,5000);

        $enc=Utils::is_utf8( $chunk );
         $enc2=Utils::detect_encoding( $chunk );
        print "<br>*** PREVI encoding es utf8? ".$enc."*".$enc2;
        
        
        $isutf8=true;
        if($enc2=="windows-1251"){
            $isutf8=false;
        }
        
        
        //abans estava aixi.. cal testejar
       // if($data->charset=="utf-8"){
        if($isutf8){
                
           // $toenc="ISO-8859-1";
           //  $ok = mb_convert_encoding($this->html,"HTML-ENTITIES",$toenc); 
              $this->html = mb_convert_encoding($this->html,"HTML-ENTITIES","utf-8"); 
              
              print "<br><br>es utf-8";
        }else{
            
            $toenc="utf-8";
            $toenc="ISO-8859-1";
            
            $this->html = mb_convert_encoding($this->html,"HTML-ENTITIES","ISO-8859-1"); 
            
             print "<br><br>es ISO";
               
            
        }
        
        
        //sembla que a vegades cal codificar en utf8 perquè es vegi correcte!
        //
       $this->html=  utf8_encode($this->html);
        /*
        //$ok= mb_convert_encoding($this->html,"HTML-ENTITIES","UTF-8"); 
        if($ok==""){
            print "****** cagada pastoret";
             $ok= mb_convert_encoding($this->html,"UTF-8","ISO-8859-1"); 
        }
            $this->html=$ok;
       */
        
       
       /*
        $b=strpos($this->html,"<body");
        $chunk=substr($this->html,$b,5000);

        $enc=Utils::is_utf8( $chunk );
         $enc2=Utils::detect_encoding( $chunk );
        print "encoding es utf8? ".$enc."*".$enc2;
        */
       
       
         // $this->html = mb_convert_encoding($this->html,"HTML-ENTITIES",$toenc); 
        /*
        $b=strpos($this->html,"<body");
        $chunk=substr($this->html,$b,5000);
        $enc=mb_detect_encoding( $chunk );
      
        if($enc!="ASCII"){
            $toenc="UTF-8";
           
        }else{
            $toenc="ISO-8859-1";
        }
        */
        
     //todo a vegades falla.. i produeix mala codificació o pèrdua d'accents..etc!
        
        
        //if($enc!="UTF-8"){
           // $this->html = mb_convert_encoding($this->html,"HTML-ENTITIES",$toenc);
         //   }
    // $this->html=utf8_encode($this->html);
        
        //print $this->html;
      
        
        
        //remove new lines
        $this->html = preg_replace('|\n|u','<br/>',$this->html);	
        
   
        @$html->loadHtml($this->html);
        
          if(!$html){
            print "$uri gives error loading";
            return false;
        }
        
        $this->xpath = new DOMXPath( $html );
        $this->xpath->preserveWhiteSpace = true;
      
        
       
        //TODO val la pena conservar els tags?
        $this->text = strip_tags($this->purify($this->html));
        
        
         // $this->purified_html = $p->purify( $this->html);
        //TODO configurable
        $max_chars=Yii::app()->params['scraper_max_chars'];
        if($max_chars==null) $max_chars=50000;
                
        if(strlen($this->text)>$max_chars) $this->text=substr($this->text,0,$max_chars);

     
        /*abans estava aixi */
        //todo no elimina realment tot el javascript, potser cal aplicar XLST
        $res=$this->xpath->query("//div[@id='content']/text()");
        //sino hi ha id content tot el body
           
        if(count($res)<=1){
           // $res=$this->xpath->query("//body//*[not(self::script)]");
            $res=$this->xpath->query("//body//text()");
           // $res=$this->xpath->query("\\");
            //body//text()
        }
        $txt="";
        $i=0;
        foreach($res as $item){
            //print "<br>**".$item->nodeValue;
            
            $txt.=$this->separator.trim($item->nodeValue); //separar linies
            //TODO posar en config
            if(strlen($txt)>10000) break; //tallem en cert punt, a vegades hi ha molta porqueria
            $i++;
        }
        

        
        
        
        $this->text_separator = (strip_tags($this->purify($txt)));
        
        //print $this->text_separator ;
        
      //  $res=parse_url($uri);
        //print_r($res);
        /*
        if(is_array($res)){

            //todo cal millorar, aquesta no es la ruta real moltes vegades
            //per exemple a gencat no es http://cultura.gencat.cat/ sino http://cultura.gencat.cat/agenda/
            $this->basepath=$res['scheme']."://".$res['host']."/";
        }else{
            
            print "error...".$res." no sembla una uri";
        }
        */
        
        return true;
    }
    
    //abans d'anar a scrapejar l'html intentar deduir informació de la propia descripció de l'event!
    public function preScrape(&$e){
        
        if($e->description!=""){
             if(!isset($this->xpaths['date'])){
                 
                if( $this->findDates($e,$e->description)){
                    $this->findHours($e, $e->description);
                }
            }
             if(!isset($this->xpaths['location'])){
             $this->findLocation($e,$e->description);
            }
        }    
    }
    
    //intentar buscar valors a microformats
    public function scrapeMF(&$e){
        
        try{
            
    
        global $mF_roots;
      // $mF_roots=array();
       //require_once(Yii::app()->basePath ."/vendors/xmf_parser/defs/mfdef.hCalendar.php");
       
       // Yii::import('vendors.xmf_parser.mfdef.mF_roots.php');
       //print "*".$e->url."*";
      // print $this->purified_html;
       $xmfp = Xmf_Parser::create_by_HTML($mF_roots, $this->html);
       
       
        $mfs=$xmfp->get_parsed_mfs();
        
        if(isset($mfs['vevent'])){
            print "Microformats found<br/>";
            
            
            $ev=$mfs['vevent'][0];
            if( $e->summary==null) $e->summary=$ev['summary'];

            if(isset( $ev['dtstart'])){ 
                $start=strtotime($ev['dtstart']);


                $e->startdate=date("Y-m-d",$start);
                $e->starttime=date("H:i",$start);

                if(isset($ev['dtend'])){
                    $end=strtotime($ev['dtend']);
                    $e->enddate=date("Y-m-d",$end);
                    $e->endtime=date("H:i",$end);
                }

            }
            
            if(isset($ev['location'])){
                
                $loc=$ev['location'];

                //ScraperUtils::cutUntilZipCode($loc);
                $e->setLocation($loc);
                 $e->address=$loc;
            }
            if(isset($ev['geo'])){
                $e->geo_lat=$ev['geo']['latitude'];
                $e->geo_lng=$ev['geo']['longitude'];
                $e->geo_pre=9;//supossem que és força precís
                
            }
            
            //TODO url  i resta de camps que poden ser interessants
        }
             
         if(isset($mfs['vcard'])){
             print  "hi ha vcard";
           
         
             $v=$mfs['vcard'][0];
             
                 print_r($v);
                 
             $e->setLocation($v['adr'][0]['street-address'][0].", ".$v['adr'][0]['locality']);
             $e->setAddress($v['fn']);
             
         
             
         }
        
         /*
        echo('<h1>Results</h1><pre>');
        print_r( $xmfp->get_parsed_mfs() );
        echo('</pre>');
        echo('<h1>Errors</h1><pre>');
        print_r( $xmfp->get_errors() );
        echo('</pre>');
        */
        }catch(Exception $e){
            print "error in MF";
        }
        
    }
    
    //microdata
    public function scrapeMD(&$e){
        $md   = new MicrodataParser($this->html);
        $json = $md->getJson(); // Return JSON
        print "microdata\r\n";
        print_r($json);
        
    }
    //busca dates i location si estan marcades correctament semànticament
    public function findDatesAndLocation(&$e){

        //segon intent.. al text
        //todo depen de l'idioma
        if( !isset($e->startdate)){
             print "<br>***".$this->text; 
            
              if(!isset($this->xpaths['date'])){
            if(!$this->findDates($e,$e->description)){
                //buscar a tot el text
                $dates=$this->findDates($e,$this->purify($this->text));
            }else{
                $this->findHours($e, $e->description);
            }
            }
        }
     
        
        if(!isset($e->location)){
        //   if(! $this->findLocation($e,$e->description)){
                $this->findLocation($e,$this->text);
        //   }
               
            
        }
    }
    
    //todo
    //guardar temporalment en local i fabricar miniatura..
    //http://www.yiiframework.com/extension/image/
    //
    
    
    //cercar una foto representativa en general
    public function findPhoto(&$e,$container=""){
        //ex //div[@class='contenidor']//img/@src  retorna llista de imatges dins d'aquest contenidor
  
        //criteris per decidir que és representativa:
        //jpg enlloc de gif
        //ruta relativa enlloc d'absoluta
        //que tingui alt
        
        $url_photo="";
        
        $nodes=$this->findXPath($container."//img/@src");
        if(!$nodes ) return false;
        
        $precandidates=array();
        $candidates=array();
        $i=0;

        foreach ($nodes as $src){
            $ext=strtolower(ScraperUtils::getExtension($src));
            
            if($ext=="jpg" ||$ext=="bmp"){     

              if(!$this->isRelative($src)){
                
                   $precandidates[]=$src;
              } else{
                  $candidates[]=$src;
              }
            }
            $i++;
        }
        //todo refinar
        if(count($candidates)>=1){
            //de moment tornar el primer
            $url_photo= $this->basepath.$candidates[0];
            
        }else{
            //sino retornar el primer jpg
            if(count($precandidates)>=1){
                //de moment tornar el primer
               $url_photo=  $precandidates[0];
            
            }
        }


        if($url_photo!=""){
     
            $url_photo=ScraperUtils::fixPath($e->url,$url_photo);
            $e->photo=$url_photo;
        }
       
    }
    
    public function setAbsoluteUrl($base,$uri){
        
        //substituir rutes relatives si cal
            if(strpos($uri,"/")==0 ){
                $uri= $base.$uri;
                
            }else{
                //$path=  ScraperUtils::getPathFromUrl($e->url);
                $uri=ScraperUtils::replacePathPoints($base,$uri); //abans era path
            }
            
         return $uri;
    }
    //intentar extreure dates d'un text qualsevol
    public function findDates(&$e,$str){
         $real_months=array("gener","febrer","març","abril","maig","juny",
           "juliol","agost","setembre","octubre","novembre","desembre");
         
         
        //TODO revisar si es decisio correcta
        
        if( $e->startdate!=null){
          
            return false;
        }
            
    
        
        if($str=="") return;
        
        $str=  strtolower($str);
        //eliminar dobles espais
        $str = preg_replace('/\s+/', ' ',$str);
        
        $matches=array();
   
     
      
          // 03/05/1967 10:30 pm
       if (preg_match_all('/[0-9]{2}\/[0-9]{2}\/[0-9]{4}\s[0-9]{1,2}:[0-9]{2} pm/',$str,$matches)){  
         
          
           $i=0;
           $st=0;
           
           
           foreach($matches[0] as $dat){
               
           
               
               
               // setlocale(LC_TIME, "spanish");
               //canviar ordre dia i mes
               $dat = preg_replace("/(\d+)\D+(\d+)\D+(\d+)/u","$3-$2-$1",$dat);
               
               $t=strtotime($dat);
               $r=getdate($t);
  
               if($i==0){
                   $st=$t;
                   if( $e->startdate==null) $e->startdate=date("Y-m-d",$t);
                   if( $e->starttime==null)$e->starttime=date("H:i",$t);
               }
                if($i==1){
                    if($t<$st){
                        //en teoria no es posible
                        $t+=60*60*12; //sumar 12 h.. potser es pel format am pm..?
                        if($t<$st){
                            //si continua sent més petit.. es incorrecte, ignorar
                            return true;
                            
                        }
                    }
                   if( $e->enddate==null)$e->enddate=date("Y-m-d",$t);
                   if( $e->endtime==null)$e->endtime=date("H:i",$t);
               }
               $i++;
           }
           print "date format found: 03/05/1967 10:30 pm <br>";
           return true;
       }
       
       
        // 03/05/1967 10:30
       if (preg_match_all('/[0-9]{2}\/[0-9]{2}\/[0-9]{4}\s[0-9]{1,2}:[0-9]{2}/',$str,$matches)){  
         
          
           $i=0;
           $st=0;
           
           
           foreach($matches[0] as $dat){
               // setlocale(LC_TIME, "spanish");
               //canviar ordre dia i mes
               $dat = preg_replace("/(\d+)\D+(\d+)\D+(\d+)/u","$3-$2-$1",$dat);
               
               $t=strtotime($dat);
               $r=getdate($t);
  
               if($i==0){
                   $st=$t;
                   if( $e->startdate==null) $e->startdate=date("Y-m-d",$t);
                   if( $e->starttime==null)$e->starttime=date("H:i",$t);
               }
                if($i==1){
                    if($t<$st){
                        //en teoria no es posible
                        $t+=60*60*12; //sumar 12 h.. potser es pel format am pm..?
                        if($t<$st){
                            //si continua sent més petit.. es incorrecte, ignorar
                            return true;
                            
                        }
                    }
                   if( $e->enddate==null)$e->enddate=date("Y-m-d",$t);
                   if( $e->endtime==null)$e->endtime=date("H:i",$t);
               }
               $i++;
           }
           print "date format found: 03/05/1967 10:30 <br>";
           return true;
       }
          // 1987/05/19 12:30
       if (preg_match_all('/[0-9]{4}\/[0-9]{2}\/[0-9]{2}\s[0-9]{1,2}:[0-9]{2}/',$str,$matches)){  
           //todo
           print "date format found: 1987/05/19 12:30<br>";
           
           return true;
       }
       
       
       
       
       
       if (preg_match_all('/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/',$str,$matches)){  
       
           $i=0;
           $st=0;
         
            foreach($matches[0] as $dat){
              $dat = preg_replace("/(\d+)\D+(\d+)\D+(\d+)/","$3-$2-$1",$dat);
                $t=strtotime($dat);
                $r=getdate($t);
                

                if($i==0){
                    $st=$t;
                     if( $e->startdate==null) $e->startdate=date("Y-m-d",$t);
                }
                if($i==1){
                      if( $e->enddate==null) $e->enddate=date("Y-m-d",$t);
                }
                $i++;
            }
            //faltaria buscar la hora!
            //todo
             print "date format found:  2/2/4<br>";
           return true;
       }
       
       //format 15.08.2012 
         if (preg_match_all('/[0-9]{2}.[0-9]{2}.[0-9]{4}/',$str,$matches)){  
       
           $i=0;
           $st=0;
         
            foreach($matches[0] as $dat){
              $dat = preg_replace("/(\d+)\D+(\d+)\D+(\d+)/","$3-$2-$1",$dat);
                $t=strtotime($dat);
                $r=getdate($t);
                

                if($i==0){
                    $st=$t;
                     if( $e->startdate==null) $e->startdate=date("Y-m-d",$t);
                }
                if($i==1){
                      if( $e->enddate==null) $e->enddate=date("Y-m-d",$t);
                }
                $i++;
            }
            //faltaria buscar la hora!
            //todo
             print "date format found:  15.08.2012<br>";
           return true;
       }
       
       
       //  del 28/05/12 al 29/06/12 
       if (preg_match_all('/[0-9]{2}\/[0-9]{2}\/[0-9]{2}/',$str,$matches)){  
         
           
           $i=0;
           $st=0;

           foreach($matches[0] as $dat){
               
               //canviar ordre dia i mes
               $dat = preg_replace("/(\d+)\D+(\d+)\D+(\d+)/","$3-$2-$1",$dat);
               
               $t=strtotime($dat);
               $r=getdate($t);
  
               if($i==0){
                 
                   if( $e->startdate==null) $e->startdate=date("Y-m-d",$t);
                
               }
                if($i==1){
                    
                    if( $e->enddate==null)$e->enddate=date("Y-m-d",$t);
                  
               }
               $i++;
           }
           print "date format found:  28/05/12 al 29/06/12 <br>";
           return true;
       }
       

  
        //  20 / octubre / 2012 
     
       
        //la u causa que no es detecti a vegades!
       
       print "******** buscant a $str";
       if (preg_match_all('/[0-9]{1,2} \/ [a-z]* \/ [0-9]{4}/',$str,$matches)){ 
           ///[0-9]{2} \/ {1,10} \/ [0-9]{4}/
         
        
           foreach($matches[0] as $dat){
               $r=explode("/",$dat);
               
               $m=-1;
               
               for($i=0; $i< count($real_months); $i++) {
                   print $real_months[$i]." *".$r[1]."*<br/>";
                    if($real_months[$i] == trim($r[1])) $m=$i;
                }
                if( $m != -1){
                    $t=strtotime($r[2]."-".($m+1)."-".$r[0]);
                    $e->startdate=date("Y-m-d",$t);
                  
                        print "date format found: 20 / octubre / 2012 <br>";
                    return true;
                }
           }
       }
       
      
       if (preg_match_all('/[0-9]{2}\-[0-9]{2}\-[0-9]{4}/',$str,$matches)){  
         
          
           $i=0;
           $st=0;

           foreach($matches[0] as $dat){
               
               //canviar ordre dia i mes
               $dat = preg_replace("/(\d+)\D+(\d+)\D+(\d+)/","$3-$2-$1",$dat);
               
               $t=strtotime($dat);
               $r=getdate($t);
  
               if($i==0){
                 
                   if( $e->startdate==null) $e->startdate=date("Y-m-d",$t);
                
               }
                if($i==1){
                    
                    if( $e->enddate==null)$e->enddate=date("Y-m-d",$t);
                  
               }
               $i++;
           }
            print "date format found:  28-05-2012 al 29-05-2012 <br>";
           return true;
       }
       
     
       
       $months=array("de gener","de febrer","de març","d'abril","de maig","de juny",
           "de juliol","d'agost","de setembre","d'octubre","de novembre","de desembre");
       
     
       
       if( !$this->parseShortDates($e,$months,$str)){
     
       
           
            if($this->parseShortDates($e,$real_months,$str)){
                 print "found date:  xx  month";
                return true;
            }else{
                
                print "buscant en format resumit...";
                
                $months=array("gen.","feb.","mar.","abr.","mai.","jun.",
           "jul.","ago.","set.","oct.","nov.","des.");
                
                 if($this->parseShortDates($e,$months,$str)){
                    print "found date:  xx  .xxx";
                    print "trobat a $str";
                    return true;
                }else{
                        print "unable to found dates";

                }
          
            }
                    
       }else{
           print "found date:  xx de month";
           return true;
       }
     
       print "no date format found<br> ";
       
       
       return false;
    }
    
    
    public function parseShortDates(&$e,$months,$str){
       
      
       $str=trim($str);
      
       
        $c=0;
        $i=0;
        //TODO mirar que passa si hi ha més de 2 ocurrencies
        //i al final vol dir case insensitive
        foreach($months as $m){
        
            $exp='/[0-9]{1,2} '.$m.'/i';  
            // si afegeixo unicode (ui) a vegades no funciona!
        

            
            if (preg_match_all($exp,$str,$matches)){
 //PREG_PATTERN_ORDER               
               
           
                //lloc (preg_match("/\blloc\b/i", " lloc blah
                foreach($matches[0] as $dat){
                        
                    $chunk=explode(" ",$dat);
                    
                    $num=$chunk[0];
                    $year=date("Y");
                    $month=($i+1);
                    if($i<9) $month="0".$month;
                    $t=$year."-".$month."-".$num;
                  
                    if($c==0){
                        $e->startdate=date("Y-m-d",strtotime($t));
               
                    }else if($c==1){
                        $e->enddate=date("Y-m-d",strtotime($t));
                    }
                    $c++;
                    
                  

                }
                return true;
            }
            $i++;
        }
        return false;
        
    }
    
      //TODO mirar tarda i mati
     public function findHours(&$e,$str,$strict=true){
        if($str=="") return;
        
        
        //TODO el fet d'afegir u espatlla la detecció!
        
        //TODO eliminar espais per fer més match
        $str = str_replace(array(' ','&nbsp;'),'',$str);

        
        $matches=array();
        
        $valid=false;
        
   //buscar si hi ha la hora en aquest format..
        // 21:00 h
        if (preg_match_all('/\d{2}:\d{2}h/i',$str,$matches)){  
         
           print "trobada hora  en format 20:00 h";
           $i=0;
           $st=0;

           foreach($matches[0] as $dat){
                if($this->hourValid($dat)) $valid=true;
                   
            
               if($valid){
                if($i==0){

                    if( $e->starttime==null) $e->starttime=$dat;


                }
               }
               
           }
           if($valid) return true;
          }    
       
          
    
       //20:00
       if (preg_match_all('/\d{2}:\d{2}/i',$str,$matches)){ 
             print "he trobat hora de format xx:xx";
             $i=0;
             foreach($matches[0] as $dat){
                  if($this->hourValid($dat)) $valid=true;
                      if($valid){
                  
                        if($i==0)

                            if($e->starttime==null) $e->starttime=$dat."";
                        if($i==1)
                            if($e->endtime==null) $e->endtime=$dat."";
                        $i++;
                      }
             }
                if ($valid) return true;
        }
          
                //21.00 a 02.00 h
          print "buscant la hora a $str";
       if (preg_match_all('/\d{1,2}\.\d{1,2}/i',$str,$matches)){ 
             print "he trobat hora de format xx.xx";
             $i=0;
             foreach($matches[0] as $dat){
                 $dat=str_replace(".",":",$dat);
                
            if($this->hourValid($dat)) $valid=true;
            if($valid){       
                     
                
                    if($i==0)
             
                        if($e->starttime==null) $e->starttime=$dat."";
                    if($i==1)
                        if($e->endtime==null) $e->endtime=$dat."";
                        
                        $i++;
                 }
             }
            
              if ($valid) return true;
        }
        
             // 21h
        print "buscant aqui..";
        
        if (preg_match_all('/[0-9]{1,2}h/i',$str,$matches)){  
         
           print "trobada hora  en format 20h";
           $i=0;
           $st=0;

           foreach($matches[0] as $dat){
               if($i==0){
                  $res=explode("h",$dat);
                  $h=  $res[0].":00";
                   if($this->hourValid($h)) $valid=true;
                  if($valid){
                   if( $e->starttime==null) $e->starttime=$h;
                  }
                
               }
               
           }
           if ($valid) return true;
          }  
          
          
      
        
        
        if($strict){
          print "no s'ha trobat la hora en mode estricte";
            return false;
        }
      
        
        
      
          
          
            //23
       if (preg_match_all('/\d{2}/i',$str,$matches)){ 
             print "he trobat hora de format xx";
             $i=0;
            
             
             foreach($matches[0] as $dat){
                 $h=$dat.":00";

                 if($this->hourValid($h)) $valid=true;
                 if($valid){
                     
               
                if($i==0){
                    if($e->starttime==null) $e->starttime=$h;
                    print "he insertat la data a ".$e->starttime;
                }    
                if($i==1){
                    if($e->endtime==null) $e->endtime=$h;
                
                }    
                $i++;
             }
             }
               if($valid) return true;
        }
        
       //4
       if (preg_match_all('/\d{1}/i',$str,$matches)){ 
             print "he trobat hora de format x";
             $i=0;
             
             foreach($matches[0] as $dat){
                 $h=$dat.":00";
                  if($this->hourValid($h)) $valid=true;
                  
                  if($valid){
                      
                if($i==0)
                    if($e->starttime==null) $e->starttime=$dat.":00";
                if($i==1)
                    if($e->endtime==null) $e->endtime=$dat.":00";
                $i++;
                  }
             }
                if ($valid) return true;
        }
          
        print "no ha trobat hora en cap format";
        return false;
       
      }
      
      
      public function hourValid($h){
          $r=explode(":",$h);
          if(intval($r[0])>24){
              return false;
          }
          if(intval($r[1])>59){
              return false;
          }
          return true;
      }
      
    public function findLocation(&$e,$str){
      print "<br><br><br>buscant location a $str<br><br><br>";
        /* disabled
        $res=$this->findXPath("//*[@class='location']");
        if(count($res)>0){
            $e->setLocation(($res[0]));
               print "<br>findLocation as class=location ".$loc;
            return true;
        }
        */

        
        if( $this->findDelims("location",$e,$str) ){
            print "he trobat location.. ".$e->location;
            
            $e->location=  ScraperUtils::purgeLocation($e->location);
            
             print "location purgada ".$e->location;
             return true;
        }else{
            print "no s'ha trobat location";
            return false;
        }
      
    }
    
  
    
  
    
    protected function getNextChunk($str,$text){
        $str=strip_tags ($str);
        $str=  strtolower($str);
        
        $res=explode($str,$text);
        if(count($res)>1){
           
            $res2=explode($this->separator,$res[1]);
            foreach($res2 as $chunk){
                if($chunk!="") return $chunk;
            }
        }
        return "";
    }

    //intentar extreure hora d'una expressió tipus " Dimecres a les 20:15 h"
    public function extractTime($str){
      
       //if (preg_match('/\b\d{2}:\d{2}\b/',$str,$matches)){  //funciona be pero nomes extreu una data
       if (preg_match_all('/\d{2}:\d{2}/u',$str,$matches)){  //funciona be pero nomes extreu una data

           return $matches[0];
           
       }
        return false;
    }
    
    /*
    //assumim que segur que és a str
    public function extractAssumedLocation(&$e,$str){
       //extreure lloc: o lloc si està abans
        $loc=$str;
        $res=explode("Lloc: ",$str);
        if(count($res)>1){
            $loc=$res[1];
        }else{
             $res=explode("Lloc ",$str);
             if(count($res)>1){
                $loc=$res[1];
             }
        }
       $loc= ScraperUtils::cutUntilZipCode($loc);
       $e->location= $loc;
        
    }*/
    //retorna un array amb dues dates si les troba en un text de l'estil de Del 01/10/2011 al 31/05/2012
    public function scrapeDatesCat($val){
        $val=utf8_decode($val);
       
        
        $val=str_replace("Del", "", $val);
        $val=str_replace("De l'", "", $val);
        $val=str_replace("al", "", $val);
        $val = preg_replace('/\s+/u', ' ', ($val));
        $val=trim($val);

        $dates=explode(" ",$val);
        $isodates=array();

        
      
        
         foreach($dates as $date){
            $date = preg_replace("/(\d+)\D+(\d+)\D+(\d+)/u","$3-$2-$1",$date);
             
            $t=strtotime($date);
          
            $isodates[]=date('Y-m-d',$t);
        }
        $status="";

        $res=array();
           
        $res[0]= $isodates[0];
        if(count($dates)>1) $res[1]= $isodates[1];
        
        Yii::log($val." ".$dates,"info","ao.scraper");
        
     
        
        return $res;
        
    }
     
    public function scrapeTimesCat($val){
        //imaginem "de 10 a 20"
        $val=  strtolower($val);
        $val=str_replace("de", "", $val);
        $res=explode("a",$val);
        
      
        
        $times=array();
        if(count($res)>1){
            $times[0]=trim($res[0]);
         
            if(strlen($times[0])<=2) $times[0].=":00";
            $times[1]=trim($res[1]);
            if(strlen($times[1])<=2) $times[1].=":00";
        }
        
        return $times;
         
         
     }
    /*
    public function test2(&$e){
        $res=$this->findXPath("//div[@class='contenidor']/div[2]/div[2]");
        
        $splitby = array('Data','Lloc de celebració','Entitat organitzadora');
   
        $pattern = '/\s?'.implode($splitby, '\s?|\s?').'\s?/';
        $result = preg_split($pattern, $res[0], -1, PREG_SPLIT_NO_EMPTY);
        print_r($result);
        
        $dates=$this->scrapeDatesCat($result[0]);
        print_r($dates);
        return;

        $i=0;
        print_r($res);
        $data=explode("Data",$res[0]);
        $data=explode("Horari",$data);
        $data=explode("Lloc de celebració",$data);
        print "<br/><br/>";
        print_r($data);
        return;
        foreach($res as $item){
            print "<br/>$i".$item;
            $i++;
        }
        $e->location="ok";
         
    }
    */
    
    protected function getPageTitle(){
      
  
        
           //sino hi ha. titol doc
        $res=$this->findXPath("//title/text()");
     
        if($res){
         return $res[0];
        }else{
            return "";
        }
        
       //buscar el primer h1
        $res=$this->findXPath("//h1");
        if(count($res)>0){
            return $res[0];
        }
        
        //sino buscar el primer h2
         //buscar el primer h1
        $res=$this->findXPath("//h2");
        if(count($res)>0){
            return $res[0];
        }
        
     
        
    }
    
    
    //métodes auxiliars todo potser separar
    public function findXPath($xpath,$join=false){
        try{
            /*if(!isset($this->xpath->query)){
                print "no funciona xpath";
                return false;
            }*/
            $nodelist = $this->xpath->query( $xpath );

            $nodearr=array();
            foreach ($nodelist as $node) {
                // you can add here a special conditions, as searching for
                // regular expression matches in your nodes names/values/attributes
                // which can not be achieved with XSLT 1.0
                
                //$nodearr[] = trim($node->nodeValue," \t.");
                //$node->normalize();
                
                 $nodearr[] = trim((($node->textContent))," \t.");;
                 
                 //print_r($node->childNodes);
                 /*
               foreach( $node->childNodes as $ch){
                   print $ch->nodeValue."   ";
                   
               }*/
            }

           if($join){
               $res="";
               $c=0;
               foreach($nodearr as $n){
                   $res.=" \r\n".$n;
                   $c++;
               }
               if($c==0) return false;
               return $res;
           }
                
            return $nodearr;
        }  catch (Exception $e){
            print "error a findXPath";
            return false;
        }
    }
    
    //TODO només en català de moment
    public function parseFields($e,$txt){

          //TODO ordenar per més fiables a menys.. agafa el primer que troba
        $strs=array("global"=>"general","descripció"=>"desc","lloc"=>"loc","localització"=>"loc","adreça"=>"loc","telèfon"=>"tef","web"=>"web","hora"=>"hour","horari"=>"hour","dates"=>"dates","data"=>"dates","on"=>"loc","marc","telèfon"=>"tef","*"=>"general","organització"=>"general");
        
      
        $res=ScraperUtils::parseStrings($txt,$strs,":");
        
        $this->filterFields($e,$res);

        //TODO separator en config?
         $res=ScraperUtils::parseStrings($txt,$strs,"\r");
         $this->filterFields($e,$res);
     
        return $res;
        
    }

    public function filterFields($e,$res){
           foreach($res as $key=>$val){
            if($strs[$key]=="loc"){
                print "parse: find loc ".$val[0];
                if($e->location=="")
                    $e->setLocation($val[0]);
                print "<br><br>parse fields found location :".$e->location."<br>";
                    $e->address= $e->location;
            }
             if($strs[$key]=="dates"){
                print "he trobat les dates aqui... ".$val[0];
                $this->findDates($e,$val[0]);
                
            }
            if($strs[$key]=="hour"){
                print "buscant les hores aqui.. ".$val[0];
                $this->findHours($e,$val[0],false);
                
            }
            if($strs[$key]=="descripcio"){
                if($e->description==null){
                    $e->description=$this->purify($val[0]);
                }
                    
                
            }
        }
        
    }
        public function findDescription(&$e){
            
          
            //intents a la "desesperada" de trobar la descripció
            
            if($this->findDescContent($e,"//*[@class='documentDescription']")!=""){
                return true;
            }
            
             if($this->findDescContent($e,"//div[@class='txt-contingut']")!=""){
                return true;
            }
            if($this->findDescContent($e,"//div[@class='continguts']")!=""){
                return true;
            }
            
            if($this->findDescContent($e,"//div[@class='vevent']")!=""){
                return true;
            }
            
              if($this->findDescContent($e,"//div[id='continguts']")!=""){
                return true;
            }
            if($this->findDescContent($e,"//div[@id='content']")!=""){
                return true;
            }
        
            if($this->findDescContent($e,"//div[@class='DetallEsdeveniment']")!=""){
                return true;
            }
        
               //fer un match d'una classe si va acompanyada d'altres
            //http://stackoverflow.com/questions/1390568/xpath-how-to-match-attributes-that-contain-a-certain-string
              //potser la resta també hauria de ser així  
             if($this->findDescContent($e,"//*[contains(concat(' ', @class, ' '), ' event-detail')]")!=""){
                return true;
            }
        
             if($this->findDescContent($e,"//div[@class='text']")!=""){
                return true;
            }
            //massa especific? Mora d'Ebre
          if($this->findDescContent($e,"//div[@id='content_main2']")!=""){
                return true;
            }
           
            
           if($this->findDescContent($e,"//h3")!=""){
                return true;
            }
           
            
        }
        
        private function findDescContent(&$e,$xpath){
             $res=$this->findXPath($xpath);
            if(count($res)>0){
               $e->description= $this->purify($res[0]); 
               
               //print "ha trobat descripció amb xpath ".$xpath;
               return true;
            }
            return false;
        }
        
    private function purify($str){
       
       $str=Yii::app()->purifier->purify($str);
        
        //atenció a vegades sembla que borra text
        /*
         $str=str_replace("\r\n"," ",$str);
         $str=str_replace("\r"," ",$str);
         //remove multiple spaces
         $str = preg_replace('/\s+/', ' ',$str);
         */
         

        return $str;
        
        
        /*
        if($this->purifier==null){
            $this->purifier = new CHtmlPurifier();
            $this->purifier->options = array('URI.AllowedSchemes'=>array(
            'http' => true,
            'https' => true,
            ));
            
        }
        return $this->purifier->purify($str);
        */
        
    }    
        
 
    private function isRelative($file){
        if($this->startsWith($file,"http://")) return false;
        return true;
    }
    private  function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
    
    private function findDelims($label,&$e,$str){
         
        $enders=array();
        foreach($this->delimeters as $key=>$value){
            if($key!=$label){
                foreach($value as $d){
                        array_push($enders, $d[0]);   
                }
            }
        }
     
        foreach($this->delimeters[$label] as $delims){
            $res=ScraperUtils::multipleExplode($delims,$str);
            if(count($res)>1){
                $res=ScraperUtils::multipleExplode($enders,$res[1]);
                $e->location=$res[0];
                return true;
                break;
            }
        }

        
        return false;
    }
    /*
    private function HTML2TEXT($Document) {
        $Rules = array ('@<script[^>]*?>.*?</script>@si', // Strip out javascript
                        '@<[\/\!]*?[^<>]*?>@si',          // Strip out HTML tags
                        '@([\r\n])[\s]+@',                // Strip out white space
                        '@&(quot|#34);@i',                // Replace HTML entities
                        '@&(amp|#38);@i',                 //   Ampersand &
                        '@&(lt|#60);@i',                  //   Less Than <
                        '@&(gt|#62);@i',                  //   Greater Than >
                        '@&(nbsp|#160);@i',               //   Non Breaking Space
                        '@&(iexcl|#161);@i',              //   Inverted Exclamation point
                        '@&(cent|#162);@i',               //   Cent 
                        '@&(pound|#163);@i',              //   Pound
                        '@&(copy|#169);@i',               //   Copyright
                        '@&(reg|#174);@i',                //   Registered
                        '@&#(d+);@e');                   // Evaluate as php
        $Replace = array ('',
                        '',
                        '1',
                        '"',
                        '&',
                        '<',
                        '>',
                        ' ',
                        chr(161),
                        chr(162),
                        chr(163),
                        chr(169),
                        chr(174),
                        'chr()');
        return preg_replace($Rules, $Replace, $Document);
    }
    */
}
