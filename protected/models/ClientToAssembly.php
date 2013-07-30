<?php

/**
 * This is the model class for table "tbl_client_to_assembly".
 *
 * The followings are the available columns in table 'tbl_client_to_assembly':
 * @property integer $id
 * @property integer $assembly_id
 * @property integer $client_id
 * @property string $alias
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Assembly $assembly
 * @property Client $client
 * @property User $updatedBy
 */
class ClientToAssembly extends ActiveRecord
{
	public $searchAssembly;
	public $searchAlias;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Assembly';

	/**
	 * @return array relational rules.
	 */
	public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'assembly' => array(self::BELONGS_TO, 'Assembly', 'assembly_id'),
            'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchAssembly', $this->searchAssembly, 'assembly.description', true);
		$criteria->compareAs('searchAlias', $this->searchAlias, 'assembly.alias', true);

		$criteria->with = array('assembly');

		return $criteria;
	}

	public function getAdminColumns()
	{
 		$columns[] = 'searchAssembly';
 		$columns[] = 'searchAlias';
 		$columns[] = 'alias';

		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchAssembly',
			'searchAlias',
			't.alias',
		);
	}

}

?>