<?php

/**
 * Dataprovider
 */

class TaskActiveDataProvider extends ActiveDataProvider
{

	public function getData($refresh = false) {
		
		// get data
		$data = parent::getData($refresh);
		
		// remove the temporary table - safer to recalc the name -- prevent accidentally dropping table in future possible use of ative data provider where _tablename not set
// todo: switch to prepared statment
		$tableName = Yii::app()->params['tempTablePrefix'] . Yii::app()->user->id;
		Yii::app()->db->createCommand("DROP TABLE IF EXITS `$tableName`")->execute();

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
