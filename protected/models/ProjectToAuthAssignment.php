<?php

/**
 * This is the model class for table "project_to_AuthAssignment".
 *
 * The followings are the available columns in table 'project_to_AuthAssignment':
 * @property string $id
 * @property string $project_id
 * @property integer $AuthAssignment_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Project $project
 * @property AuthAssignment $authAssignment
 * @property Staff $staff
 */
class ProjectToAuthAssignment extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchProject;
	public $searchAuthAssignment;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectToAuthAssignment the static model class
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
		return 'project_to_AuthAssignment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_id, AuthAssignment_id, staff_id', 'required'),
			array('AuthAssignment_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('project_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, project_id, searchProject, searchAuthAssignment, searchStaff', 'safe', 'on'=>'search'),
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
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
			'authAssignment' => array(self::BELONGS_TO, 'AuthAssignment', 'AuthAssignment_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Project to auth assignment',
			'project_id' => 'Client/Project',
			'searchProject' => 'Client/Project',
			'AuthAssignment_id' => 'Role/First/Last/Email',
			'searchAuthAssignment' => 'Role/First/Last/Email',
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
			"CONCAT_WS('$delimiter',
				authAssignment.itemname,
				user.first_name,
				user.last_name,
				user.email
				) AS searchAuthAssignment",
		);

		// where
//		$criteria->compare('t.id',$this->id);
		$this->compositeCriteria(
			$criteria,
			array(
				'authAssignment.itemname',
				'user.first_name',
				'user.last_name',
				'user.email'
			),
			$this->searchAuthAssignment
		);
		
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
			'authAssignment.itemname0',
			'authAssignment.user',
			'project',
			'project.projectType.client',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
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
			'name'=>'searchAuthAssignment',
			'value'=>'CHtml::link($data->searchAuthAssignment,
				Yii::app()->createUrl("AuthAssignment/update", array("id"=>$data->AuthAssignment_id))
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
		return array('searchProject', 'searchAuthAssignment');
	}
	
	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
//		// show when not coming from parent
//		if(!isset($_GET[ucfirst(Yii::app()->controller->id)]['project_id']) && !isset($_GET[$_GET['model']]))
		// if this pk attribute has been passed in a higher crumb in the breadcrumb trail
		if(Yii::app()->getController()->primaryKeyInBreadCrumbTrail('project_to_AuthAssignment_id'))
		{
			ActiveRecord::$labelOverrides['project_to_AuthAssignment_id'] = 'Client/Project/Role/First/Last/Email';
			$displaAttr['project->client']='name';
			$displaAttr['project']='description';
			$displaAttr['authAssignment']='itemname';
			$displaAttr['authAssignment->user']='first_name';
			$displaAttr['authAssignment->user']='last_name';
			$displaAttr['authAssignment->user']='email';
		}
		else
		{
			ActiveRecord::$labelOverrides['project_to_AuthAssignment_id'] = 'Role/First/Last/Email';
			$displaAttr['authAssignment']='itemname';
			$displaAttr['authAssignment->user']='first_name';
			$displaAttr['authAssignment->user']='last_name';
			$displaAttr['authAssignment->user']='email';
		}

		return $displaAttr;
	}

}

?>