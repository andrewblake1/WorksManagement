<?php
class Html extends CHtml
{
	public static function link($text,$url='#',$htmlOptions=array())
	{
		// if the attribute is longer than 30 characters
		if(mb_strlen($text) > 20)
		{
			// add a tooltip with the full text
			$htmlOptions['data-original-title'] = $text;
			// shorten to 20 characters total
			$text = mb_substr($text, 0, 17) . '...';
		}

		if($url!=='')
			$htmlOptions['href']=self::normalizeUrl($url);
		self::clientChange('click',$htmlOptions);
		return self::tag('a',$htmlOptions,$text);
	}
}
