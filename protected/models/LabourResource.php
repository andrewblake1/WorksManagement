<?php

/**
 * This is the model class for table "tbl_labour_resource".
 *
 * The followings are the available columns in table 'tbl_labour_resource':
 * @property integer $id
 * @property string $auth_item_name
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property AuthItem $authItemName
 * @property LabourResourceToSupplier[] $labourResourceToSuppliers
 * @property TaskTemplateToLabourResource[] $taskTemplateToLabourResources
 */
class LabourResource extends ActiveRecord
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
            'authItemName' => array(self::BELONGS_TO, 'AuthItem', 'auth_item_name'),
            'labourResourceToSuppliers' => array(self::HAS_MANY, 'LabourResourceToSupplier', 'labour_resource_id'),
            'taskTemplateToLabourResources' => array(self::HAS_MANY, 'TaskTemplateToLabourResource', 'labour_resource_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'auth_item_name' => 'Role',
		));
	}

	public function getAdminColumns()
	{
		$columns[] = 'auth_item_name';
		
		return $columns;
	}
	
	public static function getDisplayAttr()
	{
		return array(
			'auth_item_name',
		);
	}

}

?>