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

	public function actionDependantList()
	{
		// a simple cheat to create generics is to create within cancellable transaction
		$model = new Project();
		$model->attributes = $_POST[$this->modelName];
		// ensure unique project de
		$transaction = Yii::app()->db->beginTransaction();
		if($model->createSave($models))
		{
			// generics
			$this->widget('GenericWidgets',array(
				'model'=>$model,
				'form'=>new WMTbActiveForm(),
				'relation_modelToGenericModelType'=>'projectToGenericProjectType',
				'relation_modelToGenericModelTypes'=>'projectToGenericProjectTypes',
				'relation_genericModelType'=>'genericProjectType',
				'relation_category'=>'genericprojectcategory',
				'categoryModelName'=>'Genericprojectcategory',
			));
		}

		$transaction->rollBack();
		Yii::app()->end();
	}

}

?>