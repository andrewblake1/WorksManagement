 <?php

/**
 * This is the model class for table "tbl_duty_step_dependency".
 *
 * The followings are the available columns in table 'tbl_duty_step_dependency':
 * @property string $id
 * @property integer $parent_duty_step_id
 * @property integer $child_duty_step_id
 * @property string $duty_type_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property DutyData[] $dutyDatas
 * @property DutyData[] $dutyDatas1
 * @property DutyStep $parentDutyStep
 * @property DutyStep $childDutyStep
 * @property User $updatedBy
 * @property DutyType $dutyType
 */
class DutyStepDependency extends AdjacencyListActiveRecord
{

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Duty dependency';

	public $searchChildDutyStep;
	protected $defaultSort = array('childDutyStep.description');
	
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
		return array_merge(parent::rules(), array(
            array('id, parent_duty_step_id, child_duty_step_id, duty_type_id, updated_by', 'required'),
            array('parent_duty_step_id, child_duty_step_id, deleted, updated_by', 'numerical', 'integerOnly'=>true),
            array('id, duty_type_id', 'length', 'max'=>10),
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
            'dutyDatas' => array(self::HAS_MANY, 'DutyData', 'duty_step_id'),
            'dutyDatas1' => array(self::HAS_MANY, 'DutyData', 'duty_step_dependency_id'),
            'parentDutyStep' => array(self::BELONGS_TO, 'DutyStep', 'parent_duty_step_id'),
            'childDutyStep' => array(self::BELONGS_TO, 'DutyStep', 'child_duty_step_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'dutyType' => array(self::BELONGS_TO, 'DutyType', 'duty_type_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'parent_duty_step_id' => 'Integral to',
            'child_duty_step_id' => 'Depends on',
            'duty_type_id' => 'Duty type',
			'searchChildDutyStep' => 'Depends on',
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',
			't.parent_duty_step_id',
			't.child_duty_step_id',
			'childDutyStep.description AS searchChildDutyStep',
		);

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.parent_duty_step_id',$this->parent_duty_step_id);
		$criteria->compare('childDutyStep.description',$this->searchChildDutyStep,true);

		$criteria->with = array(
			'childDutyStep',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchChildDutyStep', 'DutyStep', 'child_duty_step_id');
		
		return $columns;
	}

	public static function getDisplayAttr()
	{
		return array(
			'parentDutyStep->description',
		);
	}
 
	/**
	 * Returns foreign key attribute name within this model that references another model.
	 * @param string $referencesModel the name name of the model that the foreign key references.
	 * @return string the foreign key attribute name within this model that references another model
	 */
	static function getParentForeignKey($referencesModel)
	{
		return parent::getParentForeignKey($referencesModel, array('DutyStep'=>'parent_duty_step_id'));
	}
	

}