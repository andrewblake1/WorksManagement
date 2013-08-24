<?php

class DbConnection extends CDbConnection
{
	public function createCommand($query=null)
	{
		$this->setActive(true);
		return new DbCommand($this,$query);
	}
}