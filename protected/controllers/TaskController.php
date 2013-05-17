<?php
class TaskController extends Controller
{

	public function actionDependantList()
	{
		// a simple cheat to create customValues is to create within cancellable transaction
		$model = new Task();
		$model->attributes = $_POST[$this->modelName];
		// ensure unique task de
		$transaction = Yii::app()->db->beginTransaction();
		if($model->createSave($models))
		{
			// customValues
			$this->widget('CustomFieldWidgets',array(
				'model'=>$model,
				'form'=>new WMTbActiveForm(),
				'relationModelToCustomFieldModelType'=>'taskToCustomFieldToTaskTemplate',
				'relationModelToCustomFieldModelTypes'=>'taskToCustomFieldToTaskTemplates',
				'relationCustomFieldModelType'=>'customFieldToTaskTemplate',
				'relation_category'=>'customFieldTaskCategory',
				'categoryModelName'=>'CustomFieldTaskCategory',
			));
		}

		$transaction->rollBack();
		Yii::app()->end();
	}

}

?>