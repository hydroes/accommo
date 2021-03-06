<?php

/**
 * This is the model class for table "accommodation_location_mappings".
 *
 * The followings are the available columns in table 'accommodation_location_mappings':
 * @property string $alm_id
 * @property string $alm_accommodation_id
 * @property string $alm_location_id
 */
class AccommodationLocationMappings extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return AccommodationLocationMappings the static model class
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
		return 'accommodation_location_mappings';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('alm_accommodation_id, alm_location_id', 'required'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('alm_id, alm_accommodation_id, alm_location_id', 'safe', 'on'=>'search'),
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
			'alm_id' => 'Alm',
			'alm_accommodation_id' => 'Alm Accommodation',
			'alm_location_id' => 'Alm Location',
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

		$criteria->compare('alm_id',$this->alm_id,true);
		$criteria->compare('alm_accommodation_id',$this->alm_accommodation_id,true);
		$criteria->compare('alm_location_id',$this->alm_location_id,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}