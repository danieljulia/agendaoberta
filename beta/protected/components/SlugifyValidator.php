<?php

class SlugifyValidator extends CValidator
{

	public $fromAttribute;
	
	public $skipOnError=true;

	public $allowEmpty=false;
	
	
	protected function validateAttribute($object,$attribute) {
		$value=$object->$attribute;
		if($this->allowEmpty && $this->isEmpty($value))
			return;
		
		$slugifyAttribute = $this->fromAttribute;
		
		if ($this->isEmpty($value)) {
			if (!$this->isEmpty($object->$slugifyAttribute))  {
				$object->$attribute = Utils::slugify($object->$slugifyAttribute);
			}
		} else {
			if (!preg_match("/^[a-z0-9-]+$/ui",$value)) {
				$object->$attribute = Utils::slugify($value);
			}	
		}
		
	}
}

