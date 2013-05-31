<?php

class TaskToActionController extends Controller
{
	protected function createRedirect($model, $params = array()) {
		// go to duty admin view
		$this->redirect(array_merge(array('Duty/update', 'task_to_action' => $model->getPrimaryKey()), $params));
	}

	
}

?>