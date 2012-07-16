<?php

class GenericTaskTypeController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'GenericTaskType'),
		));
	}

	public function actionCreate() {
		$model = new GenericTaskType;

		$this->performAjaxValidation($model, 'generic-task-type-form');

		if (isset($_POST['GenericTaskType'])) {
			$model->setAttributes($_POST['GenericTaskType']);

			if ($model->save()) {
				if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else
					$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'GenericTaskType');

		$this->performAjaxValidation($model, 'generic-task-type-form');

		if (isset($_POST['GenericTaskType'])) {
			$model->setAttributes($_POST['GenericTaskType']);

			if ($model->save()) {
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$this->loadModel($id, 'GenericTaskType')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('GenericTaskType');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new GenericTaskType('search');
		$model->unsetAttributes();

		if (isset($_GET['GenericTaskType']))
			$model->setAttributes($_GET['GenericTaskType']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}