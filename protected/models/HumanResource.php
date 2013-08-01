<?php

/**
 * This is the model class for table "tbl_human_resource".
 *
 * The followings are the available columns in table 'tbl_human_resource':
 * @property integer $id
 * @property string $level
 * @property string $auth_item_name
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
 * @property AuthItem $authItemName
 * @property HumanResourceToSupplier[] $humanResourceToSuppliers
 * @property TaskTemplateToHumanResource[] $taskTemplateToHumanResources
 */
class HumanResource extends ActiveRecord
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
            'authItemName' => array(self::BELONGS_TO, 'AuthItem', 'auth_item_name'),
            'humanResourceToSuppliers' => array(self::HAS_MANY, 'HumanResourceToSupplier', 'human_resource_id'),
            'taskTemplateToHumanResources' => array(self::HAS_MANY, 'TaskTemplateToHumanResource', 'human_resource_id'),
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
		$columns[] = 'auth_item_name';
		$columns[] = 'unit_price';
		$columns[] = 'maximum';
		$columns[] = 'searchLevel';
		$columns[] = 'searchAction';
		
		return $columns;
	}

}

?>