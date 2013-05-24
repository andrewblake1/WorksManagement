<?php

/**
 * This is the model class for table "v_drawing_to_assembly".
 *
 * The followings are the available columns in table 'v_drawing_to_assembly':
 * @property integer $id
 * @property integer $drawing_id
 * @property string $description
 */
class DrawingToAssembly extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Assembly';
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'v_drawing_to_assembly';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description', 'required'),
			array('id, drawing_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, drawing_id, description', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'drawing_id' => 'Drawing',
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
			't.drawing_id',
			't.description',
		);

		// where
		$criteria->compare('t.drawing_id', $this->drawing_id);
		$criteria->compare('t.description', $this->description, true);
		
		return $criteria;
	}
	
	public function getAdminColumns()
	{
		$columns[] = self::linkColumn('description', 'Assembly', 'id');
		
		return $columns;
	}

}