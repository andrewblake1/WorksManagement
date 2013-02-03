<?php
class AdjacencyListActiveRecord extends ActiveRecord {

	/*
	 * attribute to be used in breadcrumb trail  - will use models display attribute otherwise
	 */
	public $crumbAttribute = 'description';

}

?>
