<?php

class DefaultValueController extends Controller
{
	
	public function accessRules()
	{
		$accessRules = parent::accessRules();
		array_unshift($accessRules,
			array('allow',
				'actions'=>array('dynamicTables', 'dynamicColumns'),
				'roles'=>array($this->modelName),
		));

		return $accessRules;
	}

	public function actionDynamicColumns()
	{
		$tableName = Yii::app()->functions->camelize($_POST['DefaultValue']['table'], true);
		$attributes = $this->getColumns($_POST['DefaultValue']['table']);
		ob_start();
		$model = DefaultValue::model();
		$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));
		ob_end_clean();

		// if more than 20 rows in the lookup table use autotext
		if(count($attributes) > 20)
		{
			$this->widget('WMEJuiAutoCompleteColumn', array(
				'model'=>$model,
				'form'=>$form,
			));
		}
		else
		{
			// add a blank value at the top to be converted to null later if allowing nulls
			echo $form->dropDownListRow(
				'column',
				$attributes,
				array(),
				$model
			);
		}

	}

	protected function getColumns($tableName)
	{
		$attributes = array();

		if(Yii::app()->db->schema->getTable($tableName))
		{
			$tableModel = Yii::app()->functions->camelize(str_replace(Yii::app()->params->tablePrefix, '', $tableName), TRUE);
			$attributes = $tableModel::model()->safeAttributeNames;
			$attributes = array_combine($attributes, $attributes);
		}

		return $attributes;
	}

	// data provider for EJuiAutoCompleteFkField
	public function actionAutocomplete()
	{
		// if something has been entered
		if(isset($_GET['term']))
		{
			$out =array();

			if(isset($_GET['attribute']) && $_GET['attribute'] == 'column')
			{
				foreach($this->getColumns($_GET['table']) as $value)
				{
					if(stripos($value, $_GET['term']) !== false)
					{
						$out[] = array(
							// expression to give the string for the autoComplete drop-down
							'label' => $label = $value,
							'value' => $label, 
							// return value from autocomplete
							'id' => $label, 
						);
					}
				}
			}
			else
			{
				$databaseName = Yii::app()->params['databaseName'];
				$command = Yii::app()->db->createCommand("
					SELECT `TABLE_NAME` as value
					FROM information_schema.tables
					WHERE `TABLE_SCHEMA` = '$databaseName'
					AND `TABLE_NAME` LIKE :param
					LIMIT 20;
				");

				// protect against possible injection
				$command->bindParam(":param", $param = "%{$_GET['term']}%", PDO::PARAM_STR);

				foreach($command->queryAll() as $row)
				{
					$out[] = array(
						// expression to give the string for the autoComplete drop-down
						'label' => $label = $row['value'],  
						'value' => $label, 
						// return value from autocomplete
						'id' => $label, 
					);
				}
			}

			echo CJSON::encode($out);
			Yii::app()->end();
		}
	}

}

?>