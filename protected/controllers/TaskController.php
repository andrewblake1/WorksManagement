<?php
class TaskController extends Controller
{

	public function actionDependantList($model = NULL)
	{
		// a simple cheat to create customValues is to create within cancellable transaction
		if(!$model)
		{
			$model = new Task();
			$model->attributes = $_POST[$this->modelName];
			$fromAjax = TRUE;
		}

		// ensure unique task de
		$transaction = Yii::app()->db->beginTransaction();
		$model->beforeValidate();
		// need to turn of foreign key checks otherwise if mode not selected will fail to create
		Yii::app()->db->createCommand('SET foreign_key_checks = 0')->execute();
		if($model->createSave($models, false))
		{
			// customValues
			$this->widget('CustomFieldWidgets',array(
				'model'=>$model,
				'form'=>new WMTbActiveForm(),
				'relationModelToCustomFieldModelTemplate'=>'taskToTaskTemplateToCustomField',
				'relationModelToCustomFieldModelTemplates'=>'taskToTaskTemplateToCustomFields',
				'relationCustomFieldModelTemplate'=>'taskTemplateToCustomField',
				'relation_category'=>'customFieldTaskCategory',
				'categoryModelName'=>'CustomFieldTaskCategory',
			));
		}

		$transaction->rollBack();
		if(isset($fromAjax))
		{
			Yii::app()->end();
		}
	}

}

?>