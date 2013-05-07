<?php
Yii::import('bootstrap.widgets.TbTabs');

class WMTbTabs extends TbTabs
{
	protected function normalizeTabs($tabs, &$panes, &$i = 0)
	{
		$id = $this->getId();
		$items = array();

		foreach ($tabs as $tab)
		{
			$item = $tab;
			
			// if the attribute is longer than 20 characters
			if(mb_strlen($item['label']) > 20)
			{
				$item['label'] = $this->encodeLabel ? CHtml::encode($item['label']) : $item['label'];
				$item['itemOptions']['data-original-title'] = $item['label'];
				$item['label'] = mb_substr($item['label'], 0, 17) . '...';
			}

			if (isset($item['visible']) && $item['visible'] === false)
				continue;

			if (!isset($item['itemOptions']))
				$item['itemOptions'] = array();

			if (!isset($item['url']))
				$item['linkOptions']['data-toggle'] = 'tab';

			if (isset($tab['items']))
				$item['items'] = $this->normalizeTabs($item['items'], $panes, $i);
			else
			{
				if (!isset($item['id']))
					$item['id'] = $id.'_tab_'.($i + 1);

				if (!isset($item['url']))
					$item['url'] = '#'.$item['id'];

				if (!isset($item['content']))
					$item['content'] = '';

				$content = $item['content'];
				unset($item['content']);

				if (!isset($item['paneOptions']))
					$item['paneOptions'] = array();

				$paneOptions = $item['paneOptions'];
				unset($item['paneOptions']);

				$paneOptions['id'] = $item['id'];

				$classes = array('tab-pane fade');

				if (isset($item['active']) && $item['active'])
					$classes[] = 'active in';

				$classes = implode(' ', $classes);
				if (isset($paneOptions['class']))
					$paneOptions['class'] .= ' '.$classes;
				else
					$paneOptions['class'] = $classes;

				$panes[] = CHtml::tag('div', $paneOptions, $content);

				$i++; // increment the tab-index
			}

			$items[] = $item;
		}
		return $items;
	}
}
