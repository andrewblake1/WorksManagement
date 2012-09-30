<?php

class AssemblyController extends Controller
{
	/*
	 * using createRedirect to save the file as only called onece after transaction is committed
	 */
	protected function createRedirect($model)
	{
		// if a file uploaded
		if(!empty($model->file))
		{
			// save as
			$model->file->saveAs(Yii::app()->params['privateUploadPath'] . 'assembly/' . $model->id);
		}

		parent::createRedirect($model);
	}
	
	public function actionCreate($modalId = 'myModal')
	{
		$model=new $this->modelName;
		if(isset($_POST[$this->modelName]))
		{
			$model->attributes=$_POST[$this->modelName];
			$model->file=CUploadedFile::getInstance($model, 'fileName');
		}
		
		parent::actionCreate($modalId, $model);
	}
	
	protected function updateRedirect($model)
	{
		// if a file uploaded
		if(!empty($model->file))
		{
			// save as
			$model->file->saveAs(Yii::app()->params['privateUploadPath'] . 'assembly/' . $model->id);
		}

		parent::updateRedirect($model);
	}

	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
		if(isset($_POST[$this->modelName]))
		{
			$model->attributes=$_POST[$this->modelName];
			$model->file=CUploadedFile::getInstance($model, 'fileName');
		}
		
		parent::actionUpdate($id, $model);
	}
	
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// remove any file that may have been uploaded
			// local source
			$source = Yii::app()->params['privateUploadPath'] . 'assembly/' . $model->id;
			// remove it if exists
			exec("rm $source");
		}
		
		return actionDelete($id);
	}

}

?>