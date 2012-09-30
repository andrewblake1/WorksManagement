<?php

class FileAjax extends CFileValidator {

	protected function validateAttribute($object, $attribute) {
		return empty($_POST['ajax']) ? parent::validateAttribute($object, $attribute) : true;
	}

}

?>
