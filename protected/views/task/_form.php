<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'task-form',
	'enableAjaxValidation' => true,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model, 'description'); ?>
		<?php echo $form->error($model,'description'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'day'); ?>
		<?php echo $form->dropDownList($model, 'day', GxHtml::listDataEx(Day::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'day'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'purchase_orders_id'); ?>
		<?php echo $form->dropDownList($model, 'purchase_orders_id', GxHtml::listDataEx(PurchaseOrders::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'purchase_orders_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'crew_id'); ?>
		<?php echo $form->dropDownList($model, 'crew_id', GxHtml::listDataEx(Crew::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'crew_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'project_id'); ?>
		<?php echo $form->dropDownList($model, 'project_id', GxHtml::listDataEx(Project::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'project_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'client_to_task_type_client_id'); ?>
		<?php echo $form->dropDownList($model, 'client_to_task_type_client_id', GxHtml::listDataEx(ClientToTaskType::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'client_to_task_type_client_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'client_to_task_type_task_type_id'); ?>
		<?php echo $form->dropDownList($model, 'client_to_task_type_task_type_id', GxHtml::listDataEx(ClientToTaskType::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'client_to_task_type_task_type_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'staff_id'); ?>
		<?php echo $form->dropDownList($model, 'staff_id', GxHtml::listDataEx(Staff::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'staff_id'); ?>
		</div><!-- row -->

		<label><?php echo GxHtml::encode($model->getRelationLabel('clientToTaskTypeToDutyTypes')); ?></label>
		<?php echo $form->checkBoxList($model, 'clientToTaskTypeToDutyTypes', GxHtml::encodeEx(GxHtml::listDataEx(ClientToTaskTypeToDutyType::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('materials')); ?></label>
		<?php echo $form->checkBoxList($model, 'materials', GxHtml::encodeEx(GxHtml::listDataEx(Material::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('reschedules')); ?></label>
		<?php echo $form->checkBoxList($model, 'reschedules', GxHtml::encodeEx(GxHtml::listDataEx(Reschedule::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('reschedules1')); ?></label>
		<?php echo $form->checkBoxList($model, 'reschedules1', GxHtml::encodeEx(GxHtml::listDataEx(Reschedule::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('assemblies')); ?></label>
		<?php echo $form->checkBoxList($model, 'assemblies', GxHtml::encodeEx(GxHtml::listDataEx(Assembly::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('taskToGenericTaskTypes')); ?></label>
		<?php echo $form->checkBoxList($model, 'taskToGenericTaskTypes', GxHtml::encodeEx(GxHtml::listDataEx(TaskToGenericTaskType::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('resourceTypes')); ?></label>
		<?php echo $form->checkBoxList($model, 'resourceTypes', GxHtml::encodeEx(GxHtml::listDataEx(ResourceType::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('taskTypes')); ?></label>
		<?php echo $form->checkBoxList($model, 'taskTypes', GxHtml::encodeEx(GxHtml::listDataEx(TaskType::model()->findAllAttributes(null, true)), false, true)); ?>

<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->