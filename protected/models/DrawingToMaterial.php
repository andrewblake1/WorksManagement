<?php

/**
 * This is the model class for table "v_drawing_to_material".
 *
 * The followings are the available columns in table 'v_drawing_to_material':
 * @property integer $id
 * @property integer $drawing_id
 * @property string $description
 */
class DrawingToMaterial extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'v_drawing_to_material';
	}

	static function primaryKeyName() {
		return 'id';
	}

	public function getAdminColumns()
	{
		$columns[] = self::linkColumn('description', 'Material', 'id');
		$columns[] = 'alias';
		
		return $columns;
	}

}