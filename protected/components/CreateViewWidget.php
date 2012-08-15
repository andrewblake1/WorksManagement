<?php

/**
 * Create view widget
 * @param ActiveRecord $model the model
 */
class CreateViewWidget extends CWidget
{
	private $controller;
	public $model;
	public $models;

	/**
	 * Displays a particular model.
	 */
    public function init()
    {
       // this method is called by CController::beginWidget()
		$this->controller = $this->getController();
	}
 
    public function run()
    {
?>
		<?php $this->controller->beginWidget('bootstrap.widgets.TbModal', array('id'=>'myModal')); ?>

		<div class="modal-body">
			<?php
			
			echo $this->controller->renderPartial('_form',array(
				'model'=>$this->model,
				'models'=>$this->models,
				));
			
			?>
		</div>
 
		<?php $this->controller->endWidget(); ?>		
<?php
	}
}

?>
