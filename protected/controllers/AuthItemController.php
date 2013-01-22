<?php

class AuthItemController extends Controller
{
	// this needed to get around primary key not being named id and reflection causing id to be required parameter for actionUpdate
	public function actionUpdate($name, $model = null)
	{
		return parent::actionUpdate($name, $model = null);
	}

	// this needed to get around primary key not being named id and reflection causing id to be required parameter for actionDelete
	public function actionDelete($name)
	{
		return parent::actionDelete($name);
	}
}

?>