<?php

class GenericProjectTypeController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'GenericProjectType'),
		));
	}

	public function actionCreate() {
		$model = new GenericProjectType;

		$this->performAjaxValidation($model, 'generic-project-type-form');

		if (isset($_POST['GenericProjectType'])) {
			$model->setAttributes($_POST['GenericProjectType']);
			$relatedData = array(
				'projects' => $_POST['GenericProjectType']['projects'] === '' ? null : $_POST['GenericProjectType']['projects'],
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
		$model = $this->loadModel($id, 'GenericProjectType');

		$this->performAjaxValidation($model, 'generic-project-type-form');

		if (isset($_POST['GenericProjectType'])) {
			$model->setAttributes($_POST['GenericProjectType']);
			$relatedData = array(
				'projects' => $_POST['GenericProjectType']['projects'] === '' ? null : $_POST['GenericProjectType']['projects'],
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
			$this->loadModel($id, 'GenericProjectType')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('GenericProjectType');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new GenericProjectType('search');
		$model->unsetAttributes();

		if (isset($_GET['GenericProjectType']))
			$model->setAttributes($_GET['GenericProjectType']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}