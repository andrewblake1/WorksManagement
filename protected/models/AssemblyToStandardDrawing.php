<?php

/**
 * This is the model class for table "assembly_to_standard_drawing".
 *
 * The followings are the available columns in table 'assembly_to_standard_drawing':
 * @property integer $id
 * @property integer $assembly_id
 * @property integer $standard_drawing_id
 * @property integer $store_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Assembly $assembly
 * @property Store $store
 * @property StandardDrawing $standardDrawing
 * @property Staff $staff
 */
class AssemblyToStandardDrawing extends ActiveRecord
{
	public $searchStandardDrawing;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Standard drawing';

	
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
			array('assembly_id, standard_drawing_id, store_id, staff_id', 'required'),
			array('assembly_id, standard_drawing_id, store_id, staff_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, assembly_id, searchStandardDrawing, store_id, staff_id', 'safe', 'on'=>'search'),
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
			'store' => array(self::BELONGS_TO, 'Store', 'store_id'),
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
			'store_id' => 'Store',
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
			't.id',	// needed for delete and update buttons
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
	
	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'assembly->description'
		);
	}
	
	public function beforeValidate()
	{
		$assembly = Assembly::model()->findByPk($this->assembly_id);
		$this->store_id = $assembly->store_id;
		
		return parent::beforeValidate();
	}

}