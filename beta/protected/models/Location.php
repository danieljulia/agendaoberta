<?php

/**
 * This is the model class for table "ao_location".
 *
 * The followings are the available columns in table 'ao_location':
 * @property integer $id
 * @property string $name
 * @property double $lat
 * @property double $lng
 * @property string $address
 */
class Location extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Location the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ao_location';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('address', 'required'),
			array('lat, lng,pre', 'numerical'),
			array('name, address, formatted_address', 'length', 'max'=>128),
                        array('location_type', 'length', 'max'=>16),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, lat, lng, address', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'lat' => 'Lat',
			'lng' => 'Lng',
			'address' => 'Address',
                    'location_type' => 'Location Type',
			'pre' => 'Pre',
			'formatted_address' => 'Formatted Address',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('lat',$this->lat);
		$criteria->compare('lng',$this->lng);
		$criteria->compare('address',$this->address,true);
                $criteria->compare('location_type',$this->location_type,true);
		$criteria->compare('pre',$this->pre);
		$criteria->compare('formatted_address',$this->formatted_address,true);
                
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}