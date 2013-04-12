<?php

/**
 * This is the model class for table "assembly_to_standard_drawing".
 *
 * The followings are the available columns in table 'assembly_to_standard_drawing':
 * @property integer $id
 * @property integer $assembly_id
 * @property integer $standard_drawing_id
 * @property integer $store_id
 * @property integer $deleted
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

	protected $defaultSort = array('standardDrawing.description');
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('assembly_id, standard_drawing_id, store_id', 'required'),
			array('assembly_id, standard_drawing_id, store_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, assembly_id, searchStandardDrawing, store_id', 'safe', 'on'=>'search'),
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
		return parent::attributeLabels(array(
			'assembly_id' => 'Assembly',
			'standard_drawing_id' => 'Standard drawing',
			'searchStandardDrawing' => 'Standard drawing',
			'store_id' => 'Store',
		));
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
			't.standard_drawing_id',
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
        $columns[] = static::linkColumn('searchStandardDrawing', 'StandardDrawing', 'standard_drawing_id');
//		$columns[] = $this->linkThisColumn('searchStandardDrawing');
		
		return $columns;
	}
	
	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchStandardDrawing');
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