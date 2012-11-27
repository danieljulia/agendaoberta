<?php

/**
 * AdminIdentity represents the data needed to identity an admin user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class AdminIdentity extends CUserIdentity
{
	private $_id;

  public function authenticate() {
    $record=Admin::model()->findByAttributes(array('username'=>$this->username));
    if($record===null) {
      $this->errorCode=self::ERROR_USERNAME_INVALID;
    } else if($record->password!==$record->hashPassword($this->password)) {
      $this->errorCode=self::ERROR_PASSWORD_INVALID;
    } else {
      $this->_id=$record->id;      
      $this->errorCode=self::ERROR_NONE;
			$this->setState('last_login', $record->last_login);
			
			$key = uniqid();
			$this->setState('admin_key', $key);
			
			$record->last_login = date('Y-m-d H:i:s');
			$record->login_check = $key;
			$record->save(false,array('last_login','login_check'));
						
    }
    return !$this->errorCode;
  }

  public function getId() {
    return $this->_id;
  }
 
}