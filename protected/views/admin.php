<?php 

$this->widget('AdminViewWidget',array(
	'model'=>$model,
	'columns'=>$model->adminColumns,
));

?>