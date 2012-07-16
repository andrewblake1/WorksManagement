<?php

class TaskController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Task'),
		));
	}

	public function actionCreate() {
		$model = new Task;

		$this->performAjaxValidation($model, 'task-form');

		if (isset($_POST['Task'])) {
			$model->setAttributes($_POST['Task']);
			$relatedData = array(
				'clientToTaskTypeToDutyTypes' => $_POST['Task']['clientToTaskTypeToDutyTypes'] === '' ? null : $_POST['Task']['clientToTaskTypeToDutyTypes'],
				'materials' => $_POST['Task']['materials'] === '' ? null : $_POST['Task']['materials'],
				'assemblies' => $_POST['Task']['assemblies'] === '' ? null : $_POST['Task']['assemblies'],
				'resourceTypes' => $_POST['Task']['resourceTypes'] === '' ? null : $_POST['Task']['resourceTypes'],
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
		$model = $this->loadModel($id, 'Task');

		$this->performAjaxValidation($model, 'task-form');

		if (isset($_POST['Task'])) {
			$model->setAttributes($_POST['Task']);
			$relatedData = array(
				'clientToTaskTypeToDutyTypes' => $_POST['Task']['clientToTaskTypeToDutyTypes'] === '' ? null : $_POST['Task']['clientToTaskTypeToDutyTypes'],
				'materials' => $_POST['Task']['materials'] === '' ? null : $_POST['Task']['materials'],
				'assemblies' => $_POST['Task']['assemblies'] === '' ? null : $_POST['Task']['assemblies'],
				'resourceTypes' => $_POST['Task']['resourceTypes'] === '' ? null : $_POST['Task']['resourceTypes'],
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
			$this->loadModel($id, 'Task')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('Task');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new Task('search');
		$model->unsetAttributes();

		if (isset($_GET['Task']))
			$model->setAttributes($_GET['Task']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}