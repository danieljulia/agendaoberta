<?php


class ScraperUtils 
{

    static function getNextText($delimiter,$str,$next=null){
       $res=explode($delimiter,$str);
       if(count($res)<=1) return false;
      
       $text=$res[1];
        
       if($next){
        //retallar fins alguns dels textos de next
        $prop=array();
        foreach($next as $t){
            $i=stripos($text, $t);
            if($i!=0)
                $prop[]=substr($text,0,  stripos($text, $t));
        }
 
        $min=10000;
        foreach($prop as $p){
            if(strlen($p)<$min){
                $min=strlen($p);
                $text=$p;
            }
        }
       
       }
       return $text;
       
        /*
      $matches=array();
       if (preg_match_all('/([^:]*)$/',$str,$matches)){  //funciona be pero nomes extreu una data
          return $matches[0];
       }*/
        
    }

    static function getParamsFromUrl($url){
        $parsed_url=  parse_url($url);
          
            
        $url_query = $parsed_url['query'];
        parse_str($url_query, $out);
        return $out;
        
    }
    
    static function dirName($str){
       
         $path_parts = pathinfo($str);
        
         //abans estava aixi
         return $path_parts['dirname']."/";
        //return $path_parts['dirname']."//".$path_parts['basename']."/";
    }
    
    static function basePath($url){
         $data=parse_url($url);
         $res=$data['scheme']."://".$data['host']."/";
         return $res;
         
    }
    
    
    static function relativePath($from, $to, $ps = "/")
    {
    $arFrom = explode($ps, rtrim($from, $ps));
    $arTo = explode($ps, rtrim($to, $ps));
    while(count($arFrom) && count($arTo) && ($arFrom[0] == $arTo[0]))
    {
        array_shift($arFrom);
        array_shift($arTo);
    }
    return str_pad("", count($arFrom) * 3, '..'.$ps).implode($ps, $arTo);
    }


    //reemplaça .. a partir del path base
    //molt senzill.. elimina ..
    static function replacePathPoints($path,$uri){
         
        print "path $path uri $uri";
        
        /*
           // path timeou.cat/blah/blah
           // uri  ../../blah
           // reemplaça  ../../../ a partir del path base
            $p=explode("/",$path);
            $u=explode("../",$uri);
        
            
            $res=$p[0];
            for($i=0;$i<count($p)-count($u);$i++){
                $res.=$p[i];
            }
            $res.=
            print "<br><br><br><br>de path $path i uri $uri retorna res $res*";
            return $res;
            
          */
            if( !(strpos($uri,"../")===false) || strpos($uri,"../")==0 ) {
                        //buscar fins l'ultim /
            
                
                    $plorp = substr(strrchr($path,'/'), 1);
                    
                    if(strlen($plorp)>0){
                   
                      
                         $path = substr($path, 0, - strlen($plorp));
            
                   
                    }
                    $p=$path.$uri;

                   
                   $realpath=ScraperUtils::cleanPath($p);
                    //si no funciona nomes activar si hi ha ..

                    if(strpos($realpath,"http://")===false){
                        $realpath=str_replace("http:/", "http://", $realpath);
                    }
                     
                 
                    
                    return $realpath;
            }
            return $path.$uri;
        
    }
    
    //a partir de www.example.com/blah/qweqweqwe.html retorna   example.com/blah/
    static function getPathFromUrl($path){
       $res=  substr($path, 0,strripos($path,"/"));
       return $res;
    }
    
