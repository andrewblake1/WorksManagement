<?php

/**
 * This is the model class for table "tbl_contact".
 *
 * The followings are the available columns in table 'tbl_contact':
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $address_line_1
 * @property string $address_line_2
 * @property string $post_code
 * @property string $town_city
 * @property string $state_province
 * @property string $country
 * @property string $phone_mobile
 * @property string $phone_home
 * @property string $phone_work
 * @property string $phone_fax
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property ClientContact[] $clientContacts
 * @property User $updatedBy
 * @property SupplierContact[] $supplierContacts
 * @property User[] $users
 */
class Contact extends ActiveRecord
{
	use FileActiveRecordTrait;

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'clientContacts' => array(self::HAS_MANY, 'ClientContact', 'contact_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'supplierContacts' => array(self::HAS_MANY, 'SupplierContact', 'contact_id'),
            'users' => array(self::HAS_MANY, 'User', 'contact_id'),
        );
    }
	
	public function getAdminColumns()
	{
		$columns[] = $this->imageColumn();
		$columns[] = 'id';
		$columns[] = 'first_name';
		$columns[] = 'last_name';
        $columns[] = array(
			'name'=>'phone_mobile',
			'value'=>'CHtml::link($data->phone_mobile, "tel:".$data->phone_mobile)',
			'type'=>'raw',
		);
        $columns[] = array(
			'name'=>'email',
			'value'=>'$data->email',
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
			'first_name',
			'last_name',
			'email',
		);
	}

}