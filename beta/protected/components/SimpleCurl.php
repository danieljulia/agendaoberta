<?php


/* El component més complex Curl.php
 * falla per algun motiu, per això faig servir aquest simplificat
 */


class SimpleCurl 
{
	public static function curl_download($Url,$get_info=false){
		if (!function_exists('curl_init')){
			die('Sorry cURL is not installed!');
		}
		// OK cool - then let's create a new cURL resource handle

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $Url);

		// Set a referer

		curl_setopt($ch, CURLOPT_REFERER, "http://www.oberta.cat");

		// User agent

		curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
		curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
               
                curl_setopt($ch,CURLOPT_ENCODING , "gzip");
                /* gzip
                 * curl_setopt($ch,CURLOPT_ENCODING , "deflate");
                  curl_setopt($ch,CURLOPT_ENCODING , "sdch");
                 */
                
                curl_setopt($ch,CURLOPT_AUTOREFERER    ,true);
                curl_setopt($ch,CURLOPT_CONNECTTIMEOUT     ,600);   
                 curl_setopt($ch,CURLOPT_TIMEOUT     ,600); 
                 curl_setopt($ch,CURLOPT_MAXREDIRS           ,10); 
                 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$output = curl_exec($ch);
                
               /* Check for 404 (file not found). */
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if($httpCode == 404) {
                    return false;
                }
                
                if($get_info){
                    $res= new stdClass();
                    $info=curl_getinfo($ch);
                 
                    $r=explode("charset=",$info['content_type']);
                  
                    if(count($r)>1){
                        $r2=explode(";",$r[1]);
                        if(count($r2)>1) $r[1]=$r2[0];
                                  
                        $res->charset=  strtolower($r[1]);
                        
                    }else{
                        $res->charset="utf-8";
                     }
                    $res->output=$output;
                    return $res;
                }
               

		curl_close($ch);
		return $output;

	}

	

}