<?php

class ProjectToGenericProjectTypeController extends Controller
{
	/**
	 * @var string the name of the model to use in the admin view - the model may serve a database view as opposed to a table  
	 */
	protected $_adminViewModel = 'ViewProjectToGenericProjectType';

	/*
	 * overidden as mulitple models i.e. nothing to save in this model as this model can either be deleted or created as the data item resides in generic
	 */
	protected function updateSave($model, $models)
	{
		$generic = $model->generic;

		// massive assignement
		$generic->attributes=$_POST['Generic'][$generic->id];

		// set Generic custom validators as per the associated generic type
		$generic->setCustomValidators($model->genericProjectType->genericType,
			array(
				'relation_modelToGenericModelType'=>'projectToGenericProjectType',
				'relation_genericModelType'=>'genericProjectType',

			)
		);

		// attempt to save
		return $generic->dbCallback('save');
	}

}
?>