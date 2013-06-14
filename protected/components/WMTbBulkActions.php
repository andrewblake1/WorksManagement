<?php
Yii::import('ext.bootstrap.widgets.TbBulkActions');
class WMTbBulkActions extends TbBulkActions
{
	private $_controller;
	
 	/**
	 * Displays a particular model.
	 */
    public function init()
    {
       // this method is called by CController::beginWidget()
		$this->_controller = Yii::app()->controller;

		parent::init();
	}
 
	/**
	 *### .renderButtons()
	 *
	 * @return bool renders all initialized buttons
	 */
	public function renderButtons()
	{
		echo CHtml::openTag('div', array('id' => $this->getId(), 'style' => 'position:relative', 'class' => $this->align));

		foreach ($this->buttons as $actionButton)
		{
			// ensure there are some records
			if(Yii::app()->params['showDownloadButton'])
			{
				$this->renderButton($actionButton);
			}
		}
	
		echo '<div style="position:absolute;top:0;left:0;height:100%;width:100%;display:block;" class="bulk-actions-blocker"></div>';
		echo '</div>';

		// add in custom admin buttons - into the bulk action area so all buttons together
		$this->_controller->renderAdminButtons();

		$this->registerClientScript();
	}
}
