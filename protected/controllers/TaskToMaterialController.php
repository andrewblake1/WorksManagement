<?php

class TaskToMaterialController extends Controller
{
	// called within AdminViewWidget
	public function getButtons($model)
	{
//TODO: repeated
		// add jquery responsible for handling multiple form possiblities here - i.e. this handling multiple models
		// need to re-read modal contents first as selecting a different record
		$modelName = $this->modelName;
		Yii::app()->clientScript->registerScript('onclickReturnForm', "
			function onclickReturnForm(obj)
			{
				// need to re-read modal contents first as selecting a different record
				$.ajax({
					type: 'POST',
					url: $(obj).attr('href'),
					'beforeSend' : function(){
						$('#$modelName-grid').addClass('ajax-sending');
					},
					'complete' : function(){
						$('#$modelName-grid').removeClass('ajax-sending');
					},
					success: function(data){
						// change the contents
						$('#myModal div').html(data);
						// display the modal
						$('#myModal').modal('show');

					} //success
				});//ajax
			}
		", CClientScript::POS_END);
		
		return array(
			'class'=>'WMTbButtonColumn',
			'buttons'=>array(
				'delete' => array(
					'visible'=>'Yii::app()->user->checkAccess("' . $this->modelName . '")',
					'url'=>'Yii::app()->createUrl(
						$data->material_group_id
							? ($data->task_to_assembly_id
								? "TaskToMaterialToAssemblyToMaterialGroup/delete"
								: "TaskToMaterialToTaskTemplateToMaterialGroup/delete")
							: "TaskToMaterial/delete",

						$data->material_group_id
							? ($data->task_to_assembly_id
								? array("id"=>$data->task_to_material_to_assembly_to_material_group_id)
								: array("id"=>$data->task_to_material_to_task_template_to_material_group_id))
							: array("id"=>$data->id)
					)',
				),
				'update' => array(
					'visible'=>'Yii::app()->user->checkAccess("' . $this->modelName . '")',
					'url'=>'Yii::app()->createUrl(
						$data->material_group_id
							? ($data->task_to_assembly_id
								? "TaskToMaterialToAssemblyToMaterialGroup/returnForm"
								: "TaskToMaterialToTaskTemplateToMaterialGroup/returnForm")
							: "TaskToMaterial/update",

						$data->material_group_id
							? ($data->task_to_assembly_id
								? array("id"=>$data->task_to_material_to_assembly_to_material_group_id, "TaskToMaterialToAssemblyToMaterialGroup"=>array(
									"material_group_id"=>$data->material_group_id,
									"material_id"=>$data->material_id,
									"task_id"=>$data->task_id,
									"task_to_assembly_id"=>$data->task_to_assembly_id,
									"assembly_to_material_group_id"=>$data->assembly_to_material_group_id,
									))
								: array("id"=>$data->task_to_material_to_task_template_to_material_group_id, "TaskToMaterialToTaskTemplateToMaterialGroup"=>array(
									"material_group_id"=>$data->material_group_id,
									"material_id"=>$data->material_id,
									"task_id"=>$data->task_id,
									"task_template_to_material_group_id"=>$data->task_template_to_material_group_id,
									)))
							: array("id"=>$data->id)
					)',
					'click'=>'function() {if($(this).attr("href").indexOf("returnForm") >= 0) { onclickReturnForm(this); return false; }}',
				),
				'view' => array(
					'visible'=>'
						!Yii::app()->user->checkAccess("' . $this->modelName . '")
						&& Yii::app()->user->checkAccess("' . $this->modelName . 'Read")',
					'url'=>'Yii::app()->createUrl((
						$data->material_group_id
							? ($data->task_to_assembly_id
								? "TaskToMaterialToAssemblyToMaterialGroup"
								: "TaskToMaterialToTaskTemplateToMaterialGroup")
							: "TaskToMaterial"
						) . "/view", array("id"=>
							$data->material_group_id
								? $data->material_group_id
								: $data->id
					))',
				),
			),
		);
	}
	
	// override the tabs when viewing materials for a particular task - make match taskToAssembly view
	public function setTabs($model = NULL, &$tabs = NULL) {
		$modelName = $this->modelName;
		$update = FALSE;

		if(!empty($model->taskToAssembly->id))
		{
			$update = $parent_id = $model->taskToAssembly->id;
		}
		elseif(isset($_GET['task_to_assembly_id']))
		{
			$parent_id = $_GET['task_to_assembly_id'];
		}
		else
		{
			parent::setTabs($model);
		}
		
		
		if(!empty($parent_id))
		{
			$_GET['parent_id'] = $task_to_assembly_id = $parent_id;
			$taskToAssemblyController= new TaskToAssemblyController(NULL);
			$taskToAssembly = TaskToAssembly::model()->findByPk($task_to_assembly_id);
			$taskToAssemblyController->setTabs(NULL);
			$taskToAssemblyController->setActiveTabs(TaskToAssembly::getNiceNamePlural(), $modelName::getNiceNamePlural());
			static::$tabs = $taskToAssemblyController->tabs;

			static::setUpdateId(NULL, 'TaskToAssembly');

			if($update)
			{
				$lastLabel = $modelName::getNiceName(isset($_GET['id']) ? $_GET['id'] : NULL);
				$tabs=array();
				$this->addTab(
					$lastLabel,
					Yii::app()->controller->modelName,
					Yii::app()->controller->action->id,
					$_GET,
					$tabs,
					TRUE
				);
				static::$tabs = array_merge(static::$tabs, array($tabs));
			}

			$this->breadcrumbs = TaskToAssemblyController::getBreadCrumbTrail();
		}
		
	}

}

?>