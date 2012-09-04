<?php

/**
 * This is the model class for table "report".
 *
 * The followings are the available columns in table 'report':
 * @property string $id
 * @property string $description
 * @property string $select
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Staff $staff
 * @property ReportToAuthItem[] $reportToAuthItems
 */
class Report extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'report';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, select, staff_id', 'required'),
			array('staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>255),
			array('id, description, select, staff_id', 'safe', 'on'=>'search'),
			array('select', 'validationReport'),
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
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'reportToAuthItems' => array(self::HAS_MANY, 'ReportToAuthItem', 'report_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Report',
			'select' => 'Select',
		);
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		// select
		$criteria->select=array(
			't.description',
			't.select',
		);

		// where
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.select', $this->select, true);
		
		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'description';
		$columns[] = 'select';
		
		return $columns;
	}

	public function validationReport($attribute, $params)
	{
//TODO: open another database connection as this user whenever entering user entered sql.
//otherwise they can run their sql with full application access rights
		// test if sql is valid
		try
		{
			// test validity of sql
			Yii::app()->db->createCommand($this->$attribute)->queryAll();
		}
		catch(Exception $e)
		{
			$errorMessage = 'There is an error in the setup - please contact the system administrator, the database says:<br> '.$e->getMessage();
		}

		// if validation failed
		if($errorMessage)
		{
			$this->addError($attribute, $errorMessage);
		}
	}
}