<?php

/**
 * This is the model class for table "project_type".
 *
 * The followings are the available columns in table 'project_type':
 * @property integer $id
 * @property string $description
 * @property integer $client_id
 * @property string $template_project_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property GenericProjectType[] $genericProjectTypes
 * @property Project[] $projects
 * @property Staff $staff
 * @property Project $templateProject
 * @property Client $client
 * @property ProjectTypeToAuthItem[] $projectTypeToAuthItems
 * @property TaskType[] $taskTypes 
 */
class ProjectType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchClient;
	public $searchTemplateProject;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'project_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, client_id, staff_id', 'required'),
			array('client_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			array('template_project_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, client_id, deleted, searchTemplateProject, searchClient, searchStaff', 'safe', 'on'=>'search'),
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
			'genericProjectTypes' => array(self::HAS_MANY, 'GenericProjectType', 'project_type_id'),
			'projects' => array(self::HAS_MANY, 'Project', 'project_type_id'),
			'templateProject' => array(self::BELONGS_TO, 'Project', 'template_project_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
			'projectTypeToAuthItems' => array(self::HAS_MANY, 'ProjectTypeToAuthItem', 'project_type_id'),
			'taskTypes' => array(self::HAS_MANY, 'TaskType', 'project_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Project type',
			'client_id' => 'Client',
			'searchClient' => 'Client',
			'template_project_id' => 'Template project',
			'searchTemplateProject' => 'Template project',
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
			't.template_project_id',
		);

		// where
//		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.template_project_id',$this->template_project_id,true);
//		$criteria->compare('templateProject.description',$this->searchTemplateProject,true);
		if(isset($this->client_id))
		{
			$criteria->compare('t.client_id', $this->client_id);
		}
		else
		{
			$criteria->compare('client.name',$this->searchClient,true);
			$criteria->select[]="client.name AS searchClient";
		}
		
		// join
		$criteria->with = array('client', 'templateProject');

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
		$columns[] = 'description';
		if(!isset($this->client_id))
		{
			$columns[] = array(
				'name'=>'searchClient',
				'value'=>'CHtml::link($data->searchClient,
					Yii::app()->createUrl("Client/update", array("id"=>$data->client_id))
				)',
				'type'=>'raw',
			);
		}
        $columns[] = array(
			'name'=>'searchTemplateProject',
			'value'=>'CHtml::link($data->searchTemplateProject,
				Yii::app()->createUrl("TemplateProject/update", array("id"=>$data->template_project_id))
			)',
			'type'=>'raw',
		);
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
/*	public static function getDisplayAttr()
	{
/*		// if this pk attribute has been passed in a higher crumb in the breadcrumb trail
		if(Yii::app()->getController()->primaryKeyInBreadCrumbTrail('client_id'))
		{
			ActiveRecord::$labelOverrides['project_type_id'] = 'Project type';
		}
		else
		{
			ActiveRecord::$labelOverrides['project_type_id'] = 'Client/Project type';
			$displaAttr[]='client->name';
		}

		$displaAttr[]='description';

		return $displaAttr;
	}*/

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchClient', 'searchTemplateProject');
	}

}

?>