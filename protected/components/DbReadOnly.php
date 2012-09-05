<?php 
 
class DbReadOnly extends CApplicationComponent
{

	public $connectionString = ''; //override in config/mode_<mode>.php
	public $emulatePrepare = true;
	public $username = '';			//override in config/mode_<mode>.php
	public $password = '';			//override in config/mode_<mode>.php
	public $charset = 'utf8';
	public $connection = null;

    public function init()
    {
		$this->connection=new CDbConnection($this->connectionString,$this->username,$this->password);
		// establish connection. You may try...catch possible exceptions
		$this->connection->active=true;
		$this->connection->charset = $this->charset;
		$this->connection->emulatePrepare = $this->emulatePrepare;
		$this->attachBehavior('connection',$this->connection);
    }
 
}