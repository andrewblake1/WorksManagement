<?php
class TraitAdjacencyListWithFileActiveRecord extends AdjacencyListActiveRecord {
// todo: this to go into trait ImageActiveRecord
	public function expose()
	{
		// create a symlink in below doc root to expose to web
// TODO: currently if user goes back into drawing then could get up by previous at command removing at just the wrong time.		
		$session_id = session_id();
		// local source
		$source = Yii::app()->params['privateUploadPath'] . get_class($this) . "/{$this->id}/"; 
		// target directory
		$target = Yii::app()->params['publicUploadPath'] . get_class($this) . "/$session_id{$this->id}";
		// create the symlink
		exec("ln -s -f $source $target");
		// set symlink expiry
		$expire = date("H:i" , time() + 120);
		exec("echo 'rm $target' | at $expire");
		
		// return target url
		return Yii::app()->params['webUploadPath'] . get_class($this) . "/$session_id{$this->id}/";
	}	
		
	public function exposeSingle()
	{
		// local source
		$dir = opendir(Yii::app()->params['privateUploadPath'] . get_class($this) . "/{$this->id}/");  
		// get the first file in that directory
		while(($file = readdir($dir)) !== false && ($file === '.' || $file === '..' || !is_dir($file)));
		// if not found the throw an error
		if($file !== FALSE)
		{
			closedir($dir);  
			// make available temprorarily and get target url
			return $this->expose() . $file;
		}
	}	
		
}

?>
