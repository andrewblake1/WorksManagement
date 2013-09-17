<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$primary = true;
	$style = '';
	$model->type = 'Primary';

	if(!$model->isNewRecord)
	{
		// deterime if role is primary or secondary - determined by duration not being set for any of the
		// children
		if(!TaskToLabourResource::model()->findByAttributes(array(
			'labour_resource_data_id'=>$model->labour_resource_data_id,
		), 'duration IS NOT NULL'))
		{
			$primary = false;
			$style = 'style="display: none;"';
			$model->type = 'Secondary';
		}
	}

	// add this widget into a text string
	ob_start();
	$this->widget('bootstrap.widgets.TbButtonGroup', array(
		'type' => 'primary',
		'toggle' => 'radio',
		'size' => 'small',
		'buttons' => array(
			array('label'=>'Primary', 'active'=>$primary),
			array('label'=>'Secondary', 'active'=>!$primary),
		),
	));
	$betweenHtml = ob_get_clean();

	// strangly at this point neither bootstrap nor yii booster have a way of getting the value - nor setting it
	$form->hiddenField('type');
	Yii::app()->clientScript->registerScript('primarySecondary', "
		$('.btn-group .btn').click(function() {
			// whenever a button is clicked, set the hidden helper
			$('#TaskToLabourResource_type').val($(this).text());
			if($(this).text() == 'Secondary')
			{
				// hide the irrelvenat fields
				$('#primary-role').fadeOut('slow');
				$('#dependant-labour_resource_to_supplier_id').fadeOut('slow');
			}
			else
			{
				$('#primary-role').fadeIn('slow');
				$('#dependant-labour_resource_to_supplier_id').fadeIn('slow');
			}
		}); 
		", CClientScript::POS_READY
	);

	LabourResourceToSupplierController::dependantListWidgetRow(
		$model,
		$form,
		'labour_resource_to_supplier_id',
		'LabourResource',
		'labour_resource_id',
		array('class'=>'span3'),
		array('scopeLabourResource'=>array($model->labour_resource_id)),
		'Role',
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

$this->endWidget();

?>