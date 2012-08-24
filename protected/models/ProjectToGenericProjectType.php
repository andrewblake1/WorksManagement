<?php

/**
 * This is the model class for table "project_to_generic_project_type".
 *
 * The followings are the available columns in table 'project_to_generic_project_type':
 * @property string $id
 * @property integer $generic_project_type_id
 * @property string $project_id
 * @property string $generic_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Generic $generic
 * @property Staff $staff
 * @property GenericProjectType $genericProjectType
 * @property Project $project
 */
class ProjectToGenericProjectType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchGenericProjectType;
	public $searchProject;
	public $searchGeneric;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectToGenericProjectType the static model class
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
		return 'project_to_generic_project_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('generic_project_type_id, project_id, generic_id, staff_id', 'required'),
			array('generic_project_type_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('project_id, generic_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, project_id, searchGenericProjectType, searchProject, searchGeneric, searchStaff', 'safe', 'on'=>'search'),
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
			'generic' => array(self::BELONGS_TO, 'Generic', 'generic_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'genericProjectType' => array(self::BELONGS_TO, 'GenericProjectType', 'generic_project_type_id'),
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Project to generic project type',
			'generic_project_type_id' => 'Project type/Generic type)',
			'searchGenericProjectType' => 'Project type/Generic type)',
			'project_id' => 'Client/Project',
			'searchProject' => 'Client/Project',
			'generic_id' => 'Generic',
			'searchGeneric' => 'Generic',
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
//			't.id',
			'generic.id AS searchGeneric',
			"CONCAT_WS('$delimiter',
				projectType.description,
				genericType.description
				) AS searchGenericProjectType",
		);

		// where
//		$criteria->compare('t.id',$this->id);
		$criteria->compare('generic.id',$this->searchGeneric);
		$this->compositeCriteria($criteria, array(
			'projectType.description',
			'genericType.description',
			), $this->searchGenericProjectType);

		if(isset($this->project_id))
		{
			$criteria->compare('t.project_id',$this->project_id);
		}
		else
		{
			$criteria->select[]="CONCAT_WS('$delimiter',
				client.name,
				project.description
				) AS searchProject";
			$this->compositeCriteria($criteria,
				array(
					'client.name',
					'project.description',
				),
				$this->searchProject
			);
		}

		// join
		$criteria->with = array(
			'genericProjectType.projectType',
			'genericProjectType.genericType',
			'genericProjectType.projectType.client',
			'project',
			'generic',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
        $columns[] = array(
			'name'=>'searchGenericProjectType',
			'value'=>'CHtml::link($data->searchGenericProjectType,
				Yii::app()->createUrl("GenericProjectType/update", array("id"=>$data->generic_project_type_id))
			)',
			'type'=>'raw',
		);
 		if(!isset($this->project_id))
		{
			$columns[] = array(
					'name'=>'searchProject',
					'value'=>'CHtml::link($data->searchProject,
						Yii::app()->createUrl("Project/update", array("id"=>$data->project_id))
					)',
					'type'=>'raw',
				);
		}
        $columns[] = array(
			'name'=>'searchGeneric',
			'value'=>'CHtml::link($data->searchGeneric,
				Yii::app()->createUrl("Generic/update", array("id"=>$data->generic_id))
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
		return array('searchGenericProjectType', 'searchProject', 'searchGeneric');
	}
}

?>