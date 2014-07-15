<?php

/**
 * This is the model class for table "tbl_supplier_contact".
 *
 * The followings are the available columns in table 'tbl_supplier_contact':
 * @property integer $id
 * @property integer $supplier_id
 * @property integer $contact_id
 * @property string $role
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property Contact $contact
 * @property Supplier $supplier
 */
class SupplierContact extends ContactActiveRecord
{
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules($ignores = array())
	{
		return array_merge(parent::rules(array('contact_id')), array(
			array('supplier_id, first_name, last_name, email', 'required'),
			array('supplier_id', 'numerical', 'integerOnly'=>true),
			array('first_name, last_name, role, town_city, state_province, country, phone_mobile, phone_home, phone_work, phone_fax', 'length', 'max'=>64),
			array('email, address_line_1, address_line_2', 'length', 'max'=>255),
			array('post_code', 'length', 'max'=>16),
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
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
            'supplier' => array(self::BELONGS_TO, 'Supplier', 'supplier_id'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('first_name', $this->first_name, 'contact.first_name', true);
		$criteria->compareAs('last_name', $this->last_name, 'contact.last_name', true);
		$criteria->compareAs('email', $this->email, 'contact.email', true);
		$criteria->compareAs('phone_mobile', $this->phone_mobile, 'contact.phone_mobile', true);
		$criteria->compareAs('phone_home', $this->phone_home, 'contact.phone_home', true);
		$criteria->compareAs('phone_work', $this->phone_work, 'contact.phone_work', true);
		$criteria->compareAs('phone_fax', $this->phone_fax, 'contact.phone_fax', true);

		$criteria->with=array(
			'contact',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[]='first_name';
		$columns[]='last_name';
		$columns[]='role';
        $columns[] = array(
			'name'=>'phone_mobile',
			'value'=>'CHtml::link($data->phone_mobile, "tel:".$data->phone_mobile)',
			'type'=>'raw',
		);
        $columns[] = array(
			'name'=>'phone_home',
			'value'=>'CHtml::link($data->phone_home, "tel:".$data->phone_home)',
			'type'=>'raw',
		);
        $columns[] = array(
			'name'=>'phone_work',
			'value'=>'CHtml::link($data->phone_work, "tel:".$data->phone_work)',
			'type'=>'raw',
		);
		$columns[]='phone_fax';
        $columns[] = array(
			'name'=>'email',
			'value'=>'$data->email',
			'type'=>'email',
		);
		
		return $columns;
	}

}

?>