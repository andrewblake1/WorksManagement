<?php

/**
 * Dataprovider
 */

class CustomFieldActiveDataProvider extends ActiveDataProvider
{

	public function getData($refresh = false) {
		
		// get data
		$data = parent::getData($refresh);
		
		// if there is any data
		if(empty($data))
		{
			// set global that be used to hide the new button on admin view if this dataprovider for that - which it probably is
			Yii::app()->params['showDownloadButton'] = false;
		}

		return $data;
	}
}

?>