    static    function cleanPath($path) {
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

    static function  extractUnit($string, $start, $end)
    {
        $pos = stripos($string, $start);
        $str = substr($string, $pos);
        $str_two = substr($str, strlen($start));
        $second_pos = stripos($str_two, $end);
        $str_three = substr($str_two, 0, $second_pos);
        $unit = trim($str_three); // remove whitespaces
        return $unit;
    }
    
      //supossant que el contingut ja és una location, netejar
    //afegir ciutat si no la inclou i la font si
    public function purgeLocation($location,$source=null){
        $location=ScraperUtils::cleanString($location);
        $location=ScraperUtils::cutUntilZipCode($location);
        return $location;
    }
  
    //supossant que el contingut ja és una location, eliminar possibles etiquetes
    public function clearLabelLocation(&$e){
        
    }
    
    
    //remove given params from url
    static function removeParams($url,$pars){
   
        $params = parse_url($url);    

        parse_str($params['query'], $output);
        $res= $params['scheme']."://".$params['host']."/".$params['path'];

        $new=array();
        foreach($output as $key=>$value){
           // print "compara la key ".$key." amb params";
          ///  print_r($pars);
            
            if(!in_array($key, $pars)){
                $new[$key]=$value;
            }
        }
        
        return $res."?".http_build_query($new);
 
             
    }
    
    static function removeAllParams($url){
        //just fin first ? and remove from there
        $res=  explode("?", $url);
        if(!$res) return $url;
        return $res[0];
        
  
    
    }
    static function cutUntilZipCode($str){
       
      // $str=  trim(strtolower($str));
        $res=explode("situar",$str);
      
        
        if(count($res)>1){
            $str=$res[0];
        }
        
        
        //al final vol dir unicode
        if (preg_match_all('/[0-9]{1}[0-9]{1}[0-9]{1}[0-9]{1}[0-9]{1}/ui',$str,$matches,PREG_OFFSET_CAPTURE)){
 
                $pos=$matches[0][0][1];
               $str=substr($str,0,$pos+5);
        }
        
        return $str;
        
    }
    
static function parseStrings($txt,$strings,$postchar=":"){
	$txt=strtolower($txt);

	$str=array("global"=>array($txt));
	
	$c=0;
        
	//mb_regex_encoding('UTF-8');
       // mb_internal_encoding("UTF-8"); 
                                          
          
        $i=0;
        foreach($strings as $strvalue=>$strkey ){
	//for($i=0;$i<count($strings);$i++){
		//$mystring=$strings[$i].$postchar;
                $mystring=$strvalue.$postchar;
		foreach($str as $key=>$value){
			for($j=0;$j<count($value);$j++){
				$val=$value[$j];
				foreach($value as $val){
					$results=explode($mystring,$val);
                                          
                                        for($n=0;$n<count($results);$n++){
                                            $results[$n]=trim($results[$n]);
                                        }
                                        
                                        //    $results=mb_split($mystring,$val);
				
					$c++;
					if($c==100){
						
						return $str;
					}
					if(count($results)>1){ 
						
							
							$str[$key][$j]=$results[0];
							for($k=1;$k<count($results);$k++){
								if($results[$k]!=""){
							
								if(isset($str[$strvalue])){
		
									
									if(!in_array($results[$k],$str[$strvalue])){
										
										//$str[$mystring][]=$results[$k];
									}
								}else{
									$str[$strvalue]=array(trim($results[$k]));
								}
								
							}
						}
					}
					
				}
			}
		}
	
                $i++;
	}
	return $str;
	
    }
    
    
    static function fixPath($uri,$path){

        if(strpos($path,"/")===0){
            ///   /blah.jpg
         
            $uri=self::basePath($uri).$path;
            return self::removeDoubleSlash($uri); 
            
        }
        if(strpos($path,"http://")===0){
         
            return $path;
            
        }
        if(strpos($path,".")===0){
          
            return self::replacePathPoints($uri, $path);
        }
        
        //remove params from url
        //get until last / to get rid of params
      
     
        $uri=self::removeAllParams($uri);
        
        //check if type something /blah.asp .php..
        $ext=ScraperUtils::getExtension($uri);
        if($ext!=""){
           
            $uri=self::getPathFromUrl($uri)."/";
        }
        
      
        return $uri.$path;
    }
    static function removeDoubleSlash($in) {
        return preg_replace('%([^:])([/]{2,})%', '\\1/', $in);
    }
    
    static function getExtension($file){
        $res=explode(".", $file);
        if(count($res)>1 ){
            if(strlen(end($res))<=3){
                return strtolower(end($res));  
            }
        }
        return "";
    }
    
    static function http_get_file($url)    {
           try{
               
  
        //$data=file_get_contents(($url));
       // return $data;
        
        //aquesta opció falla::.
        $url_stuff = parse_url($url);
        $port = isset($url_stuff['port']) ? $url_stuff['port']:80;

        $fp = fsockopen($url_stuff['host'], $port);

        $query  = 'GET ' . $url_stuff['path'] . " HTTP/1.0\n";
        $query .= 'Host: ' . $url_stuff['host'];
        $query .= "\n\n";

        fwrite($fp, $query);

        while ($line = fread($fp, 1024)) {
            $buffer .= $line;
        }

        preg_match('/Content-Length: ([0-9]+)/', $buffer, $parts);
      // if(count($parts)>1){
        return substr($buffer, - $parts[1]);
       //    return substr($buffer);
      // }
             // }else{}catch(Exception $e){
        }catch(Exception $e){

            Yii::log("error http_get_file".$e,"info","ao.parser");
            return "";
        }
       
    }   


static function ImageCreateFromBMP($filename)
{
 //Ouverture du fichier en mode binaire
   if (! $f1 = fopen($filename,"rb")) return FALSE;

 //1 : Chargement des ent�tes FICHIER
   $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
   if ($FILE['file_type'] != 19778) return FALSE;

 //2 : Chargement des ent�tes BMP
   $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
                 '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
                 '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
   $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
   if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
   $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
   $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
   $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
   $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
   $BMP['decal'] = 4-(4*$BMP['decal']);
   if ($BMP['decal'] == 4) $BMP['decal'] = 0;

 //3 : Chargement des couleurs de la palette
   $PALETTE = array();
   if ($BMP['colors'] < 16777216)
   {
    $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
   }

 //4 : Cr�ation de l'image
   $IMG = fread($f1,$BMP['size_bitmap']);
   $VIDE = chr(0);

   $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
   $P = 0;
   $Y = $BMP['height']-1;
   while ($Y >= 0)
   {
    $X=0;
    while ($X < $BMP['width'])
    {
     if ($BMP['bits_per_pixel'] == 24)
        $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
     elseif ($BMP['bits_per_pixel'] == 16)
     {  
        $COLOR = unpack("n",substr($IMG,$P,2));
        $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     elseif ($BMP['bits_per_pixel'] == 8)
     {  
        $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
        $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     elseif ($BMP['bits_per_pixel'] == 4)
     {
        $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
        if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
        $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     elseif ($BMP['bits_per_pixel'] == 1)
     {
        $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
        if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
        elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
        elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
        elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
        elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
        elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
        elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
        elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
        $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     else
        return FALSE;
     imagesetpixel($res,$X,$Y,$COLOR[1]);
     $X++;
     $P += $BMP['bytes_per_pixel'];
    }
    $Y--;
    $P+=$BMP['decal'];
   }

 //Fermeture du fichier
   fclose($f1);

 return $res;
}

static function multipleExplode($delimiters = array(), $string = ''){ 

   // $string=  strtolower($string);
    $string=self::cleanString($string);

    
    $mainDelim=$delimiters[count($delimiters)-1]; // dernier 

    array_pop($delimiters); 
   
    foreach($delimiters as $delimiter){ 
        $string= str_replace($delimiter, $mainDelim, $string); 
    } 
    $result= explode($mainDelim, $string); 
    return $result; 

} 

    static function cleanString($str){
    
        
        $str= trim($str);
        $str= str_replace("\n"," ",$str);

        $str= str_replace("\r"," ",$str);
       // $str= preg_replace('/\s\s+/u', ' ', $str);  //fa desapareixer el text!
        $str= strip_tags( $str);

        return $str;
    }
    
    static function check_utf8($str) { 
    $len = strlen($str); 
    for($i = 0; $i < $len; $i++){ 
        $c = ord($str[$i]); 
        if ($c > 128) { 
            if (($c > 247)) return false; 
            elseif ($c > 239) $bytes = 4; 
            elseif ($c > 223) $bytes = 3; 
            elseif ($c > 191) $bytes = 2; 
            else return false; 
            if (($i + $bytes) > $len) return false; 
            while ($bytes > 1) { 
                $i++; 
                $b = ord($str[$i]); 
                if ($b < 128 || $b > 191) return false; 
                $bytes--; 
            } 
        } 
    } 
    return true; 
} // end of check_utf8 

/**
 * Edited by Nitin Kr. Gupta, publicmind.in
 */

/**
 * Copyright (c) 2008, David R. Nadeau, NadeauSoftware.com.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *	* Redistributions of source code must retain the above copyright
 *	  notice, this list of conditions and the following disclaimer.
 *
 *	* Redistributions in binary form must reproduce the above
 *	  copyright notice, this list of conditions and the following
 *	  disclaimer in the documentation and/or other materials provided
 *	  with the distribution.
 *
 *	* Neither the names of David R. Nadeau or NadeauSoftware.com, nor
 *	  the names of its contributors may be used to endorse or promote
 *	  products derived from this software without specific prior
 *	  written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY
 * WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY
 * OF SUCH DAMAGE.
 */

/*
 * This is a BSD License approved by the Open Source Initiative (OSI).
 * See:  http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * Combine a base URL and a relative URL to produce a new
 * absolute URL.  The base URL is often the URL of a page,
 * and the relative URL is a URL embedded on that page.
 *
 * This function implements the "absolutize" algorithm from
 * the RFC3986 specification for URLs.
 *
 * This function supports multi-byte characters with the UTF-8 encoding,
 * per the URL specification.
 *
 * Parameters:
 * 	baseUrl		the absolute base URL.
 *
 * 	url		the relative URL to convert.
 *
 * Return values:
 * 	An absolute URL that combines parts of the base and relative
 * 	URLs, or FALSE if the base URL is not absolute or if either
 * 	URL cannot be parsed.
 */
static function url_to_absolute( $baseUrl, $relativeUrl )
{
	// If relative URL has a scheme, clean path and return.
	$r = ScraperUtils::split_url( $relativeUrl );
	if ( $r === FALSE )
		return FALSE;
	if ( !empty( $r['scheme'] ) )
	{
		if ( !empty( $r['path'] ) && $r['path'][0] == '/' )
			$r['path'] = url_remove_dot_segments( $r['path'] );
		return join_url( $r );
	}

	// Make sure the base URL is absolute.
	$b = ScraperUtils::split_url( $baseUrl );
	if ( $b === FALSE || empty( $b['scheme'] ) || empty( $b['host'] ) )
		return FALSE;
	$r['scheme'] = $b['scheme'];

	// If relative URL has an authority, clean path and return.
	if ( isset( $r['host'] ) )
	{
		if ( !empty( $r['path'] ) )
			$r['path'] = url_remove_dot_segments( $r['path'] );
		return join_url( $r );
	}
	unset( $r['port'] );
	unset( $r['user'] );
	unset( $r['pass'] );

	// Copy base authority.
	$r['host'] = $b['host'];
	if ( isset( $b['port'] ) ) $r['port'] = $b['port'];
	if ( isset( $b['user'] ) ) $r['user'] = $b['user'];
	if ( isset( $b['pass'] ) ) $r['pass'] = $b['pass'];

	// If relative URL has no path, use base path
	if ( empty( $r['path'] ) )
	{
		if ( !empty( $b['path'] ) )
			$r['path'] = $b['path'];
		if ( !isset( $r['query'] ) && isset( $b['query'] ) )
			$r['query'] = $b['query'];
		return join_url( $r );
	}

	// If relative URL path doesn't start with /, merge with base path
	if ( $r['path'][0] != '/' )
	{
		$base = mb_strrchr( $b['path'], '/', TRUE, 'UTF-8' );
		if ( $base === FALSE ) $base = '';
		$r['path'] = $base . '/' . $r['path'];
	}
	$r['path'] = ScraperUtils::url_remove_dot_segments( $r['path'] );
	return ScraperUtils::join_url( $r );
}

/**
 * Filter out "." and ".." segments from a URL's path and return
 * the result.
 *
 * This function implements the "remove_dot_segments" algorithm from
 * the RFC3986 specification for URLs.
 *
 * This function supports multi-byte characters with the UTF-8 encoding,
 * per the URL specification.
 *
 * Parameters:
 * 	path	the path to filter
 *
 * Return values:
 * 	The filtered path with "." and ".." removed.
 */
static function url_remove_dot_segments( $path )
{
	// multi-byte character explode
	$inSegs  = preg_split( '!/!u', $path );
	$outSegs = array( );
	foreach ( $inSegs as $seg )
	{
		if ( $seg == '' || $seg == '.')
			continue;
		if ( $seg == '..' )
			array_pop( $outSegs );
		else
			array_push( $outSegs, $seg );
	}
	$outPath = implode( '/', $outSegs );
	if ( $path[0] == '/' )
		$outPath = '/' . $outPath;
	// compare last multi-byte character against '/'
	if ( $outPath != '/' &&
		(mb_strlen($path)-1) == mb_strrpos( $path, '/', 'UTF-8' ) )
		$outPath .= '/';
	return $outPath;
}


/**
 * This function parses an absolute or relative URL and splits it
 * into individual components.
 *
 * RFC3986 specifies the components of a Uniform Resource Identifier (URI).
 * A portion of the ABNFs are repeated here:
 *
 *	URI-reference	= URI
 *			/ relative-ref
 *
 *	URI		= scheme ":" hier-part [ "?" query ] [ "#" fragment ]
 *
 *	relative-ref	= relative-part [ "?" query ] [ "#" fragment ]
 *
 *	hier-part	= "//" authority path-abempty
 *			/ path-absolute
 *			/ path-rootless
 *			/ path-empty
 *
 *	relative-part	= "//" authority path-abempty
 *			/ path-absolute
 *			/ path-noscheme
 *			/ path-empty
 *
 *	authority	= [ userinfo "@" ] host [ ":" port ]
 *
 * So, a URL has the following major components:
 *
 *	scheme
 *		The name of a method used to interpret the rest of
 *		the URL.  Examples:  "http", "https", "mailto", "file'.
 *
 *	authority
 *		The name of the authority governing the URL's name
 *		space.  Examples:  "example.com", "user@example.com",
 *		"example.com:80", "user:password@example.com:80".
 *
 *		The authority may include a host name, port number,
 *		user name, and password.
 *
 *		The host may be a name, an IPv4 numeric address, or
 *		an IPv6 numeric address.
 *
 *	path
 *		The hierarchical path to the URL's resource.
 *		Examples:  "/index.htm", "/scripts/page.php".
 *
 *	query
 *		The data for a query.  Examples:  "?search=google.com".
 *
 *	fragment
 *		The name of a secondary resource relative to that named
 *		by the path.  Examples:  "#section1", "#header".
 *
 * An "absolute" URL must include a scheme and path.  The authority, query,
 * and fragment components are optional.
 *
 * A "relative" URL does not include a scheme and must include a path.  The
 * authority, query, and fragment components are optional.
 *
 * This function splits the $url argument into the following components
 * and returns them in an associative array.  Keys to that array include:
 *
 *	"scheme"	The scheme, such as "http".
 *	"host"		The host name, IPv4, or IPv6 address.
 *	"port"		The port number.
 *	"user"		The user name.
 *	"pass"		The user password.
 *	"path"		The path, such as a file path for "http".
 *	"query"		The query.
 *	"fragment"	The fragment.
 *
 * One or more of these may not be present, depending upon the URL.
 *
 * Optionally, the "user", "pass", "host" (if a name, not an IP address),
 * "path", "query", and "fragment" may have percent-encoded characters
 * decoded.  The "scheme" and "port" cannot include percent-encoded
 * characters and are never decoded.  Decoding occurs after the URL has
 * been parsed.
 *
 * Parameters:
 * 	url		the URL to parse.
 *
 * 	decode		an optional boolean flag selecting whether
 * 			to decode percent encoding or not.  Default = TRUE.
 *
 * Return values:
 * 	the associative array of URL parts, or FALSE if the URL is
 * 	too malformed to recognize any parts.
 */
static function split_url( $url, $decode=FALSE)
{
	// Character sets from RFC3986.
	$xunressub     = 'a-zA-Z\d\-._~\!$&\'()*+,;=';
	$xpchar        = $xunressub . ':@% ';

	// Scheme from RFC3986.
	$xscheme        = '([a-zA-Z][a-zA-Z\d+-.]*)';

	// User info (user + password) from RFC3986.
	$xuserinfo     = '((['  . $xunressub . '%]*)' .
	                 '(:([' . $xunressub . ':%]*))?)';

	// IPv4 from RFC3986 (without digit constraints).
	$xipv4         = '(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})';

	// IPv6 from RFC2732 (without digit and grouping constraints).
	$xipv6         = '(\[([a-fA-F\d.:]+)\])';

	// Host name from RFC1035.  Technically, must start with a letter.
	// Relax that restriction to better parse URL structure, then
	// leave host name validation to application.
	$xhost_name    = '([a-zA-Z\d-.%]+)';

	// Authority from RFC3986.  Skip IP future.
	$xhost         = '(' . $xhost_name . '|' . $xipv4 . '|' . $xipv6 . ')';
	$xport         = '(\d*)';
	$xauthority    = '((' . $xuserinfo . '@)?' . $xhost .
		         '?(:' . $xport . ')?)';

	// Path from RFC3986.  Blend absolute & relative for efficiency.
	$xslash_seg    = '(/[' . $xpchar . ']*)';
	$xpath_authabs = '((//' . $xauthority . ')((/[' . $xpchar . ']*)*))';
	$xpath_rel     = '([' . $xpchar . ']+' . $xslash_seg . '*)';
	$xpath_abs     = '(/(' . $xpath_rel . ')?)';
	$xapath        = '(' . $xpath_authabs . '|' . $xpath_abs .
			 '|' . $xpath_rel . ')';

	// Query and fragment from RFC3986.
	$xqueryfrag    = '([' . $xpchar . '/?' . ']*)';

	// URL.
	$xurl          = '^(' . $xscheme . ':)?' .  $xapath . '?' .
	                 '(\?' . $xqueryfrag . ')?(#' . $xqueryfrag . ')?$';


	// Split the URL into components.
	if ( !preg_match( '!' . $xurl . '!', $url, $m ) )
		return FALSE;

	if ( !empty($m[2]) )		$parts['scheme']  = strtolower($m[2]);

	if ( !empty($m[7]) ) {
		if ( isset( $m[9] ) )	$parts['user']    = $m[9];
		else			$parts['user']    = '';
	}
	if ( !empty($m[10]) )		$parts['pass']    = $m[11];

	if ( !empty($m[13]) )		$h=$parts['host'] = $m[13];
	else if ( !empty($m[14]) )	$parts['host']    = $m[14];
	else if ( !empty($m[16]) )	$parts['host']    = $m[16];
	else if ( !empty( $m[5] ) )	$parts['host']    = '';
	if ( !empty($m[17]) )		$parts['port']    = $m[18];

	if ( !empty($m[19]) )		$parts['path']    = $m[19];
	else if ( !empty($m[21]) )	$parts['path']    = $m[21];
	else if ( !empty($m[25]) )	$parts['path']    = $m[25];

	if ( !empty($m[27]) )		$parts['query']   = $m[28];
	if ( !empty($m[29]) )		$parts['fragment']= $m[30];

	if ( !$decode )
		return $parts;
	if ( !empty($parts['user']) )
		$parts['user']     = rawurldecode( $parts['user'] );
	if ( !empty($parts['pass']) )
		$parts['pass']     = rawurldecode( $parts['pass'] );
	if ( !empty($parts['path']) )
		$parts['path']     = rawurldecode( $parts['path'] );
	if ( isset($h) )
		$parts['host']     = rawurldecode( $parts['host'] );
	if ( !empty($parts['query']) )
		$parts['query']    = rawurldecode( $parts['query'] );
	if ( !empty($parts['fragment']) )
		$parts['fragment'] = rawurldecode( $parts['fragment'] );
	return $parts;
}


/**
 * This function joins together URL components to form a complete URL.
 *
 * RFC3986 specifies the components of a Uniform Resource Identifier (URI).
 * This function implements the specification's "component recomposition"
 * algorithm for combining URI components into a full URI string.
 *
 * The $parts argument is an associative array containing zero or
 * more of the following:
 *
 *	"scheme"	The scheme, such as "http".
 *	"host"		The host name, IPv4, or IPv6 address.
 *	"port"		The port number.
 *	"user"		The user name.
 *	"pass"		The user password.
 *	"path"		The path, such as a file path for "http".
 *	"query"		The query.
 *	"fragment"	The fragment.
 *
 * The "port", "user", and "pass" values are only used when a "host"
 * is present.
 *
 * The optional $encode argument indicates if appropriate URL components
 * should be percent-encoded as they are assembled into the URL.  Encoding
 * is only applied to the "user", "pass", "host" (if a host name, not an
 * IP address), "path", "query", and "fragment" components.  The "scheme"
 * and "port" are never encoded.  When a "scheme" and "host" are both
 * present, the "path" is presumed to be hierarchical and encoding
 * processes each segment of the hierarchy separately (i.e., the slashes
 * are left alone).
 *
 * The assembled URL string is returned.
 *
 * Parameters:
 * 	parts		an associative array of strings containing the
 * 			individual parts of a URL.
 *
 * 	encode		an optional boolean flag selecting whether
 * 			to do percent encoding or not.  Default = true.
 *
 * Return values:
 * 	Returns the assembled URL string.  The string is an absolute
 * 	URL if a scheme is supplied, and a relative URL if not.  An
 * 	empty string is returned if the $parts array does not contain
 * 	any of the needed values.
 */
static function join_url( $parts, $encode=FALSE)
{
	if ( $encode )
	{
		if ( isset( $parts['user'] ) )
			$parts['user']     = rawurlencode( $parts['user'] );
		if ( isset( $parts['pass'] ) )
			$parts['pass']     = rawurlencode( $parts['pass'] );
		if ( isset( $parts['host'] ) &&
			!preg_match( '!^(\[[\da-f.:]+\]])|([\da-f.:]+)$!ui', $parts['host'] ) )
			$parts['host']     = rawurlencode( $parts['host'] );
		if ( !empty( $parts['path'] ) )
			$parts['path']     = preg_replace( '!%2F!ui', '/',
				rawurlencode( $parts['path'] ) );
		if ( isset( $parts['query'] ) )
			$parts['query']    = rawurlencode( $parts['query'] );
		if ( isset( $parts['fragment'] ) )
			$parts['fragment'] = rawurlencode( $parts['fragment'] );
	}

	$url = '';
	if ( !empty( $parts['scheme'] ) )
		$url .= $parts['scheme'] . ':';
	if ( isset( $parts['host'] ) )
	{
		$url .= '//';
		if ( isset( $parts['user'] ) )
		{
			$url .= $parts['user'];
			if ( isset( $parts['pass'] ) )
				$url .= ':' . $parts['pass'];
			$url .= '@';
		}
		if ( preg_match( '!^[\da-f]*:[\da-f.:]+$!ui', $parts['host'] ) )
			$url .= '[' . $parts['host'] . ']';	// IPv6
		else
			$url .= $parts['host'];			// IPv4 or name
		if ( isset( $parts['port'] ) )
			$url .= ':' . $parts['port'];
		if ( !empty( $parts['path'] ) && $parts['path'][0] != '/' )
			$url .= '/';
	}
	if ( !empty( $parts['path'] ) )
		$url .= $parts['path'];
	if ( isset( $parts['query'] ) )
		$url .= '?' . $parts['query'];
	if ( isset( $parts['fragment'] ) )
		$url .= '#' . $parts['fragment'];
	return $url;
}

/**
 * This function encodes URL to form a URL which is properly 
 * percent encoded to replace disallowed characters.
 *
 * RFC3986 specifies the allowed characters in the URL as well as
 * reserved characters in the URL. This function replaces all the 
 * disallowed characters in the URL with their repective percent 
 * encodings. Already encoded characters are not encoded again,
 * such as '%20' is not encoded to '%2520'.
 *
 * Parameters:
 * 	url		the url to encode.
 *
 * Return values:
 * 	Returns the encoded URL string. 
 */
static function encode_url($url) {
  $reserved = array(
    ":" => '!%3A!ui',
    "/" => '!%2F!ui',
    "?" => '!%3F!ui',
    "#" => '!%23!ui',
    "[" => '!%5B!ui',
    "]" => '!%5D!ui',
    "@" => '!%40!ui',
    "!" => '!%21!ui',
    "$" => '!%24!ui',
    "&" => '!%26!ui',
    "'" => '!%27!ui',
    "(" => '!%28!ui',
    ")" => '!%29!ui',
    "*" => '!%2A!ui',
    "+" => '!%2B!ui',
    "," => '!%2C!ui',
    ";" => '!%3B!ui',
    "=" => '!%3D!ui',
    "%" => '!%25!ui',
  );

  $url = rawurlencode($url);
  $url = preg_replace(array_values($reserved), array_keys($reserved), $url);
  return $url;
}



}




?>
