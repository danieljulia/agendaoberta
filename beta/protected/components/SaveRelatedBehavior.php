<?php

class SaveRelatedBehavior extends CActiveRecordBehavior
{
    public $config;
    
    public $autoSaveRelated = true;
    
    public function attach($owner) {
      parent::attach($owner);
    }

    public function afterSave() {
      if ($this->autoSaveRelated) {
        foreach ($this->config as $k=>$v) {
					$this->saveRelated($k);
				}
      }
    }

		public function saveRelated($k) {

			if (!isset($this->config[$k])) return;

			$tbl = $this->config[$k][0]; 
			$fk1 = $this->config[$k][1]; 
			$fk2 = $this->config[$k][2];

			$owner = $this->getOwner();

			$a = $owner->$k;
			
			if (!$a) $a = array();
			if (isset($a[0]) && is_object($a[0])) $a = array();

			$db = $owner->getDbConnection();
			$existing = $this->readRelated($tbl, $fk1, $fk2);

			//array_diff(array1, array2, ...) Returns an array containing all the entries from array1 that are not present in any of the other arrays. 
			$del = array_diff($existing, $a);
			if ($del) {
				$db->createCommand()->delete($tbl,array('and',"$fk1 = :id",array('in',$fk2,$del)), array(':id'=>$owner->id));				
			}

			$ins = array_diff($a, $existing);
			if ($ins) {
				$comm = $db->createCommand("INSERT INTO $tbl ($fk1, $fk2) VALUES (:id, :i)");
				foreach ($ins as $i) {
					try {
						$comm->execute(array(':id'=>$owner->id,':i'=>$i));
					} catch (CDbException $e) {

					}
				}
			}
			
			if (isset($this->config[$k][3])) {
				$attr = $this->config[$k][3];
				//$owner->saveAttributes(array($attr=>isset($a[0])?$a[0]:null));
				$ownerTbl = $owner->tableName();
				$comm = $db->createCommand("UPDATE $ownerTbl SET $attr = :v WHERE id = :id");
				$comm->execute(array(':v'=>isset($a[0])?$a[0]:null, ':id'=>$owner->id));				
			}
		}	

		private function readRelated($tbl, $fk1, $fk2) {
			$owner = $this->getOwner();
			$sql = "SELECT $fk2 FROM $tbl WHERE $fk1 = :id";
			$db = $owner->getDbConnection();
			$comm = $db->createCommand($sql);
			/* @var $comm CDbCommand */
			$a = $comm->queryColumn(array(':id'=>$owner->id));
			return $a;
		}	
}