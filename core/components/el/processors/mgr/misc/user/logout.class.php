<?php

require_once MODX_CORE_PATH . 'model/modx/processors/security/logout.class.php';

/**  */
class modelUserLogoutProcessor extends modSecurityLogoutProcessor
{
	public $lifetime;

	/**
	 * {@inheritDoc}
	 * @return boolean
	 */
	public function initialize() {

		$this->setProperty('login_context', $this->getProperty('loginContext'));
		$this->setProperty('add_contexts', $this->getProperty('addContexts'));

		return parent::initialize();
	}

	public function process() {
		if (!$this->modx->user->isAuthenticated($this->loginContext)) {
			return $this->failure($this->modx->lexicon('not_logged_in'));
		}

		$this->fireBeforeLogoutEvent();
		$this->removeSessionContexts();
		$this->fireAfterLogoutEvent();

		return $this->success();
	}

}

return 'modelUserLogoutProcessor';