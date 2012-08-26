<?php

/**
 * This is the model class for table "project_type_to_AuthItem".
 *
 * The followings are the available columns in table 'project_type_to_AuthItem':
 * @property integer $id
 * @property integer $project_type_id
 * @property string $AuthItem_name
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property ProjectToProjectTypeToAuthItem[] $projectToProjectTypeToAuthItems
 * @property ProjectType $projectType
 * @property AuthItem $authItemName
 * @property Staff $staff
 * @property TaskTypeToDutyTypeToProjectTypeToAuthItem[] $taskTypeToDutyTypeToProjectTypeToAuthItems
 * @property TaskTypeToDutyTypeToProjectTypeToAuthItem[] $taskTypeToDutyTypeToProjectTypeToAuthItems1
 */
class ProjectTypeToAuthItem extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchProjectType;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Project type to role';
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectTypeToAuthItem the static model class
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
		return 'project_type_to_AuthItem';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_type_id, AuthItem_name, staff_id', 'required'),
			array('project_type_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('AuthItem_name', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('searchProjectType, id, project_type_id, AuthItem_name, deleted, staff_id', 'safe', 'on'=>'search'),
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
			'projectToProjectTypeToAuthItems' => array(self::HAS_MANY, 'ProjectToProjectTypeToAuthItem', 'project_type_to_AuthItem_id'),
			'projectType' => array(self::BELONGS_TO, 'ProjectType', 'project_type_id'),
			'authItemName' => array(self::BELONGS_TO, 'AuthItem', 'AuthItem_name'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskTypeToDutyTypeToProjectTypeToAuthItems' => array(self::HAS_MANY, 'TaskTypeToDutyTypeToProjectTypeToAuthItem', 'project_type_id'),
			'taskTypeToDutyTypeToProjectTypeToAuthItems1' => array(self::HAS_MANY, 'TaskTypeToDutyTypeToProjectTypeToAuthItem', 'project_type_to_AuthItem_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Project type to role',
			'project_type_id' => 'Project type',
			'AuthItem_name' => 'Role',
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
			'AuthItem_name',
		);

		// where
		$criteria->compare('AuthItem_name',$this->AuthItem_name,true);

		if(isset($this->project_type_id))
		{
			$criteria->compare('t.project_type_id',$this->project_type_id);
		}
		else
		{
			$criteria->select[]="CONCAT_WS('$delimiter',
				client.name,
				projectType.description
				) AS searchProjectType";
			$this->compositeCriteria($criteria,
				array(
					'client.name',
					'projectType.description',
				),
				$this->searchProjectType
			);
		}
		
		// join
		$criteria->with = array('projectType', 'projectType.client');

		return $criteria;
	}
	
	public function getAdminColumns()
	{
//		$columns[] = 'id';
		$columns[] = 'AuthItem_name';
  		if(!isset($this->project_type_id))
		{
			$columns[] = array(
				'name'=>'searchProjectType',
				'value'=>'CHtml::link($data->searchProjectType,
					Yii::app()->createUrl("ProjectType/update", array("id"=>$data->project_type_id))
				)',
				'type'=>'raw',
			);
		}
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		// if this pk attribute has been passed in a higher crumb in the breadcrumb trail
		if(Yii::app()->getController()->primaryKeyInBreadCrumbTrail('project_type_id')
			|| Yii::app()->getController()->primaryKeyInBreadCrumbTrail('project_id'))
			
		{
			ActiveRecord::$labelOverrides['project_type_to_AuthItem_id'] = 'Role';
		}
		else
		{
			ActiveRecord::$labelOverrides['project_type_to_AuthItem_id'] = 'Client/Project type/Role';
			$displaAttr[]='projectType->client->name';
			$displaAttr[]='projectType->description';
		}

		$displaAttr[]='AuthItem_name';

		return $displaAttr;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchProjectType');
	}

}