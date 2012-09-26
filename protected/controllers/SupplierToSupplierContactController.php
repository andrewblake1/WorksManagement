<?php

class SupplierToSupplierContactController extends Controller
{
	/*
	 * overidden as mulitple models
	 */
	protected function createSave($model, &$models=array())
	{
	
		$supplierContact = new SupplierContact;
		$supplierContact->attributes = $_POST['SupplierContact'];
		if($saved = parent::createSave($supplierContact, $models))
		{
			$model->supplier_contact_id = $supplierContact->id;
			$saved &= parent::createSave($model, $models);
		}

		return $saved;
	}

	/*
	 * overidden as mulitple models
	 */
	protected function updateSave($model,  &$models=array())
	{
		$supplierContact = $model->supplierContact;
		$supplierContact->attributes = $_POST['SupplierContact'];
		if($saved = parent::updateSave($supplierContact, $models))
		{
			$saved &= parent::updateSave($model, $models);
		}

		return $saved;
	}
}

?>