<?php

class ResourceDataToModeController extends Controller
{
	// redirect to admin
	protected function adminRedirect($model, $sortByNewest = false)
	{
		// just a simple hack to get ap to return to correctly filtered admin view. This needed due to special
		// nature of bypassing resource data view
		if(!empty($_POST['task_to_resource_id']))
		{
			$_GET['task_to_resource_id'] = $_POST['task_to_resource_id'];
		}

		static::staticAdminRedirect($model, $sortByNewest);
	}

	
	public function getButtons($model)
	{
		return array(
			'class' => 'WMTbButtonColumn',
			'buttons' => array(
				'delete' => array(
					'visible' => 'Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("primaryKey"=>$data->primaryKey))',
					'url' => 'Yii::app()->createUrl("' . $this->modelName . '/delete", array("' . $model->tableSchema->primaryKey . '"=>$data->primaryKey))',
				),
				'update' => array(
					'visible' => 'Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("primaryKey"=>$data->primaryKey))',
					'url' => 'Yii::app()->createUrl("' . $this->modelName . '/update", array("id"=>$data->id, "task_to_resource_id"=>$_GET["task_to_resource_id"]))',
				),
				'view' => array(
					'visible' => '
						!Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("primaryKey"=>$data->primaryKey))
						&& Yii::app()->user->checkAccess(get_class($data)."Read")',
					'url' => 'Yii::app()->createUrl("' . $this->modelName . '/view", array("' . $model->tableSchema->primaryKey . '"=>$data->primaryKey))',
				),
			),
		);
	}
}
