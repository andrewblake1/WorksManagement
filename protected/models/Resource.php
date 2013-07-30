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
	public $searchLevel;
	public $searchAction;

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
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchLevel', $this->searchLevel, 'level0.name', true);
		$criteria->compareAs('searchAction', $this->searchAction, 'action.description', true);

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