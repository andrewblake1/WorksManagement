<?php

class WMTbFileUploadActiveForm extends WMTbActiveForm
{
	private $_redirectUrl;
	public $enableAjaxValidation = true;
	public $showSubmit = 'hide';
	public $submitOptions = array('class'=>'form-button btn btn-primary btn-large hide');
	public $htmlOptions = array('enctype' => 'multipart/form-data');

    public function init()
    {
		$id = $this->id;
		$redirectParams = $this->controller->getAdminParams();
		// if create
		if($this->model->isNewRecord)
		{
			$redirectParams += array($this->controller->modelName.'_sort' => 'id.desc');
		}

		$this->_redirectUrl = $this->controller->createUrl('admin', $redirectParams);
		
		$modelName = $this->controller->modelName;
		// ensure nav variables set containing path
		$this->model->assertFromParent();

		$this->id = "$modelName-form";
		// this the same as the parent widget except overriding afterValidate because want to trigger jquery form uploads click event handler on the
		// upload button only after the form has been validated CActiveForm. 
		$this->clientOptions = array(
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
					if(typeof data.id != "undefined")
					{
						// add a hidden field to contain the new  id - created - cant use id or will think updating
						// TODO: probaby can use id now??
						$("<input>").attr({
							type: "hidden",
							name: "' . $modelName . '[created]",
							id: "created",
							value: data.id
						}).appendTo("form");
						$("span:contains(\"Create / upload\")").html("Update / upload");
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
						{'.(
							$this->model->isNewRecord
								? 'window.location.href = "' . $this->_redirectUrl . '";'
								: '// allow the CActiveform submit to occur
								return true;'
						).'}
						else
						{
							// submit form via the click function of the upload button
							$("#' . $this->id . ' .fileupload-buttonbar .start").click();
						}
					}

					// dont submit through the normal CActiveform submit
					return false;
				}

				return true;
			}');

		parent::init();
	}
 
    public function run()
    {
		$this->hiddenField('id');

		// if update
		if(!$this->model->isNewRecord)
		{
			// Load existing files:
			Yii::app()->clientScript->registerScript('loadexisting',"
				$('#{$this->id}').each(function () {
					var that = this;
					$.getJSON('" . $this->controller->createUrl('getExisting', array('id'=>$this->model->id)) . "', function (result) {
						if (result) {
							$(that).fileupload('option', 'done')
								.call(that, null, {result: result});
						}
					});
				});
			",CClientScript::POS_READY);
		}
		// otherwise creating
		else
		{
			// when closing the modal
			Yii::app()->clientScript->registerScript('closing',"
				$('#myModal').on('hidden', function ()
				{
					// remove id of anything newly created
					$('#created').remove();
					// reset the button text
					$('span:contains(\"Update / upload\")').html('Create / upload');
				});
			",CClientScript::POS_READY);
		}

		// set call back for when upload process stops
		Yii::app()->clientScript->registerScript('stopcallback',"
			$('#{$this->id}').bind('fileuploadstopped', function (e)
			{
				// allow a redirect to admin view only if there are no upload errors showing
				if (!$('form .files .error').length)
				{
					window.location.href = '{$this->_redirectUrl}';
				}
			});
		",CClientScript::POS_READY);

		$uploadView = 'application.views._uploadView';
		if($this->controller->checkAccess(Controller::accessWrite))
		{
			$formView = $this->model->isNewRecord ? 'application.views._upload' : 'application.views._uploadUpdate';
			$downloadView = 'download';
		}
		else
		{
			$formView = 'application.views._formViewRead';
			$downloadView = 'application.views._downloadViewRead';
		}

		Yii::import( "xupload.models.XUploadForm" );
		$this->widget('xupload.XUpload', array(
			'url' => $this->controller->createUrl("upload"),
			'model' => new XUploadForm,
			'htmlOptions' => array('id'=>$this->id),
			'attribute' => '',
			'multiple' => true,
			'formView' => $formView,
			'uploadView' => $uploadView,
			'downloadView' => $downloadView,
		));

		parent::run();
	}
}

?>