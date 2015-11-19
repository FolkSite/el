<?php

require_once MODX_CORE_PATH . 'model/modx/processors/security/login.class.php';

class modelUserLoginProcessor extends modSecurityLoginProcessor
{
	/**
	 * {@inheritDoc}
	 * @return boolean
	 */
	public function initialize() {

		$id = $this->getProperty('id', null);
		if ($userObject = $this->modx->getObject('modUser', $id)) {
			$this->setProperty('username', $userObject->get('username'));
			$this->setProperty('password', 'password');
		}
		$this->setProperty('login_context', $this->getProperty('loginContext'));
		$this->setProperty('add_contexts', $this->getProperty('addContexts'));
		$this->setProperty('rememberme', $this->getProperty('rememberme', 0));

		return parent::initialize();
	}

	/**
	 * Check if user is not active or blocked
	 * @return bool|null|string
	 */
	public function checkIsBlocked() {
		if (!$this->user->get('active')) {
			$this->user->set('active', 1);
		}

		return parent::checkIsBlocked();
	}

	/** Check user password
	 *
	 * @param $rt
	 * @return bool|null|string
	 */
	public function checkPassword($rt) {
		/* check if plugin authenticated the user */

		return false;
	}

}

return 'modelUserLoginProcessor';