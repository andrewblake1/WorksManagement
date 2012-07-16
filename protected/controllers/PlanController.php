<?php

class PlanController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Plan'),
		));
	}

	public function actionCreate() {
		$model = new Plan;

		$this->performAjaxValidation($model, 'plan-form');

		if (isset($_POST['Plan'])) {
			$model->setAttributes($_POST['Plan']);

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
		$model = $this->loadModel($id, 'Plan');

		$this->performAjaxValidation($model, 'plan-form');

		if (isset($_POST['Plan'])) {
			$model->setAttributes($_POST['Plan']);

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
			$this->loadModel($id, 'Plan')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('Plan');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new Plan('search');
		$model->unsetAttributes();

		if (isset($_GET['Plan']))
			$model->setAttributes($_GET['Plan']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}