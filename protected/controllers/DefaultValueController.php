<?php

class DefaultValueController extends Controller
{
	
	// data provider for EJuiAutoCompleteFkField
	public function actionAutocomplete()
	{
		// if something has been entered
		if (isset($_GET['term']))
		{
			$command = Yii::app()->db->createCommand("
				SELECT `TABLE_NAME`, `COLUMN_NAME` 
				FROM information_schema.columns
				WHERE `TABLE_SCHEMA` = 'worksmanagement_dev'
				AND `COLUMN_NAME` LIKE :column_name
				AND `TABLE_NAME` LIKE :table_name
				LIMIT 20;
				");

			// protect against possible injection
			$terms = explode(Yii::app()->params['delimiter']['search'], $_GET['term']);
			$command->bindParam(":table_name", $param1 = (isset($terms[0]) ? $terms[0] : '') . '%', PDO::PARAM_STR);
			$command->bindParam(":column_name", $param2 = (isset($terms[1]) ? $terms[1] : '') . '%', PDO::PARAM_STR);
		
			foreach($command->queryAll() as $row)
			{
				$out[] = array(
					// expression to give the string for the autoComplete drop-down
					'label' => $label = $row['TABLE_NAME'] . Yii::app()->params['delimiter']['display'] . $row['COLUMN_NAME'],  
					'value' => $label, 
					// return value from autocomplete
					'id' => $label, 
				);
			}
			echo CJSON::encode($out);
			Yii::app()->end();
		}
	}

	private function _splitSearchTableColumns()
	{
		if(isset($_POST[$this->modelName]['searchTableColumn']))
		{
			$searchTableColumns = explode(Yii::app()->params['delimiter']['display'], $_POST[$this->modelName]['searchTableColumn']);
			$_POST[$this->modelName]['table'] = $searchTableColumns[0];
			$_POST[$this->modelName]['column'] = $searchTableColumns[1];
		}
	}
	
	public function actionCreate()
	{
		// split this composite value
		$this->_splitSearchTableColumns();
		
		parent::actionCreate();
		
	}

	public function actionUpdate($id)
	{
		// split this composite value
		$this->_splitSearchTableColumns();
		
		parent::actionUpdate($id);
		
	}

}

?>