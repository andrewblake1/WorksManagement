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
					'visible' => 'Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("id"=>$data->id))',
					'url' => 'Yii::app()->createUrl("TaskToAction/delete", array("id"=>$data->id, "action_id"=>$data->id, "task_id"=>$data->task_id))',
				),
				'update' => array(
					'visible' => 'FALSE',
				),
				'view' => array(
					'visible' => '
						Yii::app()->user->checkAccess(get_class($data)."Read")',
					'url' => 'Yii::app()->createUrl("Duty/admin", array("action_id"=>$data->id, "task_id"=>$data->task_id))',
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
	
	public function actionDelete($id) {
		if(Yii::app()->request->isPostRequest)
		{
			try
			{
				// we only allow deletion via POST request
				$model = $this->loadModel($id);
				// this is a virtual model - loop thru the actual models
				foreach(ViewDuty::model()->findAllByAttributes(array(
					'action_id'=>$_GET['action_id'],
					'task_id'=>$_GET['task_id'])) as $viewDuty)
				{
					$dutyData = DutyData::model()->findByPk($viewDuty->duty_data_id);
					$dutyData->delete();
				}

			}
			catch (CDbException $e)
			{
				if (!isset($_GET['ajax'])) {
					Yii::app()->user->setFlash('error', "<strong>Database error - contact system admin!</strong>
						$e");
				} else {
					echo "
						<div class='alert alert-block alert-error fade in'>
							<a class='close' data-dismiss='alert'>×</a>
							<strong>Database error - contact system admin!</strong>
							$e
						</div>
					";
				}
			}

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if (!isset($_GET['ajax']))
			{
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin', $this->modelName => static::getAdminParams($this->modelName)));
			}
		}
		else
		{
			throw new CHttpException(400, 'Invalid request.');
		}
	}
}

?>