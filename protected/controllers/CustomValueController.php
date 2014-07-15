<?php
// TODO: not sure if this being used or tested yet - comes from custom value controller - now obsolete
class CustomValueController extends Controller
{
	// ajax dataprovider for autocomplete fields - only project at the moment - will need to pass somthing else to make customValue
	public function actionAutocomplete()
	{
		$out = array();

		// if something has been entered
		if(isset($_GET['term']) && isset($_GET['custom_field_id']))
		{
			// get the custom field
			$customField = CustomField::model()->findByPk($_GET['custom_field_id']);
			$sql = $customField->validation_text;

			// get first and second column names
			$row = Yii::app()->db->createCommand($sql)->queryRow();
			$firstColumnName = each($row);
			$firstColumnName = $firstColumnName[0];
			$secondColumnName = ($secondColumnName = each($row)) ? $secondColumnName[0] : $firstColumnName;

			// query and loop
			$command = Yii::app()->db->createCommand("$sql WHERE `$secondColumnName` LIKE :second_column_value ORDER BY `$secondColumnName` ASC LIMIT 20");
			$term = $_GET['term'].'%';
			$command->bindParam(":second_column_value", $term);
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