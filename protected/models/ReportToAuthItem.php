<?php

/**
 * This is the model class for table "tbl_report_to_auth_item".
 *
 * The followings are the available columns in table 'tbl_report_to_auth_item':
 * @property string $id
 * @property string $report_id
 * @property string $auth_item_name
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Report $report
 * @property AuthItem $authItemName
 * @property User $updatedBy
 */
class ReportToAuthItem extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Role';
	
	/** 
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'report' => array(self::BELONGS_TO, 'Report', 'report_id'),
            'authItemName' => array(self::BELONGS_TO, 'AuthItem', 'auth_item_name'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'report_id' => 'Report',
			'auth_item_name' => 'Role',
		));
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
			'auth_item_name',
		);

		// where
		$criteria->compare('t.auth_item_name',$this->auth_item_name,true);
		$criteria->compare('t.report_id',$this->report_id);
		
		return $criteria;
	}
	
	public function getAdminColumns()
	{
		$columns[] = $this->linkThisColumn('auth_item_name');
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		$displaAttr[]='auth_item_name';

		return $displaAttr;
	}

}