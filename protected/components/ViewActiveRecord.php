<?php

abstract class ViewActiveRecord extends ActiveRecord
{
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

}

?>