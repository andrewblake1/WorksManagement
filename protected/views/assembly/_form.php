<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk, 'htmlOptions'=>array('enctype' => 'multipart/form-data')));

	$form->fileFieldRow('fileName');

	$form->textFieldRow('description');

	$form->textFieldRow('unit_price');
	
// TODO: technically breaking mvc here i.e. this should be in controller but will either require global or duplication in accessupdate or 
// passing paramtere to actionUpdate in Controller that is only ever used by sub classes that have file uploads
// probably should make this or call a controller method
	// if updating - create a web accessible symlink of this image that lasts until next cron minute
	if(!$model->isNewRecord)
	{
		// see if there is a file uploaded
		if(file_exists(Yii::app()->params['privateUploadPath'] . 'assembly/' . $model->id))
		{
			$sessionId = session_id();
			// local source
			$source = Yii::app()->params['privateUploadPath'] . 'assembly/' . $model->id; 
			// local target
			$target = Yii::app()->params['publicUploadPath'] . 'assembly/' . $sessionId . $model->id;
			// web src
			$src = Yii::app()->baseUrl . Yii::app()->params['webUploadPath'] . 'assembly/' . $sessionId . $model->id;
			// create the symlink
			exec("ln -s -f $source $target");
			// set symlink expiry
			$expire = date("H:i" , time() + 120);
fb("echo 'rm $target' | at $expire");
			exec("echo 'rm $target' | at $expire");
//			$expire = date("i H d m *" , time() + 60);
//			// temporary crontab file name
//			$fileName = "$sessionId.tmp";
//			exec("crontab -l > $fileName");
//			exec("echo '$expire rm $target' >>$fileName");
//			exec("crontab $fileName");
//			exec("rm $fileName");
			
			echo "<img src='$src'>";
		}
		
	}

	
$this->endWidget();

?>