<?php

class GenericController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Generic'),
		));
	}

	public function actionCreate() {
		$model = new Generic;

		$this->performAjaxValidation($model, 'generic-form');

		if (isset($_POST['Generic'])) {
			$model->setAttributes($_POST['Generic']);

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
		$model = $this->loadModel($id, 'Generic');

		$this->performAjaxValidation($model, 'generic-form');

		if (isset($_POST['Generic'])) {
			$model->setAttributes($_POST['Generic']);

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
			$this->loadModel($id, 'Generic')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('Generic');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new Generic('search');
		$model->unsetAttributes();

		if (isset($_GET['Generic']))
			$model->setAttributes($_GET['Generic']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}