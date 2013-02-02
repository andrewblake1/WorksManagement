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

    public function __construct($grid)
	{
		$controller = Yii::app()->getController();

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
}
?>