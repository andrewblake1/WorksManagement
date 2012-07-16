<?php

class SupplierController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Supplier'),
		));
	}

	public function actionCreate() {
		$model = new Supplier;

		$this->performAjaxValidation($model, 'supplier-form');

		if (isset($_POST['Supplier'])) {
			$model->setAttributes($_POST['Supplier']);

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
		$model = $this->loadModel($id, 'Supplier');

		$this->performAjaxValidation($model, 'supplier-form');

		if (isset($_POST['Supplier'])) {
			$model->setAttributes($_POST['Supplier']);

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
			$this->loadModel($id, 'Supplier')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('Supplier');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new Supplier('search');
		$model->unsetAttributes();

		if (isset($_GET['Supplier']))
			$model->setAttributes($_GET['Supplier']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}