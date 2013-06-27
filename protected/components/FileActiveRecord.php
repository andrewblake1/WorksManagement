<?php
abstract class FileActiveRecord extends ActiveRecord
{
// todo: this to go into trait ImageActiveRecord
	public function expose()
	{
		$modelDir = lcfirst(get_class($this));
		// create a symlink in below doc root to expose to web
// TODO: currently if user goes back into drawing then could get up by previous at command removing at just the wrong time.		
		$session_id = session_id();
		// local source
		$source = Yii::app()->params['privateUploadPath'] . "$modelDir/{$this->id}/"; 
		// target directory
		$target = Yii::app()->params['publicUploadPath'] . "$modelDir/$session_id{$this->id}";
		// create the symlink
		exec("ln -s -f $source $target");
		// set symlink expiry
		exec("echo 'rm $target' | at now + 5 minutes");
		
		// return target url
		return Yii::app()->params['webUploadPath'] . "$modelDir/$session_id{$this->id}/";
	}	
		
	public function exposeSingle($thumbnail = 'thumbnail/')
	{
		// get the first file in that directory
		try
		{
			// local source-
			if(($dir = @opendir(Yii::app()->params['privateUploadPath'] . lcfirst(get_class($this)) . "/{$this->id}/$thumbnail")) !== false)
			{
				while(($file = readdir($dir)) !== false && is_dir($file))
				{
					$a[] = $file;
				}
			}
		}
		catch (Exception $e)
		{
			// loose directory not existing error
		}

		// if file found
		if(!empty($file))
		{
			closedir($dir);  
			// make available temprorarily and get target url
			return $this->expose() . $thumbnail . $file;
		}
	}
	
	public function imageColumn($thumbnail = true)
	{
		return  array(
			'class'=>'WMTbImageColumn',
			'imagePathExpression'=>'$data->exposeSingle(' . ($thumbnail ? '' : 'false') . ')',
			'usePlaceKitten'=>FALSE,
		);
	}
		
}

?>