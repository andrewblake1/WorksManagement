<?php

$form=$this->beginWidget('WMTbActiveForm', array(
		'id' => 'StandardDrawing-form',
		'model'=>$model,
		'enableAjaxValidation' => true,
		'showSubmit' => 'hide',
		'submitOptions' => array('class'=>'form-button btn btn-primary btn-large hide'),
		'parent_fk'=>$parent_fk,
		'htmlOptions'=>array('enctype' => 'multipart/form-data'),
		// this the same as the parent widget except overriding afterValidate because want to trigger jquery form uploads click event handler on the
		// upload button only after the form has been validated CActiveForm. 
	    'clientOptions'=> array(
			'validateOnSubmit'=>true,
			'validateOnChange'=>false,
			'afterValidate'=>'js: function(form, data, hasError)
			{
				// If adding/editing multiple models as a result of what appears visually to be a single model form
				// then their are errors returned in the json data object but hasError is false as it hasnt detected errors matching inputs
				// on the form as they dont exist. This function puts those erros into the error block at the top and stops the form being submitted
				var $lis = "";

				// if afterValidate is being told there are no errors what it really means is no form inputs have errors
				if(!hasError)
				{
					// if there are no errors then should have received json object with id of inserted row as using ajax validate to actually insert
					// here because of file upload submit difficulties etc - validation normally on form submit but ajax file upload does special stuff
					// on submit itself hence hacking to get the desireable functionality of CActiveForms javascript and ajax file upload.
					if(data.id !== false)
					{
						// add a hidden field to contain the new standard drawing id - created - cant use id or will think updating
						$("<input>").attr({
							type: "hidden",
							name: "StandardDrawing[created]",
							id: "created",
							value: data.id
						}).appendTo("form");
					}
					else
					{
						// loop thru json object which is 2 dimensional array
						$.each(data, function()
						{
							$.each(this, function(k, v)
							{
								$lis = $lis + "<li>" + v + "</li>";
							});
						});
					}

					// if there are errors with the models but not on the form inputs
					if($lis != "")
					{
						$errorhtml = \'<div id="-form_es_" class="alert alert-block alert-error" style="">\
						<p>Please fix the following input errors:</p><ul>\' + $lis + \'</ul></div>\';

						$("[id*=-form_es_]").replaceWith($errorhtml);
					}
					else
					{
						// allow update without having to upload - this courtesy of plugin author
						var form = $(\'form\').first();
						if (!form.find(\'.files .start\').length)
						{
							// allow the CActiveform submit to occur
							return true;
						}
						else
						{
							// submit form via the click function of the upload button
							$("#StandardDrawing-form .fileupload-buttonbar .start").click();
						}
					}

					// dont submit through the normal CActiveform submit
					return false;
				}

				return true;
			}'),
	));

	$form->textFieldRow('description');
	
	$form->textFieldRow('comment');
	
	// if update
	if(!$model->isNewRecord)
	{
		$form->hiddenField('id');
		?><script>
		$(function () {
			// Load existing files:
			$('#StandardDrawing-form').each(function () {
				var that = this;
				$.getJSON('<?php echo $this->createUrl('getExisting', array('id'=>$model->id)) ?>', function (result) {
					if (result) {
						$(that).fileupload('option', 'done')
							.call(that, null, {result: result});
					}
				});
			});
		}) 	
		</script><?php
	}
	
	?><script>
	$(function () {
		// set call back for when upload process stops
		$('#StandardDrawing-form').bind('fileuploadstopped', function (e)
		{
			// allow a redirect to admin view only if there are no upload errors showing
			if (!$('form .files .error').length)
			{
				window.location.href = '<?php
					// path calculation from CController::redirect
					$url = array('admin', $this->modelName=>$_SESSION['actionAdminGet'][$this->modelName]);
					$route=$url[0];
					echo $this->createUrl($route,array_splice($url,1));
				?>';
			}
		})
	})
	</script><?php


    Yii::import( "xupload.models.XUploadForm" );
	$this->widget('xupload.XUpload', array(
		'url' => $this->createUrl("upload"),
		'model' => new XUploadForm,
        'htmlOptions' => array('id'=>'StandardDrawing-form'),
		'attribute' => '',
		'multiple' => true,
//		'options' => array(
//			'singleFileUploads' => false,
//		),
		'formView' => $model->isNewRecord ? 'application.views.standardDrawing._upload' : 'application.views.standardDrawing._uploadUpdate',
		'uploadView' => 'application.views.standardDrawing.template_upload',

	));
		
$this->endWidget();
?>