<?php

/**
 * This is the model class for table "assembly_to_material".
 *
 * The followings are the available columns in table 'assembly_to_material':
 * @property integer $id
 * @property integer $assembly_id
 * @property integer $material_id
 * @property integer $quantity
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Assembly $assembly
 * @property Material $material
 * @property Staff $staff
 */
class AssemblyToMaterial extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchMaterial;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Material';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'assembly_to_material';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('assembly_id, material_id, quantity, staff_id', 'required'),
			array('assembly_id, material_id, quantity, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, assembly_id, searchMaterial, quantity, deleted, staff_id', 'safe', 'on'=>'search'),
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
			'material' => array(self::BELONGS_TO, 'Material', 'material_id'),
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
			'material_id' => 'Material',
			'searchMaterial' => 'Material',
			'quantity' => 'Quantity',
		);
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		// client_id should always be set unless come directly from url so cover this to be safe anyway
		if(!isset($this->assembly_id))
		{
			throw new CHttpException(400, 'No assembly identified, you must get here from the assemblys page');
		}
		
//		$criteria->compare('t.id',$this->id);
		$criteria->compare('material.description',$this->searchMaterial,true);
		$criteria->compare('t.quantity',$this->quantity);
		
		$criteria->with = array('material');

		$criteria->select=array(
//			't.id',
			'material.description AS searchMaterial',
			't.quantity',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
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