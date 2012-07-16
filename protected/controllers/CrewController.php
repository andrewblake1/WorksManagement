<?php

class CrewController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Crew'),
		));
	}

	public function actionCreate() {
		$model = new Crew;

		$this->performAjaxValidation($model, 'crew-form');

		if (isset($_POST['Crew'])) {
			$model->setAttributes($_POST['Crew']);

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
		$model = $this->loadModel($id, 'Crew');

		$this->performAjaxValidation($model, 'crew-form');

		if (isset($_POST['Crew'])) {
			$model->setAttributes($_POST['Crew']);

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
			$this->loadModel($id, 'Crew')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('Crew');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new Crew('search');
		$model->unsetAttributes();

		if (isset($_GET['Crew']))
			$model->setAttributes($_GET['Crew']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}