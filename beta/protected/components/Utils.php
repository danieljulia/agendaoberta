<?php

class Utils {

	/**
	 * Smarty truncate modifier plugin
	 *
	 * Type:     modifier<br>
	 * Name:     truncate<br>
	 * Purpose:  Truncate a string to a certain length if necessary,
	 *               optionally splitting in the middle of a word, and
	 *               appending the $etc string or inserting $etc into the middle.
	 *
	 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php truncate (Smarty online manual)
	 * @author Monte Ohrt <monte at ohrt dot com>
	 * @param string $string input string
	 * @param integer $length lenght of truncated text
	 * @param string $etc end string
	 * @param boolean $break_words truncate at word boundary
	 * @param boolean $middle truncate in the middle of text
	 * @return string truncated string
	 */
	public static function truncate($string, $length = 80, $break_words = false, $etc = '...', $middle = false) {
		if ($length == 0)
			return '';

		if (is_callable('mb_strlen') && mb_detect_encoding($string, 'UTF-8, ISO-8859-1') === 'UTF-8') {
			// $string has utf-8 encoding
			if (mb_strlen($string) > $length) {
				$length -= min($length, mb_strlen($etc));
				if (!$break_words && !$middle) {
					$string = preg_replace('/\s+?(\S+)?$/u', '', mb_substr($string, 0, $length + 1));						
				}
				if (!$middle) {
					return mb_substr($string, 0, $length) . $etc;
				} else {
					return mb_substr($string, 0, $length / 2) . $etc . mb_substr($string, - $length / 2);
				}
			} else {
				return $string;
			}
		}

		// $string has no utf-8 encoding
		if (strlen($string) > $length) {
			$length -= min($length, strlen($etc));
			if (!$break_words && !$middle) {
				$string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
			}
			if (!$middle) {
				return substr($string, 0, $length) . $etc;
			} else {
				return substr($string, 0, $length / 2) . $etc . substr($string, - $length / 2);
			}
		} else {
			return $string;
		}
	}
	
	public static function getTxt($content) {
		$txt = $content;
		$txt = strtr($txt, array('<'=>' <','>'=>'> ','&nbsp;'=>' '));
    $txt = html_entity_decode(strip_tags($txt), ENT_QUOTES , 'UTF-8');
    $txt = preg_replace('/[\n\r\t\s]+/u',' ',$txt);
		$txt = trim($txt);
		return $txt;
	}

  public static function slugify($text)	{

    $patterns = array();
    $replacements = array();
    $patterns[]     = '/[á|â|à|å|ä]/iu';
    $replacements[] = 'a';
    $patterns[]     = '/[ð|é|ê|è|ë]/iu';
    $replacements[] = 'e';
    $patterns[]     = '/[í|î|ì|ï]/iu';
    $replacements[] = 'i';
    $patterns[]     = '/[ó|ô|ò|ø|õ|ö]/iu';
    $replacements[] = 'o';
    $patterns[]     = '/[ú|û|ù|ü]/iu';
    $replacements[] = 'u';
    $patterns[]     = '/æ/iu';
    $replacements[] = 'ae';
    $patterns[]     = '/ç/iu';
    $replacements[] = 'c';
    $patterns[]     = '/ß/iu';
    $replacements[] = 'ss';
    $patterns[]     = '/ñ/iu';
    $replacements[] = 'n';

    $text = preg_replace($patterns, $replacements, $text);

    // replace non letter or digits by -
    $text = preg_replace('/[^a-z0-9]+/iu', '-', $text);
    // trim
    $text = trim($text, '-');
    // lowercase
    $text = strtolower($text);
    // remove unwanted characters
    $text = preg_replace('/[^-\w]+/', '', $text);
    if (empty($text)) {
        return 'n-a';
    }
    return $text;
  }

	public static function stripTags($content) {
		$content = preg_replace('/</',' <',$content);
		$content = preg_replace('/>/','> ',$content);		
		$content = html_entity_decode(strip_tags($content), ENT_QUOTES , 'UTF-8');
		$content = preg_replace('/[\n\r\t\s]+/u',' ',$content);
		return $content;
	}
	
	
	public static function mail_utf8($to, $subject='[No subject]', $message='', $header='', $dryRun=false) {
		
		if (is_array($header)) {
			$a = array();
			foreach ($header as $k=>$v) {		
				$a[] = $k.':'.$v;
			}
			$header = implode("\r\n", $a);
		}		
		$header = 'MIME-Version: 1.0'."\r\n".'Content-type: text/html; charset=UTF-8'."\r\n".$header;
		$subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
		if ($dryRun) {
			Yii::trace('Sending email: '.$subject, 'application');
			Yii::trace($message, 'application');
			return true;
		}
		return mail($to, $subject, $message, $header);
	}
        
        /*
       public static function is_utf8($string) {
    
        // From http://w3.org/International/questions/qa-forms-utf-8.html
        return preg_match('%^(?:
            [\x09\x0A\x0D\x20-\x7E]            # ASCII
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
            |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
            |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
        )*$%xs', $string);

    } //
*/
     public static  function detect_encoding($string) {  
        static $list = array('utf-8', 'windows-1251');

        foreach ($list as $item) {
            $sample = iconv($item, $item, $string);
            if (md5($sample) == md5($string))
            return $item;
        }
        return null;
    }
    
     public static  function is_utf8($str) {
    $c=0; $b=0;
    $bits=0;
    $len=strlen($str);
    for($i=0; $i<$len; $i++){
        $c=ord($str[$i]);
        if($c > 128){
            if(($c >= 254)) return false;
            elseif($c >= 252) $bits=6;
            elseif($c >= 248) $bits=5;
            elseif($c >= 240) $bits=4;
            elseif($c >= 224) $bits=3;
            elseif($c >= 192) $bits=2;
            else return false;
            if(($i+$bits) > $len) return false;
            while($bits > 1){
                $i++;
                $b=ord($str[$i]);
                if($b < 128 || $b > 191) return false;
                $bits--;
            }
        }
    }
    return true;
}



}

