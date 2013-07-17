<?php

/**
 * This is the model class for table "tbl_task_template_to_resource_to_mode".
 *
 * The followings are the available columns in table 'tbl_task_template_to_resource_to_mode':
 * @property string $id
 * @property integer $task_template_to_resource_id
 * @property integer $mode_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property TaskTemplateToResource $taskTemplateToResource
 * @property Mode $mode
 * @property User $updatedBy
 */
class TaskTemplateToResourceToMode extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Mode';

	public $searchMode;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_template_to_resource_id, mode_id, updated_by', 'required'),
			array('task_template_to_resource_id, mode_id, updated_by', 'numerical', 'integerOnly'=>true),
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
			'taskTemplateToResource' => array(self::BELONGS_TO, 'TaskTemplateToResource', 'task_template_to_resource_id'),
			'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'searchMode' => 'Mode',
		);
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			'mode.description AS searchMode',
		);

		$criteria->compare('mode.description',$this->searchMode,true);
		$criteria->compare('t.task_template_to_resource_id', $this->task_template_to_resource_id);

		$criteria->with = array(
			'mode',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
 		$columns[] = 'searchMode';

		return $columns;
	}

	public static function getDisplayAttr()
	{
		return array(
			'mode->description',
		);
	}
 
}

?>