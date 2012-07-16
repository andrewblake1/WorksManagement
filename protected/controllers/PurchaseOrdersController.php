<?php

class PurchaseOrdersController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'PurchaseOrders'),
		));
	}

	public function actionCreate() {
		$model = new PurchaseOrders;

		$this->performAjaxValidation($model, 'purchase-orders-form');

		if (isset($_POST['PurchaseOrders'])) {
			$model->setAttributes($_POST['PurchaseOrders']);

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
		$model = $this->loadModel($id, 'PurchaseOrders');

		$this->performAjaxValidation($model, 'purchase-orders-form');

		if (isset($_POST['PurchaseOrders'])) {
			$model->setAttributes($_POST['PurchaseOrders']);

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
			$this->loadModel($id, 'PurchaseOrders')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('PurchaseOrders');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new PurchaseOrders('search');
		$model->unsetAttributes();

		if (isset($_GET['PurchaseOrders']))
			$model->setAttributes($_GET['PurchaseOrders']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}