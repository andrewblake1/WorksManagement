<?php

/**
 * This is the model class for table "tbl_report".
 *
 * The followings are the available columns in table 'tbl_report':
 * @property string $id
 * @property string $description
 * @property string $template_html
 * @property string $context
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property AuthItem $context0
 * @property ReportToAuthItem[] $reportToAuthItems
 * @property SubReport[] $subReports
 */
class Report extends ActiveRecord
{
	public $subReport_id;	// dummy place holder for drag and drop list widget in _form

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('description', 'required'),
			array('description', 'length', 'max'=>255),
			array('context', 'length', 'max'=>64),
			array('template_html, subReport_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, description, template_html, context', 'safe', 'on'=>'search'),
		));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
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
		return parent::attributeLabels(array(
			'template_html' => 'Template Html',
			'context' => 'Context',
			'subReport_id' => 'Sub report',
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
		$columns[] = $this->linkThisColumn('description');
		$columns[] = 'context';
		
		return $columns;
	}

}