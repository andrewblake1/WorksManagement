<?php

class ScheduleController extends CategoryController
{
	public function actionFetchTree()
	{
		parent::actionFetchTree($_SESSION['actionAdminGet']['Schedule']['project_id']);
	}

}

?>