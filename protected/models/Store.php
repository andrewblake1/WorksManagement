<?php

/**
 * This is the model class for table "store".
 *
 * The followings are the available columns in table 'store':
 * @property integer $id
 * @property string $name
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Assembly[] $assemblies
 * @property AssemblyToStandardDrawing[] $assemblyToStandardDrawings
 * @property Material[] $materials
 * @property MaterialGroup[] $materialGroups
 * @property StandardDrawing[] $standardDrawings
 * @property Staff $staff
 */
class Store extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Standard';
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('name', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name', 'safe', 'on'=>'search'),
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
			'assemblies' => array(self::HAS_MANY, 'Assembly', 'store_id'),
			'assemblyToStandardDrawings' => array(self::HAS_MANY, 'AssemblyToStandardDrawing', 'store_id'),
			'materials' => array(self::HAS_MANY, 'Material', 'store_id'),
			'materialGroups' => array(self::HAS_MANY, 'MaterialGroup', 'store_id'),
			'standardDrawings' => array(self::HAS_MANY, 'StandardDrawing', 'store_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.name',$this->name,true);

		$criteria->select=array(
			't.id',
			't.name',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
		$columns[] = $this->linkThisColumn('name');
		
		return $columns;
	}

}

?>