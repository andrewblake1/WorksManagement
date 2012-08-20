<?php

/**
 * This is the model class for table "assembly".
 *
 * The followings are the available columns in table 'assembly':
 * @property integer $id
 * @property string $description
 * @property string $url
 * @property integer $material_id
 * @property integer $quantity
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Material $material
 * @property Staff $staff
 * @property TaskToAssembly[] $taskToAssemblies
 */
class Assembly extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchMaterial;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Assembly the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'assembly';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, material_id, quantity, staff_id', 'required'),
			array('material_id, quantity, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description, url', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, url, searchMaterial, quantity, searchStaff', 'safe', 'on'=>'search'),
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
			'material' => array(self::BELONGS_TO, 'Material', 'material_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'assembly_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Assembly',
			'url' => 'Url',
			'material_id' => 'Material',
			'searchMaterial' => 'Material',
			'quantity' => 'Quantity',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.url',$this->url,true);
		$criteria->compare('material.description',$this->searchMaterial,true);
		$criteria->compare('t.quantity',$this->quantity);
		
		$criteria->with = array('material');

		$criteria->select=array(
			't.id',
			't.description',
			't.url',
			'material.description AS searchMaterial',
			't.quantity',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'id';
		$columns[] = 'description';
        $columns[] = array(
			'name'=>'url',
			'value'=>'CHtml::link($data->url, $data->url)',
			'type'=>'raw',
		);
        $columns[] = array(
			'name'=>'searchMaterial',
			'value'=>'CHtml::link($data->searchMaterial,
				Yii::app()->createUrl("Material/update", array("id"=>$data->material_id))
			)',
			'type'=>'raw',
		);
 		$columns[] = 'quantity';
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchMaterial');
	}
}

?>