<?php

class DutyController extends Controller
{
	/**
	 * Specifies the access control rules.
	 * NB: need to override this to open up so can shift access control into actionUpdate method to pass params for bizrule
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('admin','index','view', 'update'),
				'roles'=>array($this->modelName.'Read'),
			),
			array('allow',
				'actions'=>array('create','delete','autocomplete'),
				'roles'=>array($this->modelName),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	// special handling of update for duties
	public function actionUpdate($id)
	{
		// get duty
		$model = Duty::model()->findByPk($id);
		// get who the duty is assigned to
		$assignedTo = $model->task->project->projectToProjectTypeToAuthItems->authAssignment->userid;

		// system admin
		if(Yii::app()->user->checkAccess('system admin'))
		{
			// is the only one allowed to alter once ticked off as complete
			parent::actionUpdate($id);
		}
		// other users with full Duty access or has DutyUpdate permission - has to be assigned to this duty
		elseif(Yii::app()->user->checkAccess('Duty') || Yii::app()->user->checkAccess('DutyUpdate', array('assignedTo'=>$assignedTo)))
		{
			// can only update if not completed
			if(!empty($model->updated))
			{
				parent::actionUpdate($id);
			}
			// otherwise can view
			{
				$this->actionView($id);
			}
		}
		// otherwise doesn't have permission to be here
		else
		{
			throw new CHttpException(403,'You do not have permission to view this duty.');
		}
	}

	/*
	 * overidden as because generic possibly added and want to edit after creation
	 */
	protected function createRedirect($model)
	{
		$this->redirect(array('update', 'id'=>$model->getPrimaryKey()));
	}

	/*
	 * overidden as mulitple models
	 */
	protected function createSave($model, &$models=array())
	{
		$saved = true;

		// if we need to create a generic
		if(!empty($model->taskTypeToDutyType->dutyType->generic_type_id))
		{
			// create a new generic item to hold value
			$saved &= Generic::createGeneric($model->taskTypeToDutyType->dutyType->genericType, $models, $generic);
			// associate the new generic to this duty
			$model->generic_id = $generic->id;
		}
		
		return $saved & parent::createSave($model, $models);
	}

	/*
	 * overidden as mulitple models
	 */
	protected function updateSave($model,  &$models=array())
	{
		$saved = true;

		$generic = $model->generic;

		// if we need to update a generic
		if(!empty($model->taskTypeToDutyType->dutyType->generic_type_id))
		{
			// massive assignement
			$generic->attributes=$_POST['Generic'][$generic->id];

			// set Generic custom validators as per the associated generic type
			$generic->setCustomValidators($model->taskTypeToDutyType->dutyType->genericType,
				array(
					'relationToGenericType'=>'duty->taskTypeToDutyType->dutyType->genericType',
				)
			);

			$saved &= parent::updateSave($generic, $models);
		}
		
		return $saved & parent::updateSave($model, $models);
	}

}

?>