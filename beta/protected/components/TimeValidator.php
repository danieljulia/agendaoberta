<?php

class TimeValidator extends CValidator
{	
	public $skipOnError=true;

	public $allowEmpty=true;
	
	public $allowSeconds = false;
	
	protected function validateAttribute($object,$attribute) {
		$value=$object->$attribute;
		if($this->allowEmpty && $this->isEmpty($value))
			return;
		
		$valid = true;
										
		if (!preg_match('/^[0-9]{2}\:[0-9]{2}(\:[0-9]{2})?$/',$value)) {
			$valid = false;
		} else {
			$a = explode(':',$value);
			if ( (int)$a[0] > 23) $valid = false; 
			else if ( (int)$a[1] > 59) $valid = false;
			else if (isset($a[2]) && (int)$a[2] > 59) $valid = false;
		}			
		
		if (!$valid) {
			$message=$this->message!==null ? $this->message : Yii::t('yii','{attribute} is invalid.');
			$this->addError($object,$attribute,$message);
			return;
		}
		
		if (!$this->allowSeconds && isset($a[2])) {
			$object->$attribute = join(":",array($a[0],$a[1]));
		}
	}
}

