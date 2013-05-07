<?php
Yii::import('ext.bootstrap.widgets.TbExtendedGridView');
class WMTbExtendedGridView extends TbExtendedGridView
{
	/**
	 * Displays a particular model.
	 */
    public function init()
    {
		if (preg_match('/extendedsummary/i', $this->template) && !empty($this->extendedSummary) && isset($this->extendedSummary['columns']))
		{
			$this->template .= "\n{extendedSummaryContent}";
			$this->displayExtendedSummary = true;
		}
		if (!empty($this->chartOptions) && @$this->chartOptions['data'] && $this->dataProvider->getItemCount())
			$this->displayChart = true;
		if ($this->bulkActions !== array()/* && isset($this->bulkActions['actionButtons'])*/)
		{
			if (!isset($this->bulkActions['class']))
				$this->bulkActions['class'] = 'WMTbBulkActions';

			$this->bulk = Yii::createComponent($this->bulkActions, $this);
			$this->bulk->init();
		}
		parent::init();
	}
 
}
