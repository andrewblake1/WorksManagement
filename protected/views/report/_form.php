<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	// set scope to limit to models
	AuthItemController::listWidgetRow($model, $form, 'context', array(), array('tasks'));

	$form->textFieldRow('description'); 

	$options['pagination'] = array('pageSize'=>10);
	$options['params'] = array(':report_id'=>$model->id);
	$dataProvider=new CSqlDataProvider('SELECT CONCAT(CONCAT("[", description), "]") AS sub_report_id FROM sub_report WHERE report_id = :report_id', $options);
	$this->widget('bootstrap.widgets.TbGridView',array(
		'id'=>'report-grid',
		'type'=>'striped',
		'dataProvider'=>$dataProvider,
		'columns'=>array('sub_report_id::Sub report'),

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