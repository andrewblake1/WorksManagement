<?php

/**
 * This is the model class for table "AuthItem".
 *
 * The followings are the available columns in table 'AuthItem':
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $bizrule
 * @property string $data
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property DutyStep[] $dutySteps
 * @property ProjectTemplateToAuthItem[] $projectTemplateToAuthItems
 * @property ProjectToAuthItem[] $projectToAuthItems
 * @property Report[] $reports
 * @property ReportToAuthItem[] $reportToAuthItems
 */
class AuthItem extends ActiveRecord
{
	/**
	 *  Auth item type constants
	 */
	const typeRole = 2;
	const typeRight = 1;
	const typeTask = 1;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Role';
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return get_class($this);
	}
	
	public function scopes()
    {
		return array(
			'roles'=>array('condition'=>'t.type=' . self::typeRole),
			'rights'=>array('condition'=>'t.type<=' . self::typeRight),
			'tasks'=>array('condition'=>'t.type=' . self::typeTask),
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
            'dutySteps' => array(self::HAS_MANY, 'DutyStep', 'auth_item_name'),
            'projectTemplateToAuthItems' => array(self::HAS_MANY, 'ProjectTemplateToAuthItem', 'auth_item_name'),
            'projectToAuthItems' => array(self::HAS_MANY, 'ProjectToAuthItem', 'auth_item_name'),
            'reports' => array(self::HAS_MANY, 'Report', 'context'),
            'reportToAuthItems' => array(self::HAS_MANY, 'ReportToAuthItem', 'auth_item_name'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'name' => 'Role',
		));
	}

	public function getAdminColumns()
	{
		$columns[] = 'name';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'name',
		);
	}

	/*
	 * can't set default value in database as TEXT data type but is required
	 */
	public function init()
	{
		// can't set default value in database as TEXT data type for AuthItem
		$this->data = 'N;';
		$this->type = '2';
		
		parent::init();
	}

}

?>