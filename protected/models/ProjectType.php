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

	public function scopeClient($client_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('client_id', $client_id);

		$this->getDbCriteria()->mergeWith($criteria);
	
		return $this;
	}

}