<?php

/**
 * This is the model class for table "assembly_to_standard_drawing".
 *
 * The followings are the available columns in table 'assembly_to_standard_drawing':
 * @property integer $id
 * @property integer $assembly_id
 * @property integer $standard_drawing_id
 * @property integer $supplier_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Assembly $assembly
 * @property Supplier $supplier
 * @property StandardDrawing $standardDrawing
 * @property Staff $staff
 */
class AssemblyToStandardDrawing extends ActiveRecord
{
	public $searchStandardDrawing;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'assembly_to_standard_drawing';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('assembly_id, standard_drawing_id, supplier_id, staff_id', 'required'),
			array('assembly_id, standard_drawing_id, supplier_id, staff_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, assembly_id, searchStandardDrawing, supplier_id, staff_id', 'safe', 'on'=>'search'),
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
			'assembly' => array(self::BELONGS_TO, 'Assembly', 'assembly_id'),
			'supplier' => array(self::BELONGS_TO, 'Supplier', 'supplier_id'),
			'standardDrawing' => array(self::BELONGS_TO, 'StandardDrawing', 'standard_drawing_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'assembly_id' => 'Assembly',
			'standard_drawing_id' => 'Standard Drawing',
			'searchStandardDrawing' => 'Standard Drawing',
			'supplier_id' => 'Supplier',
			'staff_id' => 'Staff',
		);
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			'standardDrawing.description AS searchStandardDrawing',
		);

		// where
		$criteria->compare('standardDrawing.description', $this->searchStandardDrawing, true);
		$criteria->compare('t.assembly_id', $this->assembly_id);

		$criteria->with = array('standardDrawing');

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'searchStandardDrawing';
		
		return $columns;
	}
}