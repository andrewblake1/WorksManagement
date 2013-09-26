<?php

/**
 * This is the model class for table "tbl_standard".
 *
 * The followings are the available columns in table 'tbl_standard':
 * @property integer $id
 * @property string $name
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Assembly[] $assemblies
 * @property AssemblyGroup[] $assemblyGroups
 * @property Drawing[] $drawings
 * @property Material[] $materials
 * @property MaterialGroup[] $materialGroups
 * @property User $updatedBy
 */
class Standard extends ActiveRecord
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
            'assemblies' => array(self::HAS_MANY, 'Assembly', 'standard_id'),
            'assemblyGroups' => array(self::HAS_MANY, 'AssemblyGroup', 'standard_id'),
            'drawings' => array(self::HAS_MANY, 'Drawing', 'standard_id'),
            'materials' => array(self::HAS_MANY, 'Material', 'standard_id'),
            'materialGroups' => array(self::HAS_MANY, 'MaterialGroup', 'standard_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }

	public function getAdminColumns()
	{
		$columns[] = 'name';
		
		return $columns;
	}
	
}

?>