<?php

class TaskTypeController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'TaskType'),
		));
	}

	public function actionCreate() {
		$model = new TaskType;

		$this->performAjaxValidation($model, 'task-type-form');

		if (isset($_POST['TaskType'])) {
			$model->setAttributes($_POST['TaskType']);
			$relatedData = array(
				'clients' => $_POST['TaskType']['clients'] === '' ? null : $_POST['TaskType']['clients'],
				);

			if ($model->saveWithRelated($relatedData)) {
				if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else
					$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'TaskType');

		$this->performAjaxValidation($model, 'task-type-form');

		if (isset($_POST['TaskType'])) {
			$model->setAttributes($_POST['TaskType']);
			$relatedData = array(
				'clients' => $_POST['TaskType']['clients'] === '' ? null : $_POST['TaskType']['clients'],
				);

			if ($model->saveWithRelated($relatedData)) {
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$this->loadModel($id, 'TaskType')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('TaskType');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new TaskType('search');
		$model->unsetAttributes();

		if (isset($_GET['TaskType']))
			$model->setAttributes($_GET['TaskType']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}