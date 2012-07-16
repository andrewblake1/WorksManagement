<?php

class ClientController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Client'),
		));
	}

	public function actionCreate() {
		$model = new Client;

		$this->performAjaxValidation($model, 'client-form');

		if (isset($_POST['Client'])) {
			$model->setAttributes($_POST['Client']);
			$relatedData = array(
				'taskTypes' => $_POST['Client']['taskTypes'] === '' ? null : $_POST['Client']['taskTypes'],
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
		$model = $this->loadModel($id, 'Client');

		$this->performAjaxValidation($model, 'client-form');

		if (isset($_POST['Client'])) {
			$model->setAttributes($_POST['Client']);
			$relatedData = array(
				'taskTypes' => $_POST['Client']['taskTypes'] === '' ? null : $_POST['Client']['taskTypes'],
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
			$this->loadModel($id, 'Client')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('Client');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new Client('search');
		$model->unsetAttributes();

		if (isset($_GET['Client']))
			$model->setAttributes($_GET['Client']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}