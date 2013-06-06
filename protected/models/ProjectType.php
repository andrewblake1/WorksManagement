<?php

/**
 * This is the model class for table "tbl_project_type".
 *
 * The followings are the available columns in table 'tbl_project_type':
 * @property integer $id
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 * @property string $name
 * @property integer $project_template_id
 * @property integer $client_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Project[] $projects
 * @property Project[] $projects1
 * @property ProjectTemplate $projectTemplate
 * @property ProjectTemplate $client
 */
class ProjectType extends CategoryActiveRecord {

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE1: you should only define rules for those attributes that
		// will receive user inputs.
		// NOTE2: Remove ALL rules associated with the nested Behavior:
		//rgt,lft,root,level,id.
		return array_merge(parent::rules(), array(
			array('name, project_template_id, client_id', 'required'),
            array('project_template_id, client_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max' => 64),
			array('client_id', 'safe'),
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
            'projects' => array(self::HAS_MANY, 'Project', 'project_type_id'),
            'projects1' => array(self::HAS_MANY, 'Project', 'client_id'),
            'projectTemplate' => array(self::BELONGS_TO, 'ProjectTemplate', 'project_template_id'),
            'client' => array(self::BELONGS_TO, 'ProjectTemplate', 'client_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'client_id' => 'Client',
			'project_template_id' => 'Template',
		) + parent::attributeLabels();
	}

	public function scopeClient($client_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('client_id', $client_id);

		$this->getDbCriteria()->mergeWith($criteria);
	
		return $this;
	}

}