<?php

/**
 * This is the model class for table "tbl_assembly_to_client".
 *
 * The followings are the available columns in table 'tbl_assembly_to_client':
 * @property integer $id
 * @property integer $assembly_id
 * @property integer $client_id
 * @property string $alias
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Assembly $assembly
 * @property Client $client
 * @property User $updatedBy
 */
class AssemblyToClient extends ActiveRecord
{
	public $searchAssemblyDescription;
	public $searchAssemblyUnit;
	public $searchAssemblyAlias;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Assembly';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('assembly_id, client_id', 'required'),
			array('assembly_id, client_id', 'numerical', 'integerOnly'=>true),
			array('alias', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, client_id, searchAssemblyDescription, searchAssemblyUnit, searchAssemblyAlias, alias', 'safe', 'on'=>'search'),
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
			'searchAssemblyDescription' => 'Assembly',
			'searchAssemblyUnit' => 'Unit',
			'searchAssemblyAlias' => 'Alias',
			'client_id' => 'Client',
		));
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
			'assembly.description AS searchAssemblyDescription',
			'assembly.unit AS searchAssemblyUnit',
			'assembly.alias AS searchAssemblyAlias',
			't.alias',
			'assembly_id',
			't.client_id',
		);

		$criteria->compare('assembly.description',$this->searchAssemblyDescription,true);
		$criteria->compare('assembly.unit',$this->searchAssemblyUnit,true);
		$criteria->compare('assembly.alias',$this->searchAssemblyAlias,true);
		$criteria->compare('t.client_id',$this->client_id,true);
		$criteria->compare('t.alias',$this->alias);

		$criteria->with = array('assembly');

		return $criteria;
	}

	public function getAdminColumns()
	{
 		$columns[] = 'searchAssemblyDescription';
 		$columns[] = 'searchAssemblyUnit';
 		$columns[] = 'searchAssemblyAlias';
 		$columns[] = 'alias';

		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array(
			'assembly->description',
			'assembly->unit',
			'assembly->alias',
			'alias',
		);
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'assembly->description',
			'assembly->unit',
			'assembly->alias',
			'alias',
		);
	}

}

?>