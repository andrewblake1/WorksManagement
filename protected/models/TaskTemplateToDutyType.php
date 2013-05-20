<?php

/**
 * This is the model class for table "tbl_task_template_to_duty_type".
 *
 * The followings are the available columns in table 'tbl_task_template_to_duty_type':
 * @property integer $id
 * @property integer $task_template_id
 * @property integer $project_template_id
 * @property integer $duty_type_id
 * @property integer $project_template_to_auth_item_id
 * @property string $importance
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property TaskTemplate $taskTemplate
 * @property ProjectTemplateToAuthItem $projectTemplate
 * @property DutyType $dutyType
 * @property User $updatedBy
 * @property ProjectTemplateToAuthItem $projectTemplateToAuthItem
 */
class TaskTemplateToDutyType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchDutyType;
	public $searchTaskTemplate;
	public $searchProjectTemplateToAuthItem;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Duty type';

	/**
	 * Importance. These are the emum values set by the importance custom type within 
	 * the database
	 */
	const importanceStandard = 'Standard';
	const importanceOptional = 'Optional';
	
	/**
	 * Returns importance labels.
	 * @return array data importance - to match enum type in mysql workbench
	 */
	public static function getImportanceLabels()
	{
		return array(
			self::importanceStandard=>self::importanceStandard,
			self::importanceOptional=>self::importanceOptional,
		);
	}

	/**
	 * Returns data type column names.
	 * @return array data storage types - to match enum type in mysql workbench
	 */
	public static function getImportanceColumnNames()
	{
		return array(
			self::importanceStandard=>'importanceStandard',
			self::importanceOptional=>'importanceOptional',
		);
	}

	public function scopeTask($task_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('task.id',$task_id);
		$criteria->join='JOIN tbl_task task USING(task_template_id)';
		
		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('task_template_id, project_template_id, duty_type_id, project_template_to_auth_item_id, importance', 'required'),
			array('task_template_id, project_template_id, duty_type_id, project_template_to_auth_item_id', 'numerical', 'integerOnly'=>true),
			array('importance', 'length', 'max'=>8),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, task_template_id, importance, searchDutyType, searchTaskTemplate, searchProjectTemplateToAuthItem', 'safe', 'on'=>'search'),
		));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'taskTemplate' => array(self::BELONGS_TO, 'TaskTemplate', 'task_template_id'),
            'projectTemplate' => array(self::BELONGS_TO, 'ProjectTemplateToAuthItem', 'project_template_id'),
            'dutyType' => array(self::BELONGS_TO, 'DutyType', 'duty_type_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'projectTemplateToAuthItem' => array(self::BELONGS_TO, 'ProjectTemplateToAuthItem', 'project_template_to_auth_item_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'duty_type_id' => 'Duty type',
			'searchDutyType' => 'Duty type',
			'task_template_id' => 'Client/Project type/Task type',
			'searchTaskTemplate' => 'Client/Project type/Task type',
			'searchProjectTemplateToAuthItem' => 'Role',
			'importance' => 'Standard Optional',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.duty_type_id',
			't.project_template_to_auth_item_id',
			'dutyType.description AS searchDutyType',
			'projectTemplateToAuthItem.auth_item_name AS searchProjectTemplateToAuthItem',
			't.importance',
		);

		// where
		$criteria->compare('dutyType.description',$this->searchDutyType);
		$criteria->compare('projectTemplateToAuthItem.auth_item_name',$this->searchProjectTemplateToAuthItem);
		$criteria->compare('t.task_template_id',$this->task_template_id);
		$criteria->compare('t.importance',$this->importance,true);

		// with
		$criteria->with = array(
			'dutyType',
			'taskTemplate',
			'taskTemplate.projectTemplate',
			'projectTemplateToAuthItem'
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchDutyType', 'DutyType', 'duty_type_id');
//        $columns[] = static::linkColumn('searchProjectTemplateToAuthItem', 'ProjectTemplateToAuthItem', 'project_template_to_auth_item_id');
        $columns[] = 'searchProjectTemplateToAuthItem';
		$columns[] = 'importance';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'dutyType->description',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchDutyType', 'searchProjectTemplateToAuthItem');
	}

	public function beforeValidate()
	{
		// need to set project_template_id which is an extra foreign key to make circular foreign key constraint
		if(isset($this->project_template_to_auth_item_id))
		{
			$projectTemplateToAuthItem = ProjectTemplateToAuthItem::model()->findByPk($this->project_template_to_auth_item_id);
			$this->project_template_id = $projectTemplateToAuthItem->project_template_id;
		}

		return parent::beforeValidate();
	}
}

?>