<?php

class DutyTypeController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'DutyType'),
		));
	}

	public function actionCreate() {
		$model = new DutyType;

		$this->performAjaxValidation($model, 'duty-type-form');

		if (isset($_POST['DutyType'])) {
			$model->setAttributes($_POST['DutyType']);

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
		$model = $this->loadModel($id, 'DutyType');

		$this->performAjaxValidation($model, 'duty-type-form');

		if (isset($_POST['DutyType'])) {
			$model->setAttributes($_POST['DutyType']);

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
			$this->loadModel($id, 'DutyType')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('DutyType');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new DutyType('search');
		$model->unsetAttributes();

		if (isset($_GET['DutyType']))
			$model->setAttributes($_GET['DutyType']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}