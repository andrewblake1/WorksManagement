<?php

/**
 * This is the model class for table "project".
 *
 * The followings are the available columns in table 'project':
 * @property string $id
 * @property string $description
 * @property integer $project_type_id
 * @property string $travel_time_1_way
 * @property string $critical_completion
 * @property string $planned
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property ProjectType $projectType
 * @property Staff $staff
 * @property ProjectToGenericProjectType[] $projectToGenericProjectTypes
 * @property ProjectToProjectTypeToAuthItem[] $projectToProjectTypeToAuthItems
 * @property ProjectType[] $projectTypes
 * @property Task[] $tasks
 */
class Project extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchProjectType;
	/**
	 * @var integer $client_id may be passed via get for search
	 */
	public $client_id;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'project';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, project_type_id, staff_id', 'required'),
			array('project_type_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>255),
			array('travel_time_1_way, critical_completion, planned', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, travel_time_1_way, critical_completion, planned, searchStaff, searchProjectType, client_id', 'safe', 'on'=>'search'),
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
			'projectType' => array(self::BELONGS_TO, 'ProjectType', 'project_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'projectToGenericProjectTypes' => array(self::HAS_MANY, 'ProjectToGenericProjectType', 'project_id'),
			'projectToProjectTypeToAuthItems' => array(self::HAS_MANY, 'ProjectToProjectTypeToAuthItem', 'project_id'),
			'projectTypes' => array(self::HAS_MANY, 'ProjectType', 'template_project_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'project_id'),
		);
	}
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Project',
			'travel_time_1_way' => 'Travel time 1 way',
			'critical_completion' => 'Critical completion',
			'planned' => 'Planned',
			'project_type_id' => 'Client/Project type',
			'searchProjectType' => 'Client/Project type',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',
			't.description',
			'travel_time_1_way',
			't.critical_completion',
			't.planned',
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.travel_time_1_way',$this->travel_time_1_way);
		$criteria->compare('t.critical_completion',$this->critical_completion);
		$criteria->compare('t.planned',$this->planned);
		if(isset($this->client_id))
		{
			ActiveRecord::$labelOverrides['searchProjectType'] = 'Project Type';
			$criteria->compare('client.id', $this->client_id);
			$criteria->compare('projectType.description', $this->searchProjectType, true);
			$criteria->select[] = "projectType.description AS searchProjectType";
		}
		else
		{
			$this->compositeCriteria(
				$criteria,
				array(
					'client.name',
					'projectType.description'
				),
				$this->searchProjectType
			);
			$criteria->select[]="CONCAT_WS('$delimiter',
				client.name,
				projectType.description
				) AS searchProjectType";
		}

		// join
		$criteria->with = array('projectType.client', 'projectType');

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'id';
		$columns[] = 'description';
		$columns[] = array(
			'name'=>'searchProjectType',
			'value'=>'CHtml::link($data->searchProjectType,
				Yii::app()->createUrl("ProjectType/update", array("id"=>$data->project_type_id))
			)',
			'type'=>'raw',
		);
		$columns[] = 'travel_time_1_way';
		$columns[] = 'critical_completion';
		$columns[] = 'planned';
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchProjectType');
	}

/* TODO: possibly re-engineer this to put this method in the controller. This also probably means moving a couple of other methods from
 * active record to controller also. Breaking a design rule here and creating tight coupling between the controller and the model
 * it is ok for controller to have knowledge of a model but not the other way around. Not using this in any of the associated static model functions
 * currently
 */
	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
/*	public static function getDisplayAttr()
	{
		// if this pk attribute has been passed in a higher crumb in the breadcrumb trail
		if(Yii::app()->getController()->primaryKeyInBreadCrumbTrail('client_id'))
		{
			ActiveRecord::$labelOverrides['project_id'] = 'Project';
		}
		else
		{
			ActiveRecord::$labelOverrides['project_id'] = 'Client/Project';
			$displaAttr[]='projectType->client->name';
		}

		$displaAttr[]='description';

		return $displaAttr;
	}*/

}

?>