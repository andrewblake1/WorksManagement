<?php
// TODO: not sure if this being used or tested yet - comes from custom value controller - now obsolete
class CustomFieldController extends Controller
{
	// ajax dataprovider for autocomplete fields - only project at the moment - will need to pass somthing else to make customValue
	public function actionAutocomplete()
	{
		$out = array();

		// if something has been entered
		if(isset($_GET['term']) && isset($_GET['custom_field_id']))
		{
			// get the related sql select statement
			$command = Yii::app()->db->createCommand("SELECT validation_text FROM tbl_customField WHERE id = :custom_field_id");
			$command->bindParam(":custom_field_id", $_GET['custom_field_id'], PDO::PARAM_INT);
			$sql = $command->queryScalar();

			// get first and second column names
			$row = Yii::app()->db->createCommand($sql)->queryRow();
			$firstColumnName = each($row);
			$firstColumnName = $firstColumnName[0];
			$secondColumnName = ($secondColumnName = each($row)) ? $secondColumnName[0] : $firstColumnName;

			// query and loop
			$command = Yii::app()->db->createCommand("$sql WHERE `$secondColumnName` LIKE :$secondColumnName ORDER BY `$secondColumnName` ASC LIMIT 20");
			$term = $_GET['term'].'%';
			$command->bindParam(":$secondColumnName", $term, PDO::PARAM_STR);
			foreach($command->queryAll() as $row)
			{
				$out[] = array(
					// expression to give the string for the autoComplete drop-down
					'label' => $row[$secondColumnName],  
					'value' => $row[$secondColumnName],
					// return value from autocomplete
					'id' => $row[$firstColumnName],
				);
			}
		}
		
		echo CJSON::encode($out);
		Yii::app()->end();
	}
	
}

?>