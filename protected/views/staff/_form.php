<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'staff-form',
	'enableAjaxValidation' => true,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'first_name'); ?>
		<?php echo $form->textField($model, 'first_name', array('maxlength' => 64)); ?>
		<?php echo $form->error($model,'first_name'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'last_name'); ?>
		<?php echo $form->textField($model, 'last_name', array('maxlength' => 64)); ?>
		<?php echo $form->error($model,'last_name'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'phone_mobile'); ?>
		<?php echo $form->textField($model, 'phone_mobile', array('maxlength' => 64)); ?>
		<?php echo $form->error($model,'phone_mobile'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model, 'username', array('maxlength' => 64)); ?>
		<?php echo $form->error($model,'username'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model, 'password', array('maxlength' => 32)); ?>
		<?php echo $form->error($model,'password'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'deleted'); ?>
		<?php echo $form->checkBox($model, 'deleted'); ?>
		<?php echo $form->error($model,'deleted'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'staff_id'); ?>
		<?php echo $form->dropDownList($model, 'staff_id', GxHtml::listDataEx(Staff::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'staff_id'); ?>
		</div><!-- row -->

		<label><?php echo GxHtml::encode($model->getRelationLabel('authAssignments')); ?></label>
		<?php echo $form->checkBoxList($model, 'authAssignments', GxHtml::encodeEx(GxHtml::listDataEx(AuthAssignment::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('authAssignments1')); ?></label>
		<?php echo $form->checkBoxList($model, 'authAssignments1', GxHtml::encodeEx(GxHtml::listDataEx(AuthAssignment::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('authItems')); ?></label>
		<?php echo $form->checkBoxList($model, 'authItems', GxHtml::encodeEx(GxHtml::listDataEx(AuthItem::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('assemblies')); ?></label>
		<?php echo $form->checkBoxList($model, 'assemblies', GxHtml::encodeEx(GxHtml::listDataEx(Assembly::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('clients')); ?></label>
		<?php echo $form->checkBoxList($model, 'clients', GxHtml::encodeEx(GxHtml::listDataEx(Client::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('clientToTaskTypeToDutyTypes')); ?></label>
		<?php echo $form->checkBoxList($model, 'clientToTaskTypeToDutyTypes', GxHtml::encodeEx(GxHtml::listDataEx(ClientToTaskTypeToDutyType::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('crews')); ?></label>
		<?php echo $form->checkBoxList($model, 'crews', GxHtml::encodeEx(GxHtml::listDataEx(Crew::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('crews1')); ?></label>
		<?php echo $form->checkBoxList($model, 'crews1', GxHtml::encodeEx(GxHtml::listDataEx(Crew::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('days')); ?></label>
		<?php echo $form->checkBoxList($model, 'days', GxHtml::encodeEx(GxHtml::listDataEx(Day::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('dutyTypes')); ?></label>
		<?php echo $form->checkBoxList($model, 'dutyTypes', GxHtml::encodeEx(GxHtml::listDataEx(DutyType::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('generics')); ?></label>
		<?php echo $form->checkBoxList($model, 'generics', GxHtml::encodeEx(GxHtml::listDataEx(Generic::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('genericProjectTypes')); ?></label>
		<?php echo $form->checkBoxList($model, 'genericProjectTypes', GxHtml::encodeEx(GxHtml::listDataEx(GenericProjectType::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('genericTaskTypes')); ?></label>
		<?php echo $form->checkBoxList($model, 'genericTaskTypes', GxHtml::encodeEx(GxHtml::listDataEx(GenericTaskType::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('genericTypes')); ?></label>
		<?php echo $form->checkBoxList($model, 'genericTypes', GxHtml::encodeEx(GxHtml::listDataEx(GenericType::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('genericprojectcategories')); ?></label>
		<?php echo $form->checkBoxList($model, 'genericprojectcategories', GxHtml::encodeEx(GxHtml::listDataEx(Genericprojectcategory::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('generictaskcategories')); ?></label>
		<?php echo $form->checkBoxList($model, 'generictaskcategories', GxHtml::encodeEx(GxHtml::listDataEx(Generictaskcategory::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('materials')); ?></label>
		<?php echo $form->checkBoxList($model, 'materials', GxHtml::encodeEx(GxHtml::listDataEx(Material::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('plans')); ?></label>
		<?php echo $form->checkBoxList($model, 'plans', GxHtml::encodeEx(GxHtml::listDataEx(Plan::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('projects')); ?></label>
		<?php echo $form->checkBoxList($model, 'projects', GxHtml::encodeEx(GxHtml::listDataEx(Project::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('purchaseOrders')); ?></label>
		<?php echo $form->checkBoxList($model, 'purchaseOrders', GxHtml::encodeEx(GxHtml::listDataEx(PurchaseOrders::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('reschedules')); ?></label>
		<?php echo $form->checkBoxList($model, 'reschedules', GxHtml::encodeEx(GxHtml::listDataEx(Reschedule::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('staffs')); ?></label>
		<?php echo $form->checkBoxList($model, 'staffs', GxHtml::encodeEx(GxHtml::listDataEx(Staff::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('suppliers')); ?></label>
		<?php echo $form->checkBoxList($model, 'suppliers', GxHtml::encodeEx(GxHtml::listDataEx(Supplier::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('tasks')); ?></label>
		<?php echo $form->checkBoxList($model, 'tasks', GxHtml::encodeEx(GxHtml::listDataEx(Task::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('taskToGenericTaskTypes')); ?></label>
		<?php echo $form->checkBoxList($model, 'taskToGenericTaskTypes', GxHtml::encodeEx(GxHtml::listDataEx(TaskToGenericTaskType::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('taskTypes')); ?></label>
		<?php echo $form->checkBoxList($model, 'taskTypes', GxHtml::encodeEx(GxHtml::listDataEx(TaskType::model()->findAllAttributes(null, true)), false, true)); ?>

<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->