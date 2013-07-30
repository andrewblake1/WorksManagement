<?php

/**
 * This is the model class for table "v_drawing_to_assembly".
 *
 * The followings are the available columns in table 'v_drawing_to_assembly':
 * @property integer $id
 * @property integer $drawing_id
 * @property string $description
 */
class DrawingToAssembly extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'v_drawing_to_assembly';
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.drawing_id',
			't.description',
		);

		// where
		$criteria->compare('t.drawing_id', $this->drawing_id);
		$criteria->compare('t.description', $this->description, true);
		
		return $criteria;
	}
	
	public function getAdminColumns()
	{
		$columns[] = self::linkColumn('description', 'Assembly', 'id');
		
		return $columns;
	}

}