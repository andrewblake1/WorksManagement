<?php

class ScheduleController extends CategoryController
{
	// force refresh of whole tree on move
	public $moveNodeRefresh = 'true';
	// ensure tasks can only be moved in crews, crews in days, and days in project
	public $checkMove =
		',"crrm" : {
			move : {
				check_move : function (m) {
					target = m.o.find(\'[class^="level"]\').first().attr("class").replace("level","");

					if(m.np.is("DIV"))
					{
						destination = 1;
					}
					else
					{
						destination = m.np.find(\'[class^="level"]\').first().attr("class").replace("level","");
					}

					return (target - destination) == 1;
				}
			}
		}';

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return parent::accessRules() + array(
			array('allow',
				'actions'=>array('addDay'),
				'roles'=>array($this->modelName),
			),
		);
	}

	public function actionFetchTree()
	{
		parent::actionFetchTree($_SESSION['actionAdminGet']['Schedule']['project_id']);
	}

	public function actionAddDay()
	{
		// set post variables to simulate coming from a create click in the form
		$_POST[$this->modelName]['description'] = 'fgh';
		
		// do it!
/*		// do it!
		$controller = new DayController('DayController');
		$_SESSION['Project']['value'];
		$controller->actionCreate();*/
		$day = new Day();
		$day->project_id = $_SESSION['Project']['value'];
		DayController::createSaveStatic($day);
		parent::actionCreate();
	}

	protected function newButton()
	{
		echo ' ';
		$this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'New',
			'url'=>$this->createUrl("{$this->modelName}/addDay"),
			'type'=>'primary',
			'size'=>'small', // '', 'large', 'small' or 'mini'
		));
	}

}

?>