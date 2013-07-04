<?php

/**
 * This is the model class for table "tbl_task_template_to_resource".
 *
 * The followings are the available columns in table 'tbl_task_template_to_resource':
 * @property integer $id
 * @property integer $task_template_id
 * @property integer $resource_id
 * @property integer $quantity
 * @property string $duration
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property TaskTemplate $taskTemplate
 * @property Resource $resource
 * @property User $updatedBy
 */
class TaskTemplateToResource extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchResource;
	public $searchTaskTemplate;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Resource';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('task_template_id, resource_id, quantity', 'required'),
			array('task_template_id, resource_id, quantity', 'numerical', 'integerOnly'=>true),
			array('duration', 'date', 'format'=>'H:m'),
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
            'resource' => array(self::BELONGS_TO, 'Resource', 'resource_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'task_template_id' => 'Task Type',
			'resource_id' => 'Resource Type',
			'duration' => 'Duration (HH:mm)',
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
			't.resource_id',
			'resource.description AS searchResource',
			't.quantity',
			't.duration',
		);

		// where
		$criteria->compare('resource.description',$this->searchResource);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.duration',Yii::app()->format->toMysqlTime($this->duration));
		$criteria->compare('t.task_template_id',$this->task_template_id);

		// with
		$criteria->with = array(
			'resource',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchResource', 'Resource', 'resource_id');
 		$columns[] = 'quantity';
		$columns[] = 'duration';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'resource->description',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchResource', 'searchTaskTemplate');
	}
}