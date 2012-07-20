<?php

/**
 * This is the model class for table "generic_project_type".
 *
 * The followings are the available columns in table 'generic_project_type':
 * @property integer $id
 * @property integer $generic_type_id
 * @property integer $generic_project_category_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property GenericType $genericType
 * @property Genericprojectcategory $genericProjectCategory
 * @property Staff $staff
 * @property ProjectToGenericProjectType[] $projectToGenericProjectTypes
 */
class GenericProjectType extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GenericProjectType the static model class
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
		return 'generic_project_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('generic_type_id, staff_id', 'required'),
			array('generic_type_id, generic_project_category_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, generic_type_id, generic_project_category_id, deleted, staff_id', 'safe', 'on'=>'search'),
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
			'genericType' => array(self::BELONGS_TO, 'GenericType', 'generic_type_id'),
			'genericProjectCategory' => array(self::BELONGS_TO, 'Genericprojectcategory', 'generic_project_category_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'projectToGenericProjectTypes' => array(self::HAS_MANY, 'ProjectToGenericProjectType', 'generic_project_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'generic_type_id' => 'Generic Type',
			'generic_project_category_id' => 'Generic Project Category',
			'deleted' => 'Deleted',
			'staff_id' => 'Staff',
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
		$criteria->compare('generic_type_id',$this->generic_type_id);
		$criteria->compare('generic_project_category_id',$this->generic_project_category_id);
		$criteria->compare('deleted',$this->deleted);
		$criteria->compare('staff_id',$this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}