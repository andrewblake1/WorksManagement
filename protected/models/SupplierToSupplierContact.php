<?php

/**
 * This is the model class for table "supplier_to_supplier_contact".
 *
 * The followings are the available columns in table 'supplier_to_supplier_contact':
 * @property integer $id
 * @property integer $supplier_id
 * @property integer $supplier_contact_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property SupplierContact $supplierContact
 * @property Supplier $supplier
 * @property Staff $staff
 * @property Supplier $supplier
 */
class SupplierToSupplierContact extends ActiveRecord
{
	public $searchFirst_name;
	public $searchLast_name;
	public $searchEmail;
	public $searchPhone_mobile;
	public $searchPhone_home;
	public $searchPhone_work;
	public $searchPhone_fax;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Contact';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('supplier_id, supplier_contact_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchFirst_name, searchLast_name, searchEmail, searchPhone_mobile, searchPhone_home, searchPhone_work, searchPhone_fax, supplier_id, supplier_contact_id', 'safe', 'on'=>'search'),
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
			'supplierContact' => array(self::BELONGS_TO, 'SupplierContact', 'supplier_contact_id'),
			'supplier' => array(self::BELONGS_TO, 'Supplier', 'supplier_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'supplier_id' => 'Supplier',
			'supplier_contact_id' => 'Contact',
			'searchFirst_name' => 'First name',
			'searchLast_name' => 'Last name',
			'searchEmail' => 'Email',
			'searchPhone_mobile' => 'Phone mobile',
			'searchPhone_home' => 'Phone home',
			'searchPhone_work' => 'Phone work',
			'searchPhone_fax' => 'Phone fax',
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
			'supplier_contact_id',
			'supplierContact.first_name as searchFirst_name',
			'supplierContact.last_name as searchLast_name',
			'supplierContact.email as searchEmail',
			'supplierContact.phone_mobile as searchPhone_mobile',
			'supplierContact.phone_home as searchPhone_home',
			'supplierContact.phone_work as searchPhone_work',
			'supplierContact.phone_fax as searchPhone_fax',
		);

		$criteria->compare('supplier_id',$this->supplier_id);
		$criteria->compare('supplierContact.first_name',$this->searchFirst_name,true);
		$criteria->compare('supplierContact.last_name',$this->searchLast_name,true);
		$criteria->compare('supplierContact.email',$this->searchEmail,true);
		$criteria->compare('supplierContact.phone_mobile',$this->searchPhone_mobile,true);
		$criteria->compare('supplierContact.phone_home',$this->searchPhone_home,true);
		$criteria->compare('supplierContact.phone_work',$this->searchPhone_work,true);
		$criteria->compare('supplierContact.phone_fax',$this->searchPhone_fax,true);

		// join
		$criteria->with = array(
			'supplierContact',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[]=$this->linkThisColumn('searchFirst_name');
		$columns[]=$this->linkThisColumn('searchLast_name');
        $columns[] = array(
			'name'=>'searchPhone_mobile',
			'value'=>'CHtml::link($data->searchPhone_mobile, "tel:".$data->searchPhone_mobile)',
			'type'=>'raw',
		);
        $columns[] = array(
			'name'=>'searchPhone_home',
			'value'=>'CHtml::link($data->searchPhone_home, "tel:".$data->searchPhone_home)',
			'type'=>'raw',
		);
        $columns[] = array(
			'name'=>'searchPhone_work',
			'value'=>'CHtml::link($data->searchPhone_work, "tel:".$data->searchPhone_work)',
			'type'=>'raw',
		);
		$columns[]='searchPhone_fax';
        $columns[] = array(
			'name'=>'searchEmail',
			'value'=>'$data->searchEmail',
			'type'=>'email',
		);
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'supplierContact->first_name',
			'supplierContact->last_name',
			'supplierContact->email',
		);
	}

/*	public function beforeSave() {
		if(!empty($this->supplier_contact_id))
		{
			$supplierContact = SupplierContact::model()->findByPk($this->supplier_contact_id);
			$this->supplier_id = $supplierContact->supplier_id;
		}
		return parent::beforeSave();
	}*/
	
	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array(
			'searchFirst_name',
			'searchLast_name',
			'searchEmail',
			'searchPhone_mobile',
			'searchPhone_home',
			'searchPhone_work',
			'searchPhone_fax',
		);
	}

	/*
	 * overidden as mulitple models
	 */
	public function updateSave(&$models=array())
	{
		$supplierContact = $this->supplierContact;
		$supplierContact->attributes = $_POST['SupplierContact'];
		if($saved = $supplierContact->updateSave($models))
		{
			$saved &= parent::updateSave($models);
		}

		return $saved;
	}

	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array())
	{
	
		$supplierContact = new SupplierContact;
		$supplierContact->attributes = $_POST['SupplierContact'];
		if($saved = $supplierContact->createSave($models))
		{
			$this->supplier_contact_id = $supplierContact->id;
			$saved &= parent::createSave($models);
		}

		return $saved;
	}
}

?>