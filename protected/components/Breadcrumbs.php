<?php
/**
 * Extend to cope with same labels
 */
class Breadcrumbs extends WMTbBreadcrumbs
{

	/**
	 * Renders the content of the portlet.
	 */
	public function run()
	{
		echo CHtml::openTag($this->tagName,$this->htmlOptions)."\n";
		$links=array();
		if($this->homeLink===null)
			$links[]=CHtml::link(Yii::t('zii','Home'),Yii::app()->homeUrl);
		elseif($this->homeLink!==false)
			$links[]=$this->homeLink;
		
		foreach($this->links as $link)
		{
			$label = key($link);
			$value = current($link);
			if(is_string($label) || is_array($url))
				$links[]=strtr($this->activeLinkTemplate,array(
					'{url}'=>CHtml::normalizeUrl($url),
					'{label}'=>$this->encodeLabel ? CHtml::encode($label) : $label,
				));
			else
				$links[]=str_replace('{label}',$this->encodeLabel ? CHtml::encode($url) : $url,$this->inactiveLinkTemplate);
		}
		echo implode($this->separator,$links);
		echo CHtml::closeTag($this->tagName);
	}
}