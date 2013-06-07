<?php

/**
 * This is the model class for table "tbl_default_value".
 *
 * The followings are the available columns in table 'tbl_default_value':
 * @property string $id
 * @property string $table
 * @property string $column
 * @property string $select
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 */
class DefaultValue extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Default';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('table, column, select', 'required'),
			array('table, column', 'length', 'max'=>64),
			array('select', 'validationSQLSelect'),
//			array('id, table, column, select', 'safe', 'on'=>'search'),
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
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'table' => 'Table',
			'column' => 'Attribute',
			'select' => 'Select',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$criteria->compare('t.table',$this->table);
		$criteria->compare('t.column',$this->column);
		$criteria->compare('t.select',$this->select);

		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.table',
			't.column',
			't.select',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'table';
		$columns[] = 'column';
		$columns[] = 'select';
		
		return $columns;
	}

	static function getDisplayAttr()
	{
		return array(
			'table',
			'column',
			);
	}

}