<?php

class GenerictaskcategoryController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Generictaskcategory'),
		));
	}

	public function actionCreate() {
		$model = new Generictaskcategory;

		$this->performAjaxValidation($model, 'generictaskcategory-form');

		if (isset($_POST['Generictaskcategory'])) {
			$model->setAttributes($_POST['Generictaskcategory']);

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
		$model = $this->loadModel($id, 'Generictaskcategory');

		$this->performAjaxValidation($model, 'generictaskcategory-form');

		if (isset($_POST['Generictaskcategory'])) {
			$model->setAttributes($_POST['Generictaskcategory']);

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
			$this->loadModel($id, 'Generictaskcategory')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('Generictaskcategory');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new Generictaskcategory('search');
		$model->unsetAttributes();

		if (isset($_GET['Generictaskcategory']))
			$model->setAttributes($_GET['Generictaskcategory']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}