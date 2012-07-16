<?php

class ProjectController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Project'),
		));
	}

	public function actionCreate() {
		$model = new Project;

		$this->performAjaxValidation($model, 'project-form');

		if (isset($_POST['Project'])) {
			$model->setAttributes($_POST['Project']);
			$relatedData = array(
				'authAssignments' => $_POST['Project']['authAssignments'] === '' ? null : $_POST['Project']['authAssignments'],
				'genericProjectTypes' => $_POST['Project']['genericProjectTypes'] === '' ? null : $_POST['Project']['genericProjectTypes'],
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
		$model = $this->loadModel($id, 'Project');

		$this->performAjaxValidation($model, 'project-form');

		if (isset($_POST['Project'])) {
			$model->setAttributes($_POST['Project']);
			$relatedData = array(
				'authAssignments' => $_POST['Project']['authAssignments'] === '' ? null : $_POST['Project']['authAssignments'],
				'genericProjectTypes' => $_POST['Project']['genericProjectTypes'] === '' ? null : $_POST['Project']['genericProjectTypes'],
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
			$this->loadModel($id, 'Project')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('Project');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new Project('search');
		$model->unsetAttributes();

		if (isset($_GET['Project']))
			$model->setAttributes($_GET['Project']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}