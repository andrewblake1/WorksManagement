<?php

/**
 * This is the model class for table "tbl_resource".
 *
 * The followings are the available columns in table 'tbl_resource':
 * @property integer $id
 * @property string $description
 * @property string $level
 * @property string $unit_price
 * @property integer $maximum
 * @property string $action_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property Level $level0
 * @property Action $action
 * @property ResourceToSupplier[] $resourceToSuppliers
 * @property TaskTemplateToResource[] $taskTemplateToResources
 */
class Resource extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Resource';

	public $searchLevel;
	public $searchAction;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('description', 'required'),
			array('maximum', 'numerical', 'integerOnly'=>true),
			array('unit_price', 'length', 'max'=>7),
            array('level, action_id', 'length', 'max'=>10),
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
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'level0' => array(self::BELONGS_TO, 'Level', 'level'),
            'action' => array(self::BELONGS_TO, 'Action', 'action_id'),
            'resourceToSuppliers' => array(self::HAS_MANY, 'ResourceToSupplier', 'resource_id'),
            'taskTemplateToResources' => array(self::HAS_MANY, 'TaskTemplateToResource', 'resource_id'),
        );
    }


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'unit_price' => 'Unit price',
			'maximum' => 'Maximum',
			'searchLevel' => 'Level',
			'searchAction' => 'Action',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.id',
			't.description',
			't.unit_price',
			't.maximum',
			'level0.name AS searchLevel',
			'action.description AS searchAction',
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.unit_price',$this->unit_price);
		$criteria->compare('t.maximum',$this->maximum);
		$criteria->compare('level0.name',$this->searchLevel,true);
		$criteria->compare('action.description',$this->searchAction,true);

		// with
		$criteria->with = array(
			'level0',
			'action',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'description';
		$columns[] = 'unit_price';
		$columns[] = 'maximum';
		$columns[] = 'searchLevel';
		$columns[] = 'searchAction';
		
		return $columns;
	}

}

?>