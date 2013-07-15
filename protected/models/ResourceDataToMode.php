<?php

/**
 * This is the model class for table "tbl_resource_data_to_mode".
 *
 * The followings are the available columns in table 'tbl_resource_data_to_mode':
 * @property string $id
 * @property string $resource_data_id
 * @property integer $mode_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property ResourceData $resourceData
 * @property Mode $mode
 * @property User $updatedBy
 */
class ResourceDataToMode extends ActiveRecord
{
	public $searchMode;
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('resource_data_id, mode_id, updated_by', 'required'),
			array('mode_id, updated_by', 'numerical', 'integerOnly'=>true),
			array('resource_data_id', 'length', 'max'=>10),
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
			'resourceData' => array(self::BELONGS_TO, 'ResourceData', 'resource_data_id'),
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
		$criteria->compare('t.resource_data_id', $this->resource_data_id);

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
 
	/*
	 * Need to override becuase parent is ResourceData however this accessed from TaskToResource
	 */
	public function assertFromParent($modelName = null)
	{
		if(!empty($_GET['task_to_resource_id']))
		{
			$taskToResource = TaskToResource::model()->findByPk($_GET['task_to_resource_id']);
			return $taskToResource->assertFromParent();
		}
	}

}

?>