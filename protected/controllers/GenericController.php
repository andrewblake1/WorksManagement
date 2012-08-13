<?php

class GenericController extends Controller
{
	// ajax dataprovider for autocomplete fields - only project at the moment - will need to pass somthing else to make generic
	public function actionAutocomplete()
	{
		$out = array();

		// if something has been entered
		if(isset($_GET['term']) && isset($_GET['generic_type_id']))
		{
			// get the related sql select statement
			$command = Yii::app()->db->createCommand("SELECT validation_text FROM generic_type WHERE id = :generic_type_id");
			$command->bindParam(":generic_type_id", $_GET['generic_type_id'], PDO::PARAM_INT);
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
			
/*			// if no rows found
			if(empty($out))
			{
				// see if allowing new values i.e. not refining to list
				$command = Yii::app()->db->createCommand("SELECT allow_new FROM generic_type WHERE id = :generic_type_id");
				$command->bindParam(":generic_type_id", $_GET['generic_type_id'], PDO::PARAM_INT);
				if($command->queryScalar())
				{
					$out[] = array(
						// expression to give the string for the autoComplete drop-down
						'label' => $_GET['term'],  
						'value' => $_GET['term'],
						// return value from autocomplete
						'id' => $_GET['term'],
					);
				}
			}*/
		}
		
		echo CJSON::encode($out);
		Yii::app()->end();
	}
	
}
