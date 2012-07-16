<?php

class GenericprojectcategoryController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Genericprojectcategory'),
		));
	}

	public function actionCreate() {
		$model = new Genericprojectcategory;

		$this->performAjaxValidation($model, 'genericprojectcategory-form');

		if (isset($_POST['Genericprojectcategory'])) {
			$model->setAttributes($_POST['Genericprojectcategory']);

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
		$model = $this->loadModel($id, 'Genericprojectcategory');

		$this->performAjaxValidation($model, 'genericprojectcategory-form');

		if (isset($_POST['Genericprojectcategory'])) {
			$model->setAttributes($_POST['Genericprojectcategory']);

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
			$this->loadModel($id, 'Genericprojectcategory')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('Genericprojectcategory');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new Genericprojectcategory('search');
		$model->unsetAttributes();

		if (isset($_GET['Genericprojectcategory']))
			$model->setAttributes($_GET['Genericprojectcategory']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}