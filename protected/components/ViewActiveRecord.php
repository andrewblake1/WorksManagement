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
			'user' => array(self::BELONGS_TO, 'User', 'updated_by'),
		);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		$modelName = str_replace('View', '', get_class($this));
		return 'v_' . Yii::app()->functions->uncamelize($modelName);
	}
		
}

?>