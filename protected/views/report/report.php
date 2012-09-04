<?php

	$this->widget('bootstrap.widgets.TbAlert');

	// display the grid
	$this->widget('bootstrap.widgets.TbGridView',array(
		'id'=>'report-grid',
		'type'=>'striped',
		'dataProvider'=>$dataProvider,
	));

?>
