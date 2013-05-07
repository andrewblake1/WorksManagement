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
 

	private function newButton() {
		if (Yii::app()->user->checkAccess($this->_controller->modelName)) {
			echo ' ';
			$this->_controller->widget('bootstrap.widgets.TbButton', array(
				'label' => 'New',
				'icon' => 'plus',
				'url' => '#myModal',
				'type' => 'primary',
				'size' => 'small', // '', 'large', 'small' or 'mini'
				'htmlOptions' => array(
					'data-toggle' => 'modal',
					'onclick' => '$(\'[id^=myModal] input:not([class="hasDatepicker"]):visible:enabled:first, [id^=myModal] textarea:first\').first().focus();',
				),
			));
		}
	}

	public function exportButton() {
		if (Yii::app()->params['showDownloadButton']) {
			echo ' ';
			$this->_controller->widget('bootstrap.widgets.TbButton', array(
				'label' => 'Download Excel',
				'icon' => 'download',
				'url' => $this->_controller->createUrl("{$this->_controller->modelName}/admin", $_GET + array('action' => 'download')),
				'type' => 'primary',
				'size' => 'small', // '', 'large', 'small' or 'mini'
			));
		}
	}

	/**
	 *### .renderButtons()
	 *
	 * @return bool renders all initialized buttons
	 */
	public function renderButtons()
	{
//		if ($this->buttons === array())
//			return false;

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
		$this->exportButton();
		$this->newButton();

		$this->registerClientScript();
	}
}
