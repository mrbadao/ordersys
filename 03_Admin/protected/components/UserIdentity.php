<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	private $_id;
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$c = new CDbCriteria();
		$c->condition = 'loginid=:loginid';
		$c->params = array(':loginid' => $this->username);
		$staff = Staff::model()->find($c);

		if($staff == null){
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		}else
			if ($staff->pasword !== $staff->hashPassword($this->password)){
				$this->errorCode = self::ERROR_PASSWORD_INVALID;
			}else
			{
				$this->_id = $staff->id;
				$this->username = $staff->name;
				$this->errorCode = self::ERROR_NONE;
			}

		return !$this->errorCode;
	}

	public function getId()
	{
		return $this->_id;
	}
}