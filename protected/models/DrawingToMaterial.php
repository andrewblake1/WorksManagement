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

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// where
		$criteria->compare('t.drawing_id', $this->drawing_id);
		$criteria->compare('t.description', $this->description, true);
		$criteria->compare('t.alias', $this->alias, true);
		
		return $criteria;
	}
	
	public function getAdminColumns()
	{
		$columns[] = self::linkColumn('description', 'Material', 'id');
		$columns[] = 'alias';
		
		return $columns;
	}

}