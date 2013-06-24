<?php

Yii::import('bootstrap.widgets.TbImageColumn');

class WMTbImageColumn extends TbImageColumn{
	
	/**
	 * Renders the data cell content
	 * @param int $row the row number (zero based)
	 * @param mixed $data teh data associated with the row
	 */
	protected function renderDataCellContent($row, $data)
	{
		$content = $this->emptyText;
		if ($this->imagePathExpression && $imagePath = $this->evaluateExpression($this->imagePathExpression, array('row' => $row, 'data' => $data)))
		{
			$this->imageOptions['src'] = $imagePath;
			$content = CHtml::tag('img', $this->imageOptions);
			$content = CHtml::link($content, str_replace('thumbnail/', '', $imagePath));
		}
		elseif ($this->usePlaceHoldIt && !empty($this->placeHoldItSize))
			$content = CHtml::tag('img', array('src'=>'http://placehold.it/' . $this->placeHoldItSize));
		elseif ($this->usePlaceKitten && !empty($this->placeKittenSize))
			$content = CHtml::tag('img', array('src'=>'http://placekitten.com/' . $this->placeKittenSize));
		echo $content;
	}
}
?>
