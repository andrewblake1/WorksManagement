<?php

class ResourcecategoryController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Resourcecategory'),
		));
	}

	public function actionCreate() {
		$model = new Resourcecategory;

		$this->performAjaxValidation($model, 'resourcecategory-form');

		if (isset($_POST['Resourcecategory'])) {
			$model->setAttributes($_POST['Resourcecategory']);

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
		$model = $this->loadModel($id, 'Resourcecategory');

		$this->performAjaxValidation($model, 'resourcecategory-form');

		if (isset($_POST['Resourcecategory'])) {
			$model->setAttributes($_POST['Resourcecategory']);

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
			$this->loadModel($id, 'Resourcecategory')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('Resourcecategory');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new Resourcecategory('search');
		$model->unsetAttributes();

		if (isset($_GET['Resourcecategory']))
			$model->setAttributes($_GET['Resourcecategory']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}