<?php

class TaskToActionController extends Controller
{
	// called within AdminViewWidget
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
					'visible' => 'FALSE',
				),
				'view' => array(
					'visible' => '
						Yii::app()->user->checkAccess(get_class($data)."Read")',
					'url' => 'Yii::app()->createUrl("Duty/admin", array("action_id"=>$data->primaryKey, "task_id"=>$data->task_id))',
				),
			),
		);
	}

	protected function createRedirect($model, $params = array()) {
		// go to duty admin view
//		$this->redirect(array_merge(array('Duty/admin', 'task_to_action' => $model->getPrimaryKey()), $params));
		$this->redirect(array_merge(array('Duty/admin',
			'action_id' => $model->action_id,
			'task_id' => $model->task_id,
		), $params));
	}
	
}

?>