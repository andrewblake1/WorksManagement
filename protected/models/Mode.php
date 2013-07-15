<?php

/**
 * This is the model class for table "tbl_mode".
 *
 * The followings are the available columns in table 'tbl_mode':
 * @property integer $id
 * @property string $description
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property DutyStepToMode[] $dutyStepToModes
 * @property User $updatedBy
 * @property ResourceDataToMode[] $resourceDataToModes
 * @property Task[] $tasks
 * @property TaskTemplateToResourceToMode[] $taskTemplateToResourceToModes
 */
class Mode extends ActiveRecord
{
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, updated_by', 'required'),
			array('deleted, updated_by', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>255),
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
			'dutyStepToModes' => array(self::HAS_MANY, 'DutyStepToMode', 'mode_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
			'resourceDataToModes' => array(self::HAS_MANY, 'ResourceDataToMode', 'mode_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'mode_id'),
			'taskTemplateToResourceToModes' => array(self::HAS_MANY, 'TaskTemplateToResourceToMode', 'mode_id'),
		);
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$criteria->compare('t.description',$this->description,true);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'description';
		
		return $columns;
	}

}

?>