<?php

/**
 * Admin view widget
 * @param ActiveRecord $model the model
 * @param array $columns the table columns to display in the grid view
 */
class AdminViewWidget extends CWidget
{
	private $_controller;
	public $model;
	public $createModel;
	public $columns;

	/**
	 * Displays a particular model.
	 */
    public function init()
    {
       // this method is called by CController::beginWidget()
		$this->_controller = $this->getController();

		parent::init();
	}
 
    public function run()
    {
		// show buttons on row by row basis i.e. do access check on context
		array_unshift($this->columns, $this->_controller->getButtons($this->model));
	
		// add instructions/ warnings errors via Yii::app()->user->setFlash
		// NB: thia won't work on ajax update as in delete hence afterDelete javascript added in WMTbButtonColumn
		$this->_controller->widget('bootstrap.widgets.TbAlert');

		// should we allow bulk delete
		// determine whether form elements should be enabled or disabled by on access rights
		$controllerName = get_class($this->_controller);
		$bulkActions = $controllerName::checkAccess(Controller::accessWrite)
			? array(
				'align'=>'left',
				'actionButtons' => array(
					array(
						'buttonType' => 'link',
						'type' => 'primary',
						'size' => 'small',
						'icon' => 'trash',
						'label' => 'Delete Selected',
						'id' => 'bulk_delete_button_1',
						'url' => array('batchDelete'),
							'align'=>'left',
						'htmlOptions' => array(
							'class'=>'bulk-action',
						),
						'click' => 'js:batchActions',
					),
				),
				// if grid doesn't have a checkbox column type, it will attach
				// one and this configuration will be part of it
				'checkBoxColumnConfig' => array(
					'name' => 'id'
				))
			: array();
//Yii::app()->params['showDeleteSelectedButton'] = TRUE;	
		
		// display the grid
		$this->_controller->widget('WMTbExtendedGridView',array(
			'id'=>$this->_controller->modelName.'-grid',
			'type'=>'striped',
			'dataProvider'=>$this->model->search(),
			'filter'=>$this->model,
			'columns'=>$this->columns,
			'bulkActions' => $bulkActions,
			'ajaxUrl' => Yii::app()->request->getUrl(),
		));

		// as using boostrap modal for create the html for the modal needs to be on
		// the calling page
		$this->_controller->actionCreate('myModal', $this->createModel);
		
		// add css overrides
		$sourceFolder = YiiBase::getPathOfAlias('webroot.css');
		$publishedFile = Yii::app()->assetManager->publish($sourceFolder . '/worksmanagement.css');
		Yii::app()->clientScript->registerCssFile($publishedFile);
		
		parent::run();
		
		?>
		<script type="text/javascript">
			// as a global variable
			var grid_id = "yiisession-grid";

			$(function(){
				// prevent the click event
				$(document).on('click','#yiisession-grid a.bulk-action',function() {
					return false;
				});
			});

			function batchActions(values){
				var url = "http://localhost/WorksManagement/<?php echo $this->_controller->modelName; ?>/batchDelete";
				var ids = new Array();
				if(values.size()>0){
					values.each(function(idx){
						ids.push($(this).val());
					});

					bootbox.confirm("Delete selected?",
						function(confirmed){
							if(confirmed)
							{
								$.ajax({
									type: "POST",
									url: url,
									data: {"ids":ids},
									data_type:'json',
									success: function(resp){
										if(resp.status == "success"){
											if(resp.msg) {
												$('#yw0').html(resp.msg);
											}
											$.fn.yiiGridView.update("<?php echo $this->_controller->modelName; ?>-grid");
										} else {
											alert(resp.msg);
										}
									}
								});
							}
						}
					);

				}
			}
		</script>
		<?php
	}
}

?>