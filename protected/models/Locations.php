<?php

/**
 * This is the model class for table "locations".
 *
 * The followings are the available columns in table 'locations':
 * @property string $location_id
 * @property string $location_name
 * @property string $location_short_name
 * @property string $location_parent
 * @property double $location_coord_lat
 * @property double $location_coord_lng
 * @property integer $location_zoom
 */
class Locations extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @return Locations the static model class
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
        return 'locations';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('location_name', 'required'),
            array('location_zoom', 'numerical', 'integerOnly'=>true),
            array('location_coord_lat, location_coord_lng', 'numerical'),
            array('location_name, location_short_name', 'length', 'max'=>255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('location_id, location_name, location_short_name, location_parent, location_coord_lat, location_coord_lng, location_zoom', 'safe', 'on'=>'search'),
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
            'location_id' => 'Location',
            'location_name' => 'Location Name',
            'location_short_name' => 'Location Short Name',
            'location_parent' => 'Location Parent',
            'location_coord_lat' => 'Location Coord Lat',
            'location_coord_lng' => 'Location Coord Lng',
            'location_zoom' => 'Location Zoom',
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

        $criteria->compare('location_id',$this->location_id,true);
        $criteria->compare('location_name',$this->location_name,true);
        $criteria->compare('location_short_name',$this->location_short_name,true);
        $criteria->compare('location_parent',$this->location_parent,true);
        $criteria->compare('location_coord_lat',$this->location_coord_lat);
        $criteria->compare('location_coord_lng',$this->location_coord_lng);
        $criteria->compare('location_zoom',$this->location_zoom);

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
        ));
    }
}