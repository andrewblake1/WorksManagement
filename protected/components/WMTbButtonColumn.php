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

	/**
	 * Displays a particular model.
	 */
    public function __construct($grid)
	{
		$controller = Yii::app()->getController();
		
		// set buttons based on access rights
		if($controller->checkAccess(Controller::accessWrite))
		{
			$this->template='{update} {delete}';
		}
		elseif($controller->checkAccess(Controller::accessRead))
		{
 			$this->template='{view}';
		}
		else
		{
			$this->template='';
		}
		
		parent::__construct($grid);
 	}
}
?>