<?php
/**
 * WMBootButtonColumn class file.
 * @author Andrew Blake <andrew@newzealandfishing.com>
 * @copyright  Copyright &copy; Andrew Blake 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

Yii::import('zii.widgets.grid.CButtonColumn');
Yii::import('bootstrap.widgets.BootButtonColumn');

/**
 * Works management override to remove the view button.
 */
class WMBootButtonColumn extends BootButtonColumn
{
	public $template='{update} {delete}';
}
