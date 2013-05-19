<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	/**
	 * @var int the employee id on successful login.
	 */
	private $_id;
	
	/**
	 * Authenticates a user.
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$criteria=new CDbCriteria;
		$criteria->compare('contact.email', $this->username);

		if(!$user = User::model()->with('contact')->find($criteria))
		{
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		}
		elseif($user->password != md5($this->password))
		{
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		}
		else
		{
			$this->errorCode=self::ERROR_NONE;
			$this->_id = $user->id;
		}
		
		return !$this->errorCode;
	}

	/**
	 * @return the logged in employee id
	 */
	public function getId()
	{
		return $this->_id;
	}
	
}
?>