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
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description', 'required'),
			array('description', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description', 'safe', 'on'=>'search'),
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
            'assemblyToMaterials' => array(self::HAS_MANY, 'AssemblyToMaterial', 'stage_id'),
            'assemblyToMaterialGroups' => array(self::HAS_MANY, 'AssemblyToMaterialGroup', 'stage_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.description',$this->description,true);

		$criteria->select=array(
			't.id',
			't.description',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
		$columns[] = $this->linkThisColumn('description');
		
		return $columns;
	}

}

?>