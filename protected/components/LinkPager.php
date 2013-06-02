<?php

Yii::import('ext.bootstrap.widgets.TbPager');
class LinkPager extends TbPager
{
	public function createPageUrl($page)
	{
		$pagination = $this->getPages();
		
		$params=$pagination->params===null ? $_GET : $pagination->params;
		$params[$pagination->pageVar]=$page+1;

		return $this->getController()->createUrl($pagination->route,$params);
	}

}
?>
