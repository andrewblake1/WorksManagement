<?php

/**
 * This is the model class for table "tbl_sub_report".
 *
 * The followings are the available columns in table 'tbl_sub_report':
 * @property string $id
 * @property string $description
 * @property string $select
 * @property string $report_id
 * @property string $format
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Report $report
 * @property User $updatedBy
 */
class SubReport extends ActiveRecord
{
	/**
	 * Data types. These are the emum values set by the format Custom field within 
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
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'report' => array(self::BELONGS_TO, 'Report', 'report_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }

	public function getAdminColumns()
	{
		$columns[] = 'description';
		$columns[] = 'format';
		$columns[] = 'select';
		
		return $columns;
	}

}