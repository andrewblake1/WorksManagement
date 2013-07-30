<?php
class ProjectController extends Controller
{

	public function actionUpdate($id) {
		// ensure access
		if(!Yii::app()->user->checkAccess('Project', array('primaryKey'=>$id)))
		{
			throw new CHttpException(403);
		}
		
		parent::actionUpdate($id);
	}

	public function actionDelete($id) {
		// ensure access
		if(!Yii::app()->user->checkAccess('Project', array('primaryKey'=>$id)))
		{
			throw new CHttpException(403);
		}
		
		parent::actionDelete($id);
	}

	// pretend to create the project in order to take a shortcut of creating the related items
	// in order to generate the custom fields
	public function actionDependantList($model = NULL)
	{
		// a simple cheat to create customValues is to create within cancellable transaction
		// NB: don't set any attributes as might fail validation
		if(!$model)
		{
			$model = new Project();
			$model->client_id = $_POST['Project']['client_id'];
			$model->project_type_id = $_POST['Project']['project_type_id'];
			$fromAjax = TRUE;
		}

		// ensure unique project de
		$transaction = Yii::app()->db->beginTransaction();
		if($model->createSave($models))
		{
			// customValues
			$this->widget('CustomFieldWidgets',array(
				'model'=>$model,
				'form'=>new WMTbActiveForm(),
				'relationModelToCustomFieldModelTemplate'=>'projectToProjectTemplateToCustomField',
				'relationModelToCustomFieldModelTemplates'=>'projectToProjectTemplateToCustomFields',
				'relationCustomFieldModelTemplate'=>'projectTemplateToCustomField',
				'relation_category'=>'customFieldProjectCategory',
				'categoryModelName'=>'CustomFieldProjectCategory',
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