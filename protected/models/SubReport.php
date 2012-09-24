<?php

/**
 * This is the model class for table "sub_report".
 *
 * The followings are the available columns in table 'sub_report':
 * @property string $id
 * @property string $description
 * @property string $select
 * @property string $report_id
 * @property string $format
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Report $report
 * @property Staff $staff
 */
class SubReport extends ActiveRecord
{
	/**
	 * Data types. These are the emum values set by the DataType custom type within 
	 * the database
	 */
	const subReportFormatPaged = 'Paged';
	const subReportFormatNotPaged = 'Not paged';
	const subReportFormatNoFormat= 'No format';

	/**
	 * @return array duty level value => duty level display name
	 */
	public static function getFormats()
	{
		return array(
			self::subReportFormatPaged=>self::subReportFormatPaged,
			self::subReportFormatNotPaged=>self::subReportFormatNotPaged,
			self::subReportFormatNoFormat=>self::subReportFormatNoFormat,
		);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sub_report';
	}

	public function scopeSubReportReportId($report_id)
	{
		$criteria=new CDbCriteria;
		$criteria->compare('report_id',$report_id);

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, select, report_id, format, staff_id', 'required'),
			array('staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>255),
			array('report_id', 'length', 'max'=>10),
			array('format', 'length', 'max'=>9),
			array('select', 'validationReport'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, select, report_id, format, staff_id', 'safe', 'on'=>'search'),
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
			'report' => array(self::BELONGS_TO, 'Report', 'report_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'description' => 'Description',
			'select' => 'Select',
			'report_id' => 'Report',
			'format' => 'Format',
			'staff_id' => 'Staff',
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
			't.format',
			't.select',
		);

		// where
		$criteria->compare('t.description', $this->description, true);
		$criteria->compare('t.format', $this->format, true);
		$criteria->compare('t.select', $this->select, true);
		$criteria->compare('t.report_id', $this->report_id);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'description';
		$columns[] = 'format';
		$columns[] = 'select';
		
		return $columns;
	}

	public function validationReport($attribute, $params)
	{
//TODO: open another database connection as this user whenever entering user entered sql.
//otherwise they can run their sql with full application access rights

		// first fake valid substitutions
		$sql = str_ireplace(':pk', '1', $this->$attribute);
		$sql = str_ireplace(':userid', '1', $sql);

		// test if sql is valid
		try
		{
			// test validity of sql
			Yii::app()->db->createCommand($sql)->queryAll();
		}
		catch(Exception $e)
		{
			$errorMessage = 'There is an error in the setup - please contact the system administrator, the database says:<br> '.$e->getMessage();
			$this->addError($attribute, $errorMessage);
		}
	}

}