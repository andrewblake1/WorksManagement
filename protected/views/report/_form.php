<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	// set scope to limit to models
	AuthItemController::listWidgetRow($model, $form, 'context', array(), array('tasks'), 'Context');

	$form->textFieldRow('description'); 

	$options['pagination'] = array('pageSize'=>10);
	$options['params'] = array(':report_id'=>$model->id);
	$dataProvider=new CSqlDataProvider('SELECT id, CONCAT(CONCAT("{", description), "}") AS subReport_id FROM subReport WHERE report_id = :report_id', $options);
	$this->widget('bootstrap.widgets.TbGridView',array(
		'id'=>'report-grid',
		'type'=>'striped',
		'dataProvider'=>$dataProvider,
		'columns'=>array('subReport_id::Sub report'),
	));

//	echo '<div class="tinymce">';
//	echo $form->labelEx($model,'contractData');
	// tiny mce for wysiwyg html
	$this->widget('ext.tinymce.ETinyMce', array(
        'model'=>$model,
		'attribute'=>'template_html',
		'editorTemplate'=>'full',
		'useSwitch'=>false,
		));
//	echo $form->error($model,'contractData'); 
//	echo '</div>';

$this->endWidget();

?>