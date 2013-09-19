<?php
class TaskController extends Controller
{

	public function actionDependantList($model = NULL)
	{
		$fromAjax = FALSE;

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
			echo CHtml::openTag('div', array('id'=>'templateDependantArea'));

			$form = new WMTbActiveForm;
			// quantity
			$taskTemplate = $model->taskTemplate;
			$form->rangeFieldRow('quantity', $taskTemplate->quantity, $taskTemplate->minimum, $taskTemplate->maximum, $taskTemplate->select, $taskTemplate->quantity_tooltip, array(), $model);

			// customValues
			$this->widget('CustomFieldWidgets',array(
				'model'=>$model,
				'form'=>new WMTbActiveForm(),
				'relationModelToCustomFieldModelTemplate'=>'taskToTaskTemplateToCustomField',
				'relationModelToCustomFieldModelTemplates'=>'taskToTaskTemplateToCustomFields',
				'relationCustomFieldModelTemplate'=>'taskTemplateToCustomField',
				'relation_category'=>'customFieldTaskCategory',
				'categoryModelName'=>'CustomFieldTaskCategory',
				'ajax'=>$fromAjax,
			));

			echo CHtml::closeTag('div');
		}

		$transaction->rollBack();
		if($fromAjax)
		{
			Yii::app()->end();
		}
	}

}

?>