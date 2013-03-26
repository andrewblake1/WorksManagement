<?php

class TaskToMaterialController extends Controller
{
	/**
	 * @var string the name of the model to use in the admin view - the model may serve a database view as opposed to a table  
	 */
	protected $_adminViewModel = 'ViewTaskToMaterial';

	// called within AdminViewWidget
	public function getButtons($model)
	{
		return array(
			'class'=>'WMTbButtonColumn',
			'buttons'=>array(
				'delete' => array(
					'visible'=>'Yii::app()->user->checkAccess($data->id ? "TaskToMaterial" : "TaskToMaterialToMaterialGroupToMaterial", array("primaryKey"=>$data->id ? $data->id : $data->material_group_id))',
					'url'=>'Yii::app()->createUrl(($data->id ? "TaskToMaterial" : "TaskToMaterialToMaterialGroupToMaterial" ).
							"/delete", array("id"=>$data->id ? $data->id : $data->material_group_id))',
				),
				'update' => array(
					'visible'=>'Yii::app()->user->checkAccess($data->id ? "TaskToMaterial" : "TaskToMaterialToMaterialGroupToMaterial", array("primaryKey"=>$data->id ? $data->id : $data->material_group_id))',
					'url'=>'Yii::app()->createUrl(($data->id ? "TaskToMaterial/update" : "TaskToMaterialToMaterialGroupToMaterial/create" ), array("id"=>$data->id ? $data->id : $data->material_group_id))',
				),
				'view' => array(
					'visible'=>'!Yii::app()->user->checkAccess($data->id ? "TaskToMaterial" : "TaskToMaterialToMaterialGroupToMaterial", array("primaryKey"=>$data->id ? $data->id : $data->material_group_id))
						&& Yii::app()->user->checkAccess($data->id ? "TaskToMaterialRead" : "TaskToMaterialToMaterialGroupToMaterialRead")',
					'url'=>'Yii::app()->createUrl(($data->id ? "TaskToMaterial" : "TaskToMaterialToMaterialGroupToMaterial" ).
							"/view", array("id"=>$data->id ? $data->id : $data->material_group_id))',
				),
			),
		);
	}

}

?>