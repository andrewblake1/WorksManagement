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
			't.table',
			't.column',
		);
	}

}