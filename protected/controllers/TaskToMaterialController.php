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
					'visible'=>'Yii::app()->user->checkAccess($data->material_group_id ? "TaskToMaterialToMaterialGroupToMaterial" : "TaskToMaterial", array("primaryKey"=>$data->material_group_id ? $data->material_group_id : $data->id))',
					'url'=>'Yii::app()->createUrl(
								$data->material_group_id
									? $data->id
										? "TaskToMaterialToMaterialGroupToMaterial/update"
										: "TaskToMaterialToMaterialGroupToMaterial/create"
									: "TaskToMaterial/update",
								$data->material_group_id
									? array("id"=>$data->searchTaskToMaterialToMaterialGroupToMaterialId, "TaskToMaterialToMaterialGroupToMaterial"=>array(
										"material_group_id"=>$data->material_group_id,
										"material_id"=>$data->material_id,
										"task_id"=>$data->task_id,
										"task_to_assembly_id"=>$data->task_to_assembly_id,
										"assembly_to_material_group_id"=>$data->assembly_to_material_group_id,
										))
									: array("id"=>$data->id)
							)',
				),
				'view' => array(
					'visible'=>'!Yii::app()->user->checkAccess($data->material_group_id ? "TaskToMaterialToMaterialGroupToMaterial" : "TaskToMaterial", array("primaryKey"=>$data->material_group_id ? $data->material_group_id : $data->id))
						&& Yii::app()->user->checkAccess($data->material_group_id ? "TaskToMaterialToMaterialGroupToMaterialRead" : "TaskToMaterialRead")',
				'url'=>'Yii::app()->createUrl(($data->material_group_id ? "TaskToMaterialToMaterialGroupToMaterial" : "TaskToMaterial" ).
							"/view", array("id"=>$data->material_group_id ? $data->material_group_id : $data->id))',
					),
			),
		);
	}
	
}

?>