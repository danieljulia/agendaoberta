<?php
/**
 * ERememberFiltersBehavior class file.
 *
 * @author Marton Kodok http://www.yiiframework.com/forum/index.php?/user/8824-pentium10/
 * @link http://www.yiiframework.com/
 * @version 1.1
 */

class RememberFiltersBehavior extends CActiveRecordBehavior {

    /**
     * Array that holds any default filter value like array('active'=>'1')
     *
     * @var array
     */
    public $defaults=array();
    /**
     * When this flag is true, the default values will be used also when the user clears the filters
     *
     * @var boolean
     */
    public $defaultStickOnClear=false;

    private function readSearchValues() {
        $modelName = get_class($this->owner);
        $attributes = $this->owner->getSafeAttributeNames();

        // set any default value

        if (is_array($this->defaults) && (null==Yii::app()->user->getState($modelName . __CLASS__. 'defaultsSet', null))) {
            foreach ($this->defaults as $attribute => $value) {
                if (null == (Yii::app()->user->getState($modelName . $attribute, null))) {
                    Yii::app()->user->setState($modelName . $attribute, $value);
                }
            }
            Yii::app()->user->setState($modelName . __CLASS__. 'defaultsSet', 1);
        }
        
        // set values from session

        foreach ($attributes as $attribute) {
            if (null != ($value = Yii::app()->user->getState($modelName . $attribute, null))) {
                try
                {
                    $this->owner->$attribute = $value;
                }
                catch (Exception $e) {
                }
            }
        }
    }

    private function saveSearchValues() {

        $modelName = get_class($this->owner);
        $attributes = $this->owner->getSafeAttributeNames();
        foreach ($attributes as $attribute) {
            if (isset($this->owner->$attribute)) {
                Yii::app()->user->setState($modelName . $attribute, $this->owner->$attribute);
            }
        }
    }

    public function afterConstruct($event) {
        if ($this->owner->scenario == 'search') {
            $this->owner->unsetAttributes();
            if (isset($_GET[get_class($this->owner)])) {
                $this->owner->attributes = $_GET[get_class($this->owner)];
                $this->saveSearchValues();
            } else {
                $this->readSearchValues();
            }
        }
    }

    public function unsetFilters() {
        $modelName = get_class($this->owner);
        $attributes = $this->owner->getSafeAttributeNames();

        foreach ($attributes as $attribute) {
            if (null != ($value = Yii::app()->user->getState($modelName . $attribute, null))) {
                Yii::app()->user->setState($modelName . $attribute, 1, 1);
            }
        }
        if ($this->defaultStickOnClear) {
            Yii::app()->user->setState($modelName . __CLASS__. 'defaultsSet', 1,1);
        }
    }

}




?>