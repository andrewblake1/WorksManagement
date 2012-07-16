<?php

class AuthItemController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'AuthItem'),
		));
	}

	public function actionCreate() {
		$model = new AuthItem;

		$this->performAjaxValidation($model, 'auth-item-form');

		if (isset($_POST['AuthItem'])) {
			$model->setAttributes($_POST['AuthItem']);

			if ($model->save()) {
				if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else
					$this->redirect(array('view', 'id' => $model->name));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'AuthItem');

		$this->performAjaxValidation($model, 'auth-item-form');

		if (isset($_POST['AuthItem'])) {
			$model->setAttributes($_POST['AuthItem']);

			if ($model->save()) {
				$this->redirect(array('view', 'id' => $model->name));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$this->loadModel($id, 'AuthItem')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('AuthItem');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new AuthItem('search');
		$model->unsetAttributes();

		if (isset($_GET['AuthItem']))
			$model->setAttributes($_GET['AuthItem']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}