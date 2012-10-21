<?php

/**
 * This is the model class for table "assembly_to_client".
 *
 * The followings are the available columns in table 'assembly_to_client':
 * @property string $id
 * @property integer $assembly_id
 * @property integer $client_id
 * @property string $alias
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Assembly $assembly
 * @property Client $client
 * @property Staff $staff
 */
class AssemblyToClient extends ActiveRecord
{
	public $searchAlias;
	public $searchAssembly;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Assembly';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'assembly_to_client';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('assembly_id, client_id, staff_id', 'required'),
			array('assembly_id, client_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('id, alias', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, client_id, searchAssembly, searchAlias, alias, deleted, staff_id', 'safe', 'on'=>'search'),
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
			'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Assembly',
			'assembly_id' => 'Assembly',
			'client_id' => 'Client',
			'alias' => 'Alias',
		);
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			"CONCAT_WS('$delimiter',
				assembly.description,
				assembly.alias
				) AS searchAssembly",
			't.alias',
			'assembly_id',
			't.client_id',
		);

		$this->compositeCriteria($criteria,
			array(
			'assembly.description',
			'assembly.alias'
			),
			$this->searchAssembly
		);
		$criteria->compare('t.client_id',$this->client_id,true);
		$criteria->compare('t.alias',$this->alias);

		$criteria->with = array('assembly');

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchAssembly', 'Assembly', 'assembly_id');
 		$columns[] = 'alias';

		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchAssembly');
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'assembly->description',
			'assembly->alias',
		);
	}

}

?>