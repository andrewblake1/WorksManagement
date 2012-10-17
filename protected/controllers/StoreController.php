<?php

class StoreController extends Controller
{
	public function accessRules()
	{
		$accessRules = parent::accessRules();
		array_unshift($accessRules,
			array('allow',
				'actions'=>array('dynamicMaterials'),
				'roles'=>array($this->modelName),
		));

		return $accessRules;
	}

	public function actionDynamicMaterials()
	{
		$modelName = $_POST['controller'];
		ob_start();
		$form=$this->beginWidget('WMTbActiveForm', array('model'=>Material::model(), 'parent_fk'=>'material_id'));
		ob_end_clean();
		MaterialController::listWidgetRow($modelName::model(), $form, 'material_id', array(),
			array('scopeStore'=>array((int)$_POST[$modelName]['store_id'])));
	}

	public function actionDynamicAssemblies()
	{
		$modelName = $_POST['controller'];
		ob_start();
		$form=$this->beginWidget('WMTbActiveForm', array('model'=>Assembly::model(), 'parent_fk'=>'assembly_id'));
		ob_end_clean();
		AssemblyController::listWidgetRow($modelName::model(), $form, 'assembly_id', array(),
			array('scopeStore'=>array((int)$_POST[$modelName]['store_id'])));
	}

}
