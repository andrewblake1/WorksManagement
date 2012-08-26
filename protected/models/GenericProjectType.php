<?php

/**
 * This is the model class for table "generic_project_type".
 *
 * The followings are the available columns in table 'generic_project_type':
 * @property integer $id
 * @property integer $project_type_id
 * @property integer $genericprojectcategory_id
 * @property integer $generic_type_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property GenericType $genericType
 * @property Genericprojectcategory $genericprojectcategory
 * @property Staff $staff
 * @property ProjectType $projectType
 * @property ProjectToGenericProjectType[] $projectToGenericProjectTypes
 */
class GenericProjectType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchProjectType;
	public $searchGenericprojectcategory;
	public $searchGenericType;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GenericProjectType the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'generic_project_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_type_id, generic_type_id, staff_id', 'required'),
			array('project_type_id, genericprojectcategory_id, generic_type_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, project_type_id, searchProjectType, searchGenericprojectcategory, searchGenericType, searchStaff', 'safe', 'on'=>'search'),
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
			'genericType' => array(self::BELONGS_TO, 'GenericType', 'generic_type_id'),
			'genericprojectcategory' => array(self::BELONGS_TO, 'Genericprojectcategory', 'genericprojectcategory_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'projectType' => array(self::BELONGS_TO, 'ProjectType', 'project_type_id'),
			'projectToGenericProjectTypes' => array(self::HAS_MANY, 'ProjectToGenericProjectType', 'generic_project_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Generic project type',
			'project_type_id' => 'Client/Project type',
			'searchProjectType' => 'Client/Project type',
			'genericprojectcategory_id' => 'Generic project category',
			'searchGenericprojectcategory' => 'Generic project category',
			'generic_type_id' => 'Generic type',
			'searchGenericType' => 'Generic type',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
//			't.id',
			"CONCAT_WS('$delimiter',
				client.name,
				projectType.description
				) AS searchProjectType",
			'genericprojectcategory.description AS searchGenericprojectcategory',
			'genericType.description AS searchGenericType',
		);

		// where
//		$criteria->compare('t.id',$this->id);
		$this->compositeCriteria($criteria, array(
			'client.name',
			'projectType.description'
		), $this->searchProjectType);
		$criteria->compare('genericprojectcategory.description',$this->searchGenericprojectcategory,true);
		$criteria->compare('genericType.description',$this->searchGenericType,true);
		
		if(isset($this->project_type_id))
		{
			$criteria->compare('t.project_type_id',$this->project_type_id);
		}
		else
		{
			$criteria->select[]="CONCAT_WS('$delimiter',
				client.name,
				projectType.description
				) AS searchProjectType";
			$this->compositeCriteria($criteria,
				array(
					'client.name',
					'projectType.description',
				),
				$this->searchProjectType
			);
		}
		
		//join 
		$criteria->with = array(
			'projectType',
			'projectType.client',
			'genericprojectcategory',
			'genericType',
			);

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
  		if(!isset($this->project_type_id))
		{
			$columns[] = array(
				'name'=>'searchProjectType',
				'value'=>'CHtml::link($data->searchProjectType,
					Yii::app()->createUrl("ProjectType/update", array("id"=>$data->project_type_id))
				)',
				'type'=>'raw',
			);
		}
        $columns[] = array(
			'name'=>'searchGenericprojectcategory',
			'value'=>'CHtml::link($data->searchGenericprojectcategory,
				Yii::app()->createUrl("Genericprojectcategory/update", array("id"=>$data->genericprojectcategory_id))
			)',
			'type'=>'raw',
		);
        $columns[] = array(
			'name'=>'searchGenericType',
			'value'=>'CHtml::link($data->searchGenericType,
				Yii::app()->createUrl("GenericType/update", array("id"=>$data->generic_type_id))
			)',
			'type'=>'raw',
		);
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		ActiveRecord::$labelOverrides['generic_project_type_id'] = 'Generic type';
		return array(
//			'projectType->client->name',
//			'projectType->description',
			'genericType->description',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchProjectType', 'searchGenericprojectcategory', 'searchGenericType');
	}

}

?>