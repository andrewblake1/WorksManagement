<?php

/**
 * This is the model class for table "resource_type".
 *
 * The followings are the available columns in table 'resource_type':
 * @property integer $id
 * @property string $description
 * @property integer $resourcecategory_id
 * @property integer $maximum
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Staff $staff
 * @property Resourcecategory $resourcecategory
 * @property TaskToResourceType[] $taskToResourceTypes
 * @property TaskTypeToResourceType[] $taskTypeToResourceTypes
 */
class ResourceType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchResourcecategory;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ResourceType the static model class
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
		return 'resource_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, maximum, staff_id', 'required'),
			array('resourcecategory_id, maximum, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, searchResourcecategory, maximum, deleted, searchStaff', 'safe', 'on'=>'search'),
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
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'resourcecategory' => array(self::BELONGS_TO, 'Resourcecategory', 'resourcecategory_id'),
			'taskToResourceTypes' => array(self::HAS_MANY, 'TaskToResourceType', 'resource_type_id'),
			'taskTypeToResourceTypes' => array(self::HAS_MANY, 'TaskTypeToResourceType', 'resource_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Resource type',
			'resourcecategory_id' => 'Resource category',
			'searchResourcecategory' => 'Resource category',
			'maximum' => 'Maximum',
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
			't.id',
			't.description',
			'resourcecategory.description AS searchResourcecategory',
			't.maximum',
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('resourcecategory.description',$this->searchResourcecategory,true);
		$criteria->compare('t.maximum',$this->maximum);
		if(isset($this->resource_type_id))
		{
			$criteria->compare('t.resource_type_id', $this->resource_type_id);
		}
		else
		{
			$criteria->compare('resourcecategory.description',$this->searchResourcecategory,true);
			$criteria->select[]='resourcecategory.description AS searchResourcecategory';
		}
		
		// join
		$criteria->with = array('resourcecategory');

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'id';
		$columns[] = 'description';
 		if(!isset($this->dutycategory_id))
		{
			$columns[] = array(
				'name'=>'searchResourcecategory',
				'value'=>'CHtml::link($data->searchResourcecategory,
					Yii::app()->createUrl("Resourcecategory/update", array("id"=>$data->resourcecategory_id))
				)',
				'type'=>'raw',
			);
		}
		$columns[] = 'maximum';
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchResourcecategory');
	}
}

?>