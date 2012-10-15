<?php

$form=$this->beginWidget('WMTbActiveForm', array(
		'id' => 'StandarDrawing-form',
		'model'=>$model,
		'enableAjaxValidation' => false,
		'showSubmit' => false,
		'parent_fk'=>$parent_fk,
		'htmlOptions'=>array('enctype' => 'multipart/form-data'))
	);

	$form->textFieldRow('description');
	
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
						if (result && result.length) {
							$(that).fileupload('option', 'done')
								.call(that, null, {result: result});
						}
					});
				});
		}) 	
		</script><?php
	}
	// else create
	else
	{
		// set redirect
		?><script>
		$('#StandardDrawing-form')
			.bind('fileuploadstop', function (e, data) {
				window.location.href = '<?php echo Yii::app()->request->requestUri; ?>';
			})
		</script><?php
	}

    Yii::import( "xupload.models.XUploadForm" );
	$this->widget('xupload.XUpload', array(
		'url' => $this->createUrl("upload"),
		'model' => new XUploadForm,
        'htmlOptions' => array('id'=>'StandardDrawing-form'),
		'attribute' => 'file',
		'multiple' => true,
		'formView' => 'application.views.standardDrawing._upload',
	));
		
/*
// TODO: technically breaking mvc here i.e. this should be in controller but will either require global or duplication in actionUpdate or 
// passing paramtere to actionUpdate in Controller that is only ever used by sub classes that have file uploads
// probably should make this or call a controller method
	// if updating - create a web accessible symlink of this image that lasts until next cron minute
	if(!$model->isNewRecord)
	{
		// see if there is a file uploaded
		if(file_exists(Yii::app()->params['privateUploadPath'] . 'standard_drawing/' . $model->id))
		{
			$sessionId = session_id();
			// local source
			$source = Yii::app()->params['privateUploadPath'] . 'standard_drawing/' . $model->id; 
			// local target
			$target = Yii::app()->params['publicUploadPath'] . 'standard_drawing/' . $sessionId . $model->id;
			// web src
			$src = Yii::app()->baseUrl . Yii::app()->params['webUploadPath'] . 'standard_drawing/' . $sessionId . $model->id;
			// create the symlink
			exec("ln -s -f $source $target");
			// set symlink expiry
			$expire = date("H:i" , time() + 120);
			exec("echo 'rm -rf $target' | at $expire");

//			$expire = date("i H d m *" , time() + 60);
//			// temporary crontab file name
//			$fileName = "$sessionId.tmp";
//			exec("crontab -l > $fileName");
//			exec("echo '$expire rm $target' >>$fileName");
//			exec("crontab $fileName");
//			exec("rm $fileName");
			
			echo "<img src='$src'>";
		}
		
	}*/

	
$this->endWidget();

?>