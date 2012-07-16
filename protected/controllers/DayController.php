<?php

class DayController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Day'),
		));
	}

	public function actionCreate() {
		$model = new Day;

		$this->performAjaxValidation($model, 'day-form');

		if (isset($_POST['Day'])) {
			$model->setAttributes($_POST['Day']);

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
		$model = $this->loadModel($id, 'Day');

		$this->performAjaxValidation($model, 'day-form');

		if (isset($_POST['Day'])) {
			$model->setAttributes($_POST['Day']);

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
			$this->loadModel($id, 'Day')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('Day');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new Day('search');
		$model->unsetAttributes();

		if (isset($_GET['Day']))
			$model->setAttributes($_GET['Day']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}