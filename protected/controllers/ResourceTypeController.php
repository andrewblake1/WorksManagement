<?php

class ResourceTypeController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'ResourceType'),
		));
	}

	public function actionCreate() {
		$model = new ResourceType;

		$this->performAjaxValidation($model, 'resource-type-form');

		if (isset($_POST['ResourceType'])) {
			$model->setAttributes($_POST['ResourceType']);
			$relatedData = array(
				'tasks' => $_POST['ResourceType']['tasks'] === '' ? null : $_POST['ResourceType']['tasks'],
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
		$model = $this->loadModel($id, 'ResourceType');

		$this->performAjaxValidation($model, 'resource-type-form');

		if (isset($_POST['ResourceType'])) {
			$model->setAttributes($_POST['ResourceType']);
			$relatedData = array(
				'tasks' => $_POST['ResourceType']['tasks'] === '' ? null : $_POST['ResourceType']['tasks'],
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
			$this->loadModel($id, 'ResourceType')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('ResourceType');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new ResourceType('search');
		$model->unsetAttributes();

		if (isset($_GET['ResourceType']))
			$model->setAttributes($_GET['ResourceType']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}