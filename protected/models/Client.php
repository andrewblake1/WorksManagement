<?php

/**
 * This is the model class for table "tbl_client".
 *
 * The followings are the available columns in table 'tbl_client':
 * @property integer $id
 * @property string $name
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property ClientToAssembly[] $clientToAssemblys
 * @property User $updatedBy
 * @property ClientContact[] $clientContacts
 * @property ClientToMaterial[] $clientToMaterials
 * @property ProjectTemplate[] $projectTemplates
 */
class Client extends ActiveRecord
{
	use FileActiveRecordTrait;

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'clientToAssemblys' => array(self::HAS_MANY, 'ClientToAssembly', 'client_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'clientContacts' => array(self::HAS_MANY, 'ClientContact', 'client_id'),
            'clientToMaterials' => array(self::HAS_MANY, 'ClientToMaterial', 'client_id'),
            'projectTemplates' => array(self::HAS_MANY, 'ProjectTemplate', 'client_id'),
        );
    }

	public function getAdminColumns()
	{
		$columns[] = $this->imageColumn();
        $columns[] = 'name';

		return $columns;
	}

}

?>