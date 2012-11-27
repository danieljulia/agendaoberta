<?php

/**
 * xpath del model ha de permetre extreure una llista de links a les pàgines úniques del contingut 
 */
class PageParser extends Parser
{
    
        public function parse($num=null){

            $this->html=SimpleCurl::curl_download($this->source->getFeed());
            
         
            
            $html = new DOMDocument();
            @$html->loadHtml($this->html);
            $xpath = new DOMXPath( $html );
            
           // print $this->html;
            
            $xpaths=$this->source->getXPaths();
          
            $res=$xpath->query($xpaths['page']);
 
          
            
            $basedoc="";
            $baseres=$xpath->query("//base/@href");
            foreach($baseres as $node){
                $basedoc=$node->nodeValue;     
           
            }
        
            $path=ScraperUtils::dirName($this->source->getFeed());
            $basepath=ScraperUtils::basePath($this->source->getFeed());
 
            $c=0;
 
          
        
            
            foreach($res as $item){
       
                //todo millorar la substitució del path base
                $uri=$item->nodeValue;

                
                 //eliminar jsession...
               // ;jsessionid=
                $uri = preg_replace("/;jsessionid=.*?(?=\\?|$)/", "",$uri);
     
                //comprovat funciona correctament amb Mataró que te links tipus ../../../,,
               // print "buscant el path real a partir de $uri i de $path";
               // $realpath=$this->truepath($uri,$path);
   
          
                //si hi ha ../ substituir al path
                
                if( !(strpos($uri,"../")===false) || strpos($uri,"../")===0 ) {
                    
                    
                     $realpath=ScraperUtils::url_to_absolute($path,$uri);
                    
                    //$realpath=ScraperUtils::replacePathPoints($path,$uri);
                    /*
                    $p=$path.$uri;

                    $realpath=$this->cleanPath($p);
                    //si no funciona nomes activar si hi ha ..

                    if(strpos($realpath,"http://")===false){
                        $realpath=str_replace("http:/", "http://", $realpath);
                    }
                     */
 
                  
                }else{
                    
                  
                
                    if($basedoc!=""){
                        $uri=str_replace(array('./'),$basedoc,$uri);
                    }else{
                        $uri=str_replace(array('./'),$basepath,$uri);
                    }

                    /* comentat.. pot ser que deixin de funcionar agendes anteriors revisar!! */

                    
                    $realpath="";
                   
                    if(strpos($uri,"http://")===0){
                        $realpath=$uri;
                    }else{
                  print "fent aixo ";
                        $realpath=$basepath.$uri;
                    }
                
                
                }
                
              
                //si realpath es ok deixar-la
               //compte no canviar poden fallar altres
                //if(strpos($realpath,"http://")===0){
                //    }else{
                 $realpath=  ScraperUtils::fixPath($path, $uri);
               //  }
             
              
                
                if($realpath=="") {
                    
                    return false;
                    
                }
                
                if($this->isValidUrl($realpath)){
                    
                
               
                $e=EventItem::getCache($realpath);
                if($e==null){
                    $e=new EventItem($realpath);
                }else{
                  //recuperat de la cache
                }   
                $this->items[]=$e;
                $c++;
                if($c==$num) break;
               
                   //div[@class='pres_agenda_resultats']/table[@class='taula_hover']/tbody/tr[*]/td[@class='celes']/a[@class='enllac']/@href
                
                }else{
                    print "url not valid: $realpath";
                    
                }
                
                
            }
               
            if($c==0){
                
                print "Unable to get items with this url: ".$this->source->getFeed()." and xpath ".$xpaths['page']."<br>";
            }
              
           return parent::parse();
         }
         
         private function isValidUrl($uri){
             $ext=ScraperUtils::getExtension($uri);
             if(strtolower($ext)=="pdf") return false;
             if(strtolower($ext)=="jpg") return false;
             
           
             return true;
             
         }
         private  function truepath($path,$base){
            // whether $path is unix or not
            $unipath=strlen($path)==0 || $path{0}!='/';
            // attempts to detect if path is relative in which case, add cwd
            if(strpos($path,':')===false && $unipath)
                $path=$base."/".$path;
            // resolve path parts (single dot, double dot and double delimiters)
            $path = str_replace(array('/', '\\'), "/", $path);
            $parts = array_filter(explode("/", $path), 'strlen');
            $absolutes = array();
            foreach ($parts as $part) {
                if ('.'  == $part) continue;
                if ('..' == $part) {
                    array_pop($absolutes);
                } else {
                    $absolutes[] = $part;
                }
            }
            $path=implode('/', $absolutes);
            // resolve any symlinks
            if(file_exists($path) && linkinfo($path)>0)$path=readlink($path);
            // put initial separator that could have been lost
            $path=!$unipath ? '//'.$path : $path;
            return $path;
        }

        private function cleanPath($path) {
    $result = array();
    // $pathA = preg_split('/[\/\\\]/', $path);
    $pathA = explode('/', $path);
    if (!$pathA[0])
        $result[] = '';
    foreach ($pathA AS $key => $dir) {
        if ($dir == '..') {
            if (end($result) == '..') {
                $result[] = '..';
            } elseif (!array_pop($result)) {
                $result[] = '..';
            }
        } elseif ($dir && $dir != '.') {
            $result[] = $dir;
        }
    }
    if (!end($pathA))
        $result[] = '';
    return implode('/', $result);
}
}

?>
