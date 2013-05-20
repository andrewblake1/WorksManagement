<?php

/**
 * This is the model class for table "tbl_assembly_to_drawing".
 *
 * The followings are the available columns in table 'tbl_assembly_to_drawing':
 * @property integer $id
 * @property integer $assembly_id
 * @property integer $drawing_id
 * @property integer $standard_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Assembly $assembly
 * @property Standard $standard
 * @property Drawing $drawing
 * @property User $updatedBy
 */
class AssemblyToDrawing extends ActiveRecord
{
	public $searchDrawing;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Drawing';

	protected $defaultSort = array('drawing.description');
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('assembly_id, drawing_id, standard_id', 'required'),
			array('assembly_id, drawing_id, standard_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, assembly_id, searchDrawing, standard_id', 'safe', 'on'=>'search'),
		));
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
            'standard' => array(self::BELONGS_TO, 'Standard', 'standard_id'),
            'drawing' => array(self::BELONGS_TO, 'Drawing', 'drawing_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'assembly_id' => 'Assembly',
			'drawing_id' => 'Standard drawing',
			'searchDrawing' => 'Standard drawing',
			'standard_id' => 'Standard',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.drawing_id',
			"CONCAT_WS('$delimiter',
				drawing.description,
				drawing.alias
				) AS searchDrawing",
		);

		// where
		$criteria->compare('drawing.description', $this->searchDrawing, true);
		$this->compositeCriteria($criteria,
			array(
				'drawing.description',
				'drawing.alias'
			),
			$this->searchDrawing
		);
		$criteria->compare('t.assembly_id', $this->assembly_id);

		$criteria->with = array('drawing');

		return $criteria;
	}

	public function getAdminColumns()
	{
		$extraParams = array();
		if(isset($_GET['task_to_assembly_id']))
		{
			$extraParams['task_to_assembly_id'] = $_GET['task_to_assembly_id'];
			$extraParams['parent_id'] = $_GET['parent_id'];
		}
        $columns[] = static::linkColumn('searchDrawing', 'Drawing', 'drawing_id', $extraParams);
//		$columns[] = $this->linkThisColumn('searchDrawing');
		
		return $columns;
	}
	
	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchDrawing');
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'drawing->description',
			'drawing->alias',
		);
	}
	
	public function beforeValidate()
	{
		$assembly = Assembly::model()->findByPk($this->assembly_id);
		$this->standard_id = $assembly->standard_id;
		
		return parent::beforeValidate();
	}

}