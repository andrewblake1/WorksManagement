<?php

/**
 * This is the model class for table "report".
 *
 * The followings are the available columns in table 'report':
 * @property string $id
 * @property string $description
 * @property string $template_html
 * @property string $context
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Staff $staff
 * @property AuthItem $context0
 * @property ReportToAuthItem[] $reportToAuthItems
 * @property SubReport[] $subReports
 */
class Report extends ActiveRecord
{
	public $sub_report_id;	// dummy place holder for drag and drop list widget in _form

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
			array('description, staff_id', 'required'),
			array('staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>255),
			array('context', 'length', 'max'=>64),
			array('template_html, sub_report_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, template_html, context, staff_id', 'safe', 'on'=>'search'),
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
			'context0' => array(self::BELONGS_TO, 'AuthItem', 'context'),
			'reportToAuthItems' => array(self::HAS_MANY, 'ReportToAuthItem', 'report_id'),
			'subReports' => array(self::HAS_MANY, 'SubReport', 'report_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Report',
			'description' => 'Description',
			'template_html' => 'Template Html',
			'context' => 'Context',
			'sub_report_id' => 'Sub report',
		);
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.description',
			't.template_html',
			't.context',
		);

		// where
		$criteria->compare('t.description', $this->description, true);
		$criteria->compare('t.template_html', $this->template_html, true);
		$criteria->compare('t.context', $this->context, true);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'description';
		$columns[] = 'context';
		
		return $columns;
	}

}