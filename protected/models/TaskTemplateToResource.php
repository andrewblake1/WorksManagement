<?php

/**
 * This is the model class for table "tbl_task_template_to_resource".
 *
 * The followings are the available columns in table 'tbl_task_template_to_resource':
 * @property integer $id
 * @property integer $task_template_id
 * @property integer $resource_id
 * @property integer $mode_id
 * @property integer $quantity
 * @property string $duration
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property TaskTemplate $taskTemplate
 * @property Resource $resource
 * @property User $updatedBy
 * @property Mode $mode
 */
class TaskTemplateToResource extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchResource;
	public $searchTaskTemplate;
	public $searchMode;

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
            'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
        );
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
			'mode.description AS searchMode',
		);

		// where
		$criteria->compare('mode.description',$this->searchMode,true);
		$criteria->compare('resource.description',$this->searchResource);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.duration',Yii::app()->format->toMysqlTime($this->duration));
		$criteria->compare('t.task_template_id',$this->task_template_id);

		// with
		$criteria->with = array(
			'resource',
			'mode',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchResource', 'Resource', 'resource_id');
 		$columns[] = 'quantity';
		$columns[] = 'duration';
 		$columns[] = 'searchMode';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchResource',
			'searchMode',
		);
	}

}