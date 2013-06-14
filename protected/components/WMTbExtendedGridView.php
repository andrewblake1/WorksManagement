<?php
Yii::import('ext.bootstrap.widgets.TbExtendedGridView');
class WMTbExtendedGridView extends TbExtendedGridView
{
	private $_controller;

	/**
	 * Displays a particular model.
	 */
    public function init()
    {
 		$this->_controller = $this->getController();
		
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

	/**
	 *### .renderTableFooter()
	 *
	 * Renders the table footer.
	 */
	public function renderTableFooter()
	{
		$hasFilter = $this->filter !== null && $this->filterPosition === self::FILTER_POS_FOOTER;

		$hasFooter = $this->getHasFooter();
		if ($this->bulk !== null || $hasFilter || $hasFooter || Yii::app()->params['showDownloadButton'])
		{
			echo "<tfoot>\n";
			if ($hasFooter)
			{
				echo "<tr>\n";
				/** @var $column CDataColumn */
				foreach ($this->columns as $column)
					$column->renderFooterCell();
				echo "</tr>\n";
			}
			if ($hasFilter)
				$this->renderFilter();

			if ($this->bulk !== null)
				$this->renderBulkActions();
			elseif(Yii::app()->params['showDownloadButton'])// if there are records that can be downloaded
			{
				echo '<tr><td colspan="' . count($this->columns) . '">';
				echo CHtml::openTag('div', array('id' => $this->getId(), 'style' => 'position:relative', 'class' => 'left'));
				// add in custom admin buttons - into the bulk action area so all buttons together
				$this->_controller->exportButton();
				echo '</td></tr>';
			}

			echo "</tfoot>\n";
		}
	}
 
}
