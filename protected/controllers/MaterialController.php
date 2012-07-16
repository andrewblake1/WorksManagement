<?php

class MaterialController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Material'),
		));
	}

	public function actionCreate() {
		$model = new Material;

		$this->performAjaxValidation($model, 'material-form');

		if (isset($_POST['Material'])) {
			$model->setAttributes($_POST['Material']);
			$relatedData = array(
				'tasks' => $_POST['Material']['tasks'] === '' ? null : $_POST['Material']['tasks'],
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
		$model = $this->loadModel($id, 'Material');

		$this->performAjaxValidation($model, 'material-form');

		if (isset($_POST['Material'])) {
			$model->setAttributes($_POST['Material']);
			$relatedData = array(
				'tasks' => $_POST['Material']['tasks'] === '' ? null : $_POST['Material']['tasks'],
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
			$this->loadModel($id, 'Material')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('Material');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new Material('search');
		$model->unsetAttributes();

		if (isset($_GET['Material']))
			$model->setAttributes($_GET['Material']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}