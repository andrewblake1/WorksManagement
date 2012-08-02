<?php

/**
 * Autocomplete widget that extends EJuiAutoCompleteFkField extension for WM.
 * WMEJuiAutoCompleteFkField class file.
 * @author Andrew Blake <andrew@newzealandfishing.com>
 * @copyright  Copyright &copy; Andrew Blake 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 *
 * @param WMBootActiveForm $form the form
 * @param ActiveRecord $model the model
 * @param string $relName the relation name
 * @param string $fkField the foreign key field
 * @param array $displayAttr the columns to display
 * @param int $autoCompleteLength the length of the AutoComplete/display field, defaults to 50
 * @param bool $showFKField set 'true' to display the FK field with 'readonly' attribute
 * @param int $FKFieldSize display size of the FK field. Only matters if not hidden. Defaults to 10
 * @param array $htmlOptions html options
 * @param array $options any attributes of CJuiAutoComplete and jQuery JUI AutoComplete widget may
 *		also be defined. Read the code and docs for all options
 */

class WMEJuiAutoCompleteFkField extends EJuiAutoCompleteFkField
{
	public $form;

	public $model;
	public $relName;
	public $fkField;
	public $displayAttr=NULL;
	public $autoCompleteLength = 50;
	public $showFKField = false;
	public $FKFieldSize = 10;
	public $htmlOptions = array();
	public $options = array('minLength'=>1); 
	
	/**
	 * Displays a particular model.
	 */
    public function init()
    {
        // this method is called by CController::beginWidget()
		parent::init();
		echo $this->form->labelEx($this->model,$this->fkField);
	}
 
    public function run()
    {
        // this method is called by CController::endWidget()
		parent::run();
		echo $this->form->error($this->model,$this->fkField);
    }
}

?>
