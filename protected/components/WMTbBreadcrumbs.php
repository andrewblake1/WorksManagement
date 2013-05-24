<?php
Yii::import('ext.bootstrap.widgets.TbBreadcrumbs');

class WMTbBreadcrumbs extends TbBreadcrumbs
{
	/**
	 * @var boolean whether to encode item labels.
	 */
	public $encodeLabel = false;

	/**
	 *### .run()
	 *
	 * Renders the content of the widget.
	 *
	 * @throws CException
	 */
	public function run()
	{
		// Hide empty breadcrumbs.
		if (empty($this->links))
			return;

		$links = '';

		if (!isset($this->homeLink))
		{
			$content = CHtml::link(Yii::t('zii', 'Home'), Yii::app()->homeUrl);
			$links .= $this->renderItem($content);
		} else if ($this->homeLink !== false)
			$links .= $this->renderItem($this->homeLink);

		$count = count($this->links);
		$counter = 0;
		foreach($this->links as $index => $link)
		{
			if(is_array($link))
			{
				$label = key($link);
				$url = current($link);
			}
			else
			{
				$label = $index;
				$url = $link;
			}
			

			++$counter; // latest is the active one
			if (is_string($label) || is_array($url))
			{
				$content = Html::link($this->encodeLabel ? CHtml::encode($label) : $label, $url);
				$links .= $this->renderItem($content);
			}
			else
			{
				// if the attribute is longer than 30 characters
				if(mb_strlen($url) > 20)
				{
					$url = $this->encodeLabel ? CHtml::encode($url) : $url;
					// shorten to 20 characters total
					$url = CHtml::tag('span',array('data-original-title' => $url), mb_substr($url, 0, 17) . '...');
				}
				$links .= $this->renderItem($url, ($counter === $count));
			}
		}

		echo CHtml::tag('ul', $this->htmlOptions, $links);
	}

	/**
	 *### .renderItem()
	 *
	 * Renders a single breadcrumb item.
	 *
	 * @param string $content the content.
	 * @param boolean $active whether the item is active.
	 * @return string the markup.
	 */
	protected function renderItem($content, $active = false)
	{
		ob_start();
		echo CHtml::openTag('li', $active ? array('class' => 'active') : array());
		echo $content;
		if (!$active) echo $this->separator; 
		echo '</li>';
		return ob_get_clean();
	}
}
