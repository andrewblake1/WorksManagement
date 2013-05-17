<?php

/**
 * This is the model class for table "tbl_supplier_to_supplier_contact".
 *
 * The followings are the available columns in table 'tbl_supplier_to_supplier_contact':
 * @property integer $id
 * @property integer $supplier_id
 * @property integer $supplier_contact_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Supplier $supplier
 * @property SupplierContact $supplierContact
 * @property User $updatedBy
 */
class SupplierToSupplierContact extends ActiveRecord
{
	public $searchFirstName;
	public $searchLastName;
	public $searchEmail;
	public $searchPhoneMobile;
	public $searchPhoneHome;
	public $searchPhoneWork;
	public $searchPhoneFax;

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
			array('id, searchFirstName, searchLastName, searchEmail, searchPhoneMobile, searchPhoneHome, searchPhoneWork, searchPhoneFax, supplier_id, supplier_contact_id', 'safe', 'on'=>'search'),
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
            'supplier' => array(self::BELONGS_TO, 'Supplier', 'supplier_id'),
            'supplierContact' => array(self::BELONGS_TO, 'SupplierContact', 'supplier_contact_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
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
			'searchFirstName' => 'First name',
			'searchLastName' => 'Last name',
			'searchEmail' => 'Email',
			'searchPhoneMobile' => 'Phone mobile',
			'searchPhoneHome' => 'Phone home',
			'searchPhoneWork' => 'Phone work',
			'searchPhoneFax' => 'Phone fax',
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
			'supplierContact.first_name as searchFirstName',
			'supplierContact.last_name as searchLastName',
			'supplierContact.email as searchEmail',
			'supplierContact.phone_mobile as searchPhoneMobile',
			'supplierContact.phone_home as searchPhoneHome',
			'supplierContact.phone_work as searchPhoneWork',
			'supplierContact.phone_fax as searchPhoneFax',
		);

		$criteria->compare('supplier_id',$this->supplier_id);
		$criteria->compare('supplierContact.first_name',$this->searchFirstName,true);
		$criteria->compare('supplierContact.last_name',$this->searchLastName,true);
		$criteria->compare('supplierContact.email',$this->searchEmail,true);
		$criteria->compare('supplierContact.phone_mobile',$this->searchPhoneMobile,true);
		$criteria->compare('supplierContact.phone_home',$this->searchPhoneHome,true);
		$criteria->compare('supplierContact.phone_work',$this->searchPhoneWork,true);
		$criteria->compare('supplierContact.phone_fax',$this->searchPhoneFax,true);

		// with
		$criteria->with = array(
			'supplierContact',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[]=$this->linkThisColumn('searchFirstName');
		$columns[]=$this->linkThisColumn('searchLastName');
        $columns[] = array(
			'name'=>'searchPhoneMobile',
			'value'=>'CHtml::link($data->searchPhoneMobile, "tel:".$data->searchPhoneMobile)',
			'type'=>'raw',
		);
        $columns[] = array(
			'name'=>'searchPhoneHome',
			'value'=>'CHtml::link($data->searchPhoneHome, "tel:".$data->searchPhoneHome)',
			'type'=>'raw',
		);
        $columns[] = array(
			'name'=>'searchPhoneWork',
			'value'=>'CHtml::link($data->searchPhoneWork, "tel:".$data->searchPhoneWork)',
			'type'=>'raw',
		);
		$columns[]='searchPhoneFax';
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
			'searchFirstName',
			'searchLastName',
			'searchEmail',
			'searchPhoneMobile',
			'searchPhoneHome',
			'searchPhoneWork',
			'searchPhoneFax',
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