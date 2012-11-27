<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	
	public $layout=false; //If it is false, no layout will be applied.
												//El posarem manualment des de les views, si cal (p.ex. en el cas de format html)

	
	public function sendResponse($view,$data=array(),$status=200,$format=null) {
		
		// set the status
    $status_header = 'HTTP/1.1 ' . $status . ' ' . self::getStatusCodeMessage($status);
    header($status_header);
		
		if ($format===null) {
			$format = $this->detectFormat();
		}
		
		$view = $this->getViewFormat($view, $format);
				
    // and the content type
    header('Content-type: ' . self::getContentType($format));
		
		$this->render($view,$data);
		
		Yii::app()->end();
	}
	
	public function detectFormat() {
		$format = Yii::app()->request->getQuery('out');
		return $format;
	}
	
	public function getViewFormat($view,$format='') {
		if ($format) $view = $view.str_replace('.','_',$format);
		$path = $this->getViewFile($view);
		if (!$path) {
			throw new CHttpException(400,'Format not supported.');
		}
		return $view;
	}
	
	
	public static function getContentType($ext) {
		switch ($ext) {
			case '.ics':
				return 'text/calendar';
			case '.json':
				if (!empty($_GET['callback'])) return 'application/javascript';
				return 'application/json';			
			case '.xml':
				return 'text/xml';
			case '.rss':
				return 'application/rss+xml';
			default:
				return 'text/html';
		}
	}
	
	public static function getStatusCodeMessage($status) {  
		// these could be stored in a .ini file and loaded  
		// via parse_ini_file()... however, this will suffice  
		// for an example  
		$codes = Array(  
			100 => 'Continue',  
			101 => 'Switching Protocols',  
			200 => 'OK',  
			201 => 'Created',  
			202 => 'Accepted',  
			203 => 'Non-Authoritative Information',  
			204 => 'No Content',  
			205 => 'Reset Content',  
			206 => 'Partial Content',  
			300 => 'Multiple Choices',  
			301 => 'Moved Permanently',  
			302 => 'Found',  
			303 => 'See Other',  
			304 => 'Not Modified',  
			305 => 'Use Proxy',  
			306 => '(Unused)',  
			307 => 'Temporary Redirect',  
			400 => 'Bad Request',  
			401 => 'Unauthorized',  
			402 => 'Payment Required',  
			403 => 'Forbidden',  
			404 => 'Not Found',  
			405 => 'Method Not Allowed',  
			406 => 'Not Acceptable',  
			407 => 'Proxy Authentication Required',  
			408 => 'Request Timeout',  
			409 => 'Conflict',  
			410 => 'Gone',  
			411 => 'Length Required',  
			412 => 'Precondition Failed',  
			413 => 'Request Entity Too Large',  
			414 => 'Request-URI Too Long',  
			415 => 'Unsupported Media Type',  
			416 => 'Requested Range Not Satisfiable',  
			417 => 'Expectation Failed',  
			500 => 'Internal Server Error',  
			501 => 'Not Implemented',  
			502 => 'Bad Gateway',  
			503 => 'Service Unavailable',  
			504 => 'Gateway Timeout',  
			505 => 'HTTP Version Not Supported'  
		);  

		return (isset($codes[$status])) ? $codes[$status] : '';  
	}
	
}