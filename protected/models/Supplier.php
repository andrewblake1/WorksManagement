<?php

/**
 * This is the model class for table "tbl_supplier".
 *
 * The followings are the available columns in table 'tbl_supplier':
 * @property integer $id
 * @property string $name
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property ClientToMaterial[] $clientToMaterials
 * @property HumanResourceToSupplier[] $humanResourceToSuppliers
 * @property User $updatedBy
 * @property SupplierContact[] $supplierContacts
 */
class Supplier extends ActiveRecord
{
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'clientToMaterials' => array(self::HAS_MANY, 'ClientToMaterial', 'supplier_id'),
            'resourceToSuppliers' => array(self::HAS_MANY, 'HumanResourceToSupplier', 'supplier_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'supplierContacts' => array(self::HAS_MANY, 'SupplierContact', 'supplier_id'),
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