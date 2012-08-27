<?php

/**
 * This is the model class for table "duty_type".
 *
 * The followings are the available columns in table 'duty_type':
 * @property integer $id
 * @property string $description
 * @property integer $lead_in_days
 * @property integer $dutycategory_id
 * @property integer $generic_type_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property TaskTypeToDutyType[] $taskTypeToDutyTypes
 * @property Dutycategory $dutycategory
 * @property GenericType $genericType
 * @property Staff $staff
 */
class DutyType extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Duty';

	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchDutycategory;
	public $searchGenericType;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'duty_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, staff_id', 'required'),
			array('lead_in_days, dutycategory_id, generic_type_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, lead_in_days, dutycategory_id, searchDutycategory, searchGenericType, searchStaff', 'safe', 'on'=>'search'),
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
			'taskTypeToDutyTypes' => array(self::HAS_MANY, 'TaskTypeToDutyType', 'duty_type_id'),
			'dutycategory' => array(self::BELONGS_TO, 'Dutycategory', 'dutycategory_id'),
			'genericType' => array(self::BELONGS_TO, 'GenericType', 'generic_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Duty type',
			'lead_in_days' => 'Lead in days',
			'dutycategory_id' => 'Duty category',
			'searchDutycategory' => 'Duty category',
			'generic_type_id' => 'Custom type',
			'searchGenericType' => 'Custom type',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		// select
		$criteria->select=array(
//			't.id',
			't.description',
			't.lead_in_days',
			'genericType.description AS searchGenericType',
		);

		// where
//		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.lead_in_days',$this->lead_in_days);
		$criteria->compare('genericType.description',$this->searchGenericType,true);
		if(isset($this->duty_type_id))
		{
			$criteria->compare('t.duty_type_id', $this->duty_type_id);
		}
		else
		{
			$criteria->compare('dutycategory.description',$this->searchDutycategory,true);
			$criteria->select[]='dutycategory.description AS searchDutycategory';
		}
		
		// join
		$criteria->with = array('dutycategory','genericType');

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
		$columns[] = 'description';
		$columns[] = 'lead_in_days';
		if(!isset($this->dutycategory_id))
		{
			$columns[] = array(
				'name'=>'searchDutycategory',
				'value'=>'CHtml::link($data->searchDutycategory,
					Yii::app()->createUrl("Dutycategory/update", array("id"=>$data->dutycategory_id))
				)',
				'type'=>'raw',
			);
		}
        $columns[] = array(
			'name'=>'searchGenericType',
			'value'=>'CHtml::link($data->searchGenericType,
				Yii::app()->createUrl("GenericType/update", array("id"=>$data->generic_type_id))
			)',
			'type'=>'raw',
		);
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchDutycategory', 'searchGenericType');
	}
}

?>