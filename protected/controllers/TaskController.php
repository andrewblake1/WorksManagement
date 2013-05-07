<?php
class TaskController extends Controller
{

	public function actionDependantList()
	{
		// a simple cheat to create generics is to create within cancellable transaction
		$model = new Task();
		$model->attributes = $_POST[$this->modelName];
		// ensure unique task de
		$transaction = Yii::app()->db->beginTransaction();
		if($model->createSave($models))
		{
			// generics
			$this->widget('GenericWidgets',array(
				'model'=>$model,
				'form'=>new WMTbActiveForm(),
				'relation_modelToGenericModelType'=>'taskToGenericTaskType',
				'relation_modelToGenericModelTypes'=>'taskToGenericTaskTypes',
				'relation_genericModelType'=>'genericTaskType',
				'relation_category'=>'generictaskcategory',
				'categoryModelName'=>'Generictaskcategory',
			));
		}

		$transaction->rollBack();
		Yii::app()->end();
	}

}

?>