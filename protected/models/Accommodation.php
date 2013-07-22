<?php

/**
 * This is the model class for table "accommodation".
 *
 * The followings are the available columns in table 'accommodation':
 * @property string $accommodation_id
 * @property string $accommodation_name
 * @property string $accommodation_description
 * @property integer $accommodation_sleeps
 * @property string $accommodation_price_min
 * @property string $accommodation_price_max
 * @property string $accommodation_create_date
 * @property string $accommodation_user_id
 * @property string $accommodation_type
 */
class Accommodation extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Accommodation the static model class
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
		return 'accommodation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('accommodation_name, accommodation_description, accommodation_create_date, accommodation_user_id', 'required'),
			array('accommodation_sleeps', 'numerical', 'integerOnly'=>true),
			array('accommodation_name', 'length', 'max'=>255),
			array('accommodation_price_min, accommodation_price_max, accommodation_create_date, accommodation_user_id', 'length', 'max'=>10),
			array('accommodation_type', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('accommodation_id, accommodation_name, accommodation_description, accommodation_sleeps, accommodation_price_min, accommodation_price_max, accommodation_create_date, accommodation_user_id, accommodation_type', 'safe', 'on'=>'search'),
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
			'accommodation_id' => 'Accommodation',
			'accommodation_name' => 'Accommodation Name',
			'accommodation_description' => 'Accommodation Description',
			'accommodation_sleeps' => 'Accommodation Sleeps',
			'accommodation_price_min' => 'Accommodation Price Min',
			'accommodation_price_max' => 'Accommodation Price Max',
			'accommodation_create_date' => 'Accommodation Create Date',
			'accommodation_user_id' => 'Accommodation User',
			'accommodation_type' => 'Accommodation Type',
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

		$criteria->compare('accommodation_id',$this->accommodation_id,true);
		$criteria->compare('accommodation_name',$this->accommodation_name,true);
		$criteria->compare('accommodation_description',$this->accommodation_description,true);
		$criteria->compare('accommodation_sleeps',$this->accommodation_sleeps);
		$criteria->compare('accommodation_price_min',$this->accommodation_price_min,true);
		$criteria->compare('accommodation_price_max',$this->accommodation_price_max,true);
		$criteria->compare('accommodation_create_date',$this->accommodation_create_date,true);
		$criteria->compare('accommodation_user_id',$this->accommodation_user_id,true);
		$criteria->compare('accommodation_type',$this->accommodation_type,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}