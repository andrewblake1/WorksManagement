<?php

/**
 * This is the model class for table "tbl_task".
 *
 * The followings are the available columns in table 'tbl_task':
 * @property string $id
 * @property string $level
 * @property string $project_id
 * @property integer $task_template_id
 * @property integer $quantity
 * @property string $location
 * @property integer $preferred_mon
 * @property integer $preferred_tue
 * @property integer $preferred_wed
 * @property integer $preferred_thu
 * @property integer $preferred_fri
 * @property integer $preferred_sat
 * @property integer $preferred_sun
 * @property string $crew_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Duty[] $duties
 * @property Project $project
 * @property User $updatedBy
 * @property TaskTemplate $taskTemplate
 * @property Planning $level
 * @property Crew $crew
 * @property Planning $id0
 * @property TaskToAssembly[] $taskToAssemblies
 * @property TaskToTaskTemplateToCustomField[] $taskToTaskTemplateToCustomFields
 * @property TaskToMaterial[] $taskToMaterials
 * @property TaskToLabourResource[] $taskToLabourResources
 */
class DashboardTask extends Task
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Task';
	
	public function assertFromParent($modelName = null)
	{

	}
	
	public function afterFind() {
	}

	public function tableName() {

		// need to create a single shot instance of creating the temp table that appends required custom columns - only if in search scenario will actually
		// do the search later when attribute assignments have been made which will repeat this - however some methods need the table architecture earlier
		static $tableName = NULL;

		if(!$tableName && strcasecmp(Yii::app()->controller->id, __CLASS__)  == 0 && Yii::app()->controller->action->id == 'admin')
		{
			Yii::app()->db->createCommand("CALL pro_get_tasks_from_duty_admin_view({$_GET['duty_data_id']})")->execute();
			return $tableName = 'tmp_table';
		}

		return $tableName ? $tableName : 'tbl_task';
	}
	
}
?>