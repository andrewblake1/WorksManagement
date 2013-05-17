<?php

/**
 * This is the model class for table "tbl_client".
 *
 * The followings are the available columns in table 'tbl_client':
 * @property integer $id
 * @property string $name
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property AssemblyToClient[] $assemblyToClients
 * @property User $updatedBy
 * @property ClientContact[] $clientContacts
 * @property MaterialToClient[] $materialToClients
 * @property ProjectTemplate[] $projectTemplates
 */
class Client extends ActiveRecord
{

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('name', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'assemblyToClients' => array(self::HAS_MANY, 'AssemblyToClient', 'client_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'clientContacts' => array(self::HAS_MANY, 'ClientContact', 'client_id'),
            'materialToClients' => array(self::HAS_MANY, 'MaterialToClient', 'client_id'),
            'projectTemplates' => array(self::HAS_MANY, 'ProjectTemplate', 'client_id'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.name',$this->name,true);

		$criteria->select=array(
			't.id',
			't.name',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
        $columns[] = $this->linkThisColumn('name');

		return $columns;
	}

}

?>