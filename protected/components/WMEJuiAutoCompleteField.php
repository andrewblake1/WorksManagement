<?php
/* 
 * Autocomplete widget that extends CJuiAutoComplete extension for WM.
 * WMEJuiAutoCompleteFkField class file.
 * 
 * A Blake altered extension EJuiAutoCompleteFkField class file to suit - see that for further documention
 * have removed documentation for brevity
 * EJuiAutoCompleteFkField
 * @author Jeremy Dunn <jeremy.j.dunn@gmail.com>
 * @link http://www.yiiframework.com/
 * @version 1.4
 */

Yii::import('zii.widgets.jui.CJuiAutoComplete');
abstract class WMEJuiAutoCompleteField extends CJuiAutoComplete
{
	/**
	 * @var WMTbActiveFormthe form.
	 */
	public $form;
	/**
	 * @var ActiveRecord the model.
	 */
	public $model;
	/**
	 * @var array any attributes of CJuiAutoComplete and jQuery JUI AutoComplete widget may
	 * also be defined. Read the code and docs for all options.
	 */
	public $options = array('minLength'=>1); 
	/**
	 * @var boolean whether to show the FK field.
	 */
	public $showField = false;
	/**
	 * @var integer length of the FK field if visible
	 */
	public $fieldSize = 10;
	/**
	 * @var string the attribute (or pseudo-attribute) to display from the FK table
	 */
	public $displayAttr;
	/**
	 * @var integer width of the AutoComplete field
	 */
	public $autoCompleteLength = 50;
	/**
	 * @var string the ID of the FK field
	 */
	protected $_fieldID;
 	/**
	 * @var string the ID of the hidden field to save the display value
	 */
	protected $_saveID;
 	/**
	 * @var string the ID of the AutoComplete field
	 */
	protected $_lookupID;
	/**
	 * @var string the initial display value
	 */
	protected $_display;
	/**
	 * @var string the initial display value
	 */
	public $afterSelect = '';

    public function init()
    {
// todo: discover the cause of this weird htmloptions issue where htmloptions resets to an empty array if initialized in the properties intitialization
// area above. Suspect screwy behaviour maybe attributes backup
		/**
		* @var array html options.
		 * NB:
		*/
		$this->htmlOptions += array(
			'class'=>'ui-autocomplete-input',
			'autocomplete'=>'off',
			'role'=>'textbox',
			'aria-autocomplete'=>'list',
			'aria-haspopup'=>'true',
		);
		
		// a bit of a hack here to pick up on ajax in htmloptions which is intended to be after to select and works for
		// drop down lists but doesn't send the selected item text to autotext widget unless in onselect
		if(isset($this->htmlOptions['ajax']))
		{
			$this->afterSelect = CHtml::ajax($this->htmlOptions['ajax']);
			unset($this->htmlOptions['ajax']);
		}
		
        if (!isset($this->options['minLength']))
            $this->options['minLength'] = 2;

        if (!isset($this->options['maxHeight']))
            $this->options['maxHeight']='100';

        $this->htmlOptions['size'] = $this->autoCompleteLength;
        // fix problem with Chrome 10 validating maxLength for the auto-complete field
        $this->htmlOptions['maxLength'] = $this->autoCompleteLength;

        // setup javascript to do the work
		
		// show initial display value
 //       $this->options['create']="js:function(event, ui){\$(this).val('".addslashes($this->_display)."');}";
       // after user picks from list, save the ID in model/attr field, and Value in _save field for redisplay
        $this->options['select']="js:function(event, ui){\$('#".$this->_fieldID."').val(ui.item.id);\$('#".$this->_saveID."').val(ui.item.value);{$this->afterSelect}}";
        // if onblur not set
		if(empty($this->htmlOptions['onblur']))
		{
			// this is either the previous value if user didn't pick anything; or the new value if they did
			$this->htmlOptions['onblur']="
				if($(this).val())
				{
				console.log(2);
					$(this).val($('#".$this->_saveID."').val());
				}
				else
				{
					$('#".$this->_saveID."').val('');
					$('#".$this->_fieldID."').val('');
				console.log(4);
				}
			";
		}

		parent::init(); // ensure necessary assets are loaded
	}
 
    public function run()
    {
         // first render the FK field.  This is the actual data field, populated by autocomplete.select()
        if ($this->showField)
		{
            echo CHtml::activeTextField($this->model, $this->attribute, array('size'=>$this->fieldSize, 'readonly'=>'readonly'));
        }
		else
		{
            echo CHtml::activeHiddenField($this->model, $this->attribute);
        }

        // second, the hidden field used to refresh the display value
        echo CHtml::hiddenField($this->_saveID, $this->_display, array('id'=>$this->_saveID)); 

        // third, the autoComplete field itself
        $this->htmlOptions['id'] = $this->_lookupID;
        $this->htmlOptions['name'] = $this->_lookupID;   
		$this->htmlOptions['value'] = $this->_display;   

 /*       // fouth, an image button to empty all three fields
        // JJD 1/2/13 v1.6 use relNameLabel if present
        $label=Yii::t('DR','Remove '). ucfirst((!empty($this->relNameLabel) ? $this->relNameLabel : $this->relName));
        $deleteImageURL = '/images/text_field_remove.png'; 
 			echo CHtml::link('<i class="'.$button['icon'].'"></i>', $deleteImageURL, array('title'=>$label,
                'name' => 'remove'.$this->_fieldID,
                'style'=>'margin-left:6px;',
                // JJD 4/27/12 #1350 trigger onchange event for display field, in case there's an event attached (e.g. unsaved-changes-warning)
                'onclick'=>"$('#".$this->_fieldID."').val('').trigger('change');$('#".$this->_saveID."').val('');$('#".$this->_lookupID."').val('');",
            ));
       echo CHtml::image($deleteImageURL, $label,
            array('title'=>$label,
                'name' => 'remove'.$this->_fieldID,
                'style'=>'margin-left:6px;',
                // JJD 4/27/12 #1350 trigger onchange event for display field, in case there's an event attached (e.g. unsaved-changes-warning)
                'onclick'=>"$('#".$this->_fieldID."').val('').trigger('change');$('#".$this->_saveID."').val('');$('#".$this->_lookupID."').val('');",
            )
        );*/

        parent::run();
    }
}

?>