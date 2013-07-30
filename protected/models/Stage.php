<?php

/**
 * This is the model class for table "tbl_stage".
 *
 * The followings are the available columns in table 'tbl_stage':
 * @property integer $id
 * @property string $description
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property AssemblyToMaterial[] $assemblyToMaterials
 * @property AssemblyToMaterialGroup[] $assemblyToMaterialGroups
 * @property User $updatedBy
 */
class Stage extends ActiveRecord
{
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'assemblyToMaterials' => array(self::HAS_MANY, 'AssemblyToMaterial', 'stage_id'),
            'assemblyToMaterialGroups' => array(self::HAS_MANY, 'AssemblyToMaterialGroup', 'stage_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }

	public function getAdminColumns()
	{
		$columns[] = 'description';
		
		return $columns;
	}

}

?>