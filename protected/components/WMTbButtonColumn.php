<?php
/**
 * WMTbButtonColumn class file.
 * @author Andrew Blake <andrew@newzealandfishing.com>
 * @copyright  Copyright &copy; Andrew Blake 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

Yii::import('zii.widgets.grid.CButtonColumn');
Yii::import('bootstrap.widgets.TbButtonColumn');

/**
 * Works management override to remove the view button.
 */
class WMTbButtonColumn extends TbButtonColumn
{
	public $template;
	protected $controller;

    public function __construct($grid)
	{
		$this->controller = Yii::app()->getController();

		// TODO: this id needs fixing
		$this->afterDelete='function(link,success,data) {
			if(success)
			{
				$("#yw0").html(data);
			}
		}';
		
		
		// set buttons based on access rights
//		if($controller->checkAccess(Controller::accessWrite))
//		{
			$this->template='{view}{update}{delete}';

	//	}
//		elseif($controller->checkAccess(Controller::accessRead))
	//	{
 //			$this->template='';
	//	}
//		else
//		{
//			$this->template='';
//		}
		
		
		
//		$this->cssClassExpression = 'test';
		parent::__construct($grid);
 	}
	
	/**
	 *### .renderButton()
	 *
	 * Renders a link button.
	 *
	 * @param string $id the ID of the button
	 * @param array $button the button configuration which may contain 'label', 'url', 'imageUrl' and 'options' elements.
	 * @param integer $row the row number (zero-based)
	 * @param mixed $data the data object associated with the row
	 */
	protected function renderButton($id, $button, $row, $data)
	{
		if (isset($button['visible']) && !$this->evaluateExpression($button['visible'], array('row'=>$row, 'data'=>$data)))
			return;

		$label = isset($button['label']) ? $button['label'] : $id;
		$url = isset($button['url']) ? $this->evaluateExpression($button['url'], array('data'=>$data, 'row'=>$row)) : '#';
		$options = isset($button['options']) ? $button['options'] : array();

/*		if (!isset($options['title']))
			$options['title'] = $label;

		if (!isset($options['rel']))
			$options['rel'] = 'tooltip';*/

		if (isset($button['icon']))
		{
			if (strpos($button['icon'], 'icon') === false)
				$button['icon'] = 'icon-'.implode(' icon-', explode(' ', $button['icon']));

			echo CHtml::link('<i class="'.$button['icon'].'"></i>', $url, $options);
		}
		else if (isset($button['imageUrl']) && is_string($button['imageUrl']))
			echo CHtml::link(CHtml::image($button['imageUrl'], $label), $url, $options);
		else
			echo CHtml::link($label, $url, $options);
	}
	
}
?>