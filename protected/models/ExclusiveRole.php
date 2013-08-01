<?php

/**
 * This is the model class for table "tbl_exclusive_role".
 *
 * The followings are the available columns in table 'tbl_exclusive_role':
 * @property string $id
 * @property string $parent_id
 * @property string $child_id
 * @property string $planning_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property RoleData $planning
 * @property RoleData $parent
 * @property RoleData $child
 */
class ExclusiveRole extends ActiveRecord
{
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
			'planning' => array(self::BELONGS_TO, 'RoleData', 'planning_id'),
			'parent' => array(self::BELONGS_TO, 'RoleData', 'parent_id'),
			'child' => array(self::BELONGS_TO, 'RoleData', 'child_id'),
		);
	}
   public function attributeLabels()
    {
        return array(
            'child_id' => 'Exclusive to',
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchExclusiveTo', $this->searchExclusiveTo, 'child.auth_item_name', true);

		$criteria->with = array(
			'childDutyStep',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchExclusiveTo';
 		
		return $columns;
	}

	public static function getDisplayAttr()
	{
		return array(
			'searchExclusiveTo',
		);
	}
 
	/**
	 * Returns foreign key attribute name within this model that references another model.
	 * @param string $referencesModel the name name of the model that the foreign key references.
	 * @return string the foreign key attribute name within this model that references another model
	 */
	static function getParentForeignKey($referencesModel)
	{
		return parent::getParentForeignKey($referencesModel, array('RoleData'=>'parent_id'));
	}	
	
	public function beforeValidate()
	{
		$this->planning_id = $this->parent->planning_id;
		parent::beforeValidate();
	}

}