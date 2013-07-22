<?php

/**
 * This is the model class for table "accommodation_images".
 *
 * The followings are the available columns in table 'accommodation_images':
 * @property string $ai_id
 * @property string $ai_accommodation_id
 * @property string $ai_name
 */
class AccommodationImages extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return AccommodationImages the static model class
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
		return 'accommodation_images';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ai_accommodation_id, ai_name', 'required'),
			array('ai_accommodation_id', 'length', 'max'=>10),
			array('ai_name', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ai_id, ai_accommodation_id, ai_name', 'safe', 'on'=>'search'),
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
			'ai_id' => 'Ai',
			'ai_accommodation_id' => 'Ai Accommodation',
			'ai_name' => 'Ai Name',
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

		$criteria->compare('ai_id',$this->ai_id,true);
		$criteria->compare('ai_accommodation_id',$this->ai_accommodation_id,true);
		$criteria->compare('ai_name',$this->ai_name,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}