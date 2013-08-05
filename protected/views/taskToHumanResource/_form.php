<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$primary = true;
	$style = '';
	$model->type = 'Primary role';

	if(!$model->isNewRecord)
	{
		// deterime if role is primary or secondary - determined by duration not being set for any of the
		// children
		if(!TaskToHumanResource::model()->findByAttributes(array(
			'human_resource_data_id'=>$model->human_resource_data_id,
		), 'duration IS NOT NULL'))
		{
			$primary = false;
			$style = 'style="display: none;"';
			$model->type = 'Secondary role';
		}
	}

	
	// add this widget into a text string
	ob_start();
	$this->widget('bootstrap.widgets.TbButtonGroup', array(
		'type' => 'primary',
		'toggle' => 'radio',
		'size' => 'small',
		'buttons' => array(
			array('label'=>'Primary role', 'active'=>$primary),
			array('label'=>'Secondary role', 'active'=>!$primary),
		),
	));
	$betweenHtml = ob_get_clean();


	// strangly at this point neither bootstrap nor yii booster have a way of getting the value - nor setting it
	$form->hiddenField('type');
	Yii::app()->clientScript->registerScript('primarySecondary', "
		$('.btn-group .btn').click(function() {
			// whenever a button is clicked, set the hidden helper
			$('#TaskToHumanResource_type').val($(this).text());
			if($(this).text() == 'Secondary role')
			{
				// hide the irrelvenat fields
				$('#primary-role').fadeOut('slow');
				$('#dependant-human_resource_to_supplier_id').fadeOut('slow');
			}
			else
			{
				$('#primary-role').fadeIn('slow');
				$('#dependant-human_resource_to_supplier_id').fadeIn('slow');
			}
		}); 
		", CClientScript::POS_READY
	);

	HumanResourceToSupplierController::dependantListWidgetRow(
		$model,
		$form,
		'human_resource_to_supplier_id',
		'HumanResource',
		'human_resource_id',
		array('class'=>'span3'),
		array('scopeHumanResource'=>array($model->human_resource_id === null ? null : $model->human_resource_id)),
		null,
		array(),
		$betweenHtml
	);

	$form->dropDownListRow('level', Planning::$levels);

	
	echo "<div id=\"primary-role\" $style>";
	$form->timepickerRow('durationTemp');

	$form->timepickerRow('estimated_total_duration');

	$form->timepickerRow('start');
	echo '</div>';

	$form->textFieldRow('quantity');

	$form->textFieldRow('estimated_total_quantity');

//	ModeController::listWidgetRow($model, $form, 'mode_id');

$this->endWidget();

?>