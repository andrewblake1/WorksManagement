<?php

/**
 * This is the model class for table "tbl_custom_field_duty_step_category".
 *
 * The followings are the available columns in table 'tbl_custom_field_duty_step_category':
 * @property integer $id
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 * @property string $name
 * @property integer $duty_step_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property DutyStep $dutyStep
 * @property DutyStepToCustomField[] $dutyStepToCustomFields
 * @property DutyStepToCustomField[] $dutyStepToCustomFields1
 */
class CustomFieldDutyStepCategory extends CategoryActiveRecord {
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Field set';
	
	// place holders used by getValidGetParam in order to retain correct breadcrumb tail and these needed
	// due to the multi levels available within actions - a dirty hack. Otherwise calculation of breadcrumb trail
	// will loose the fact that have come thru action - may
//	public $project_template_id;
//	public $client_id;
	public $action_id;

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'dutyStep' => array(self::BELONGS_TO, 'DutyStep', 'duty_step_id'),
            'dutyStepToCustomFields' => array(self::HAS_MANY, 'DutyStepToCustomField', 'duty_step_id'),
            'dutyStepToCustomFields1' => array(self::HAS_MANY, 'DutyStepToCustomField', 'custom_field_duty_step_category_id'),
        );
    }

}