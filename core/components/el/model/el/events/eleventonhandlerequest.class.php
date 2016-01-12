<?php


class elEventOnHandleRequest extends elEventPlugin
{
	public function run()
	{
		if ($this->modx->context->key == 'mgr') {
			return '';
		}

		$ns = $this->modx->getOption('ns', $_REQUEST, 0);
		$action = $this->modx->getOption('action', $_REQUEST, 0);
		$hash = $this->modx->getOption('hash', $_REQUEST, 0);

		if (empty($ns)) {
			return '';
		}

		switch (true) {
			case ($action == 'login'):
				$opts = $this->el->getLock(array('key' => 'link', 'id' => $hash));
				$user = $this->modx->getOption('user', $opts, 0);
				if (empty($user)) {
					return '';
				}

				$properties = $this->modx->getOption('properties', $opts, array());
				$this->el->config = array_merge($properties, $this->el->config);
				$sid = session_id();

				if (!$this->modx->user->isAuthenticated($properties['loginContext'])) {
					if (!$this->el->loginUser(array(
						'id'           => $user,
						'loginContext' => $properties['loginContext'],
						'addContexts'  => $properties['addContexts']
					))
					) {
						$this->modx->log(modX::LOG_LEVEL_ERROR, "[el] Could not login for user: {$user}");
					}
				}

				$this->el->removeLock(array('key' => 'link', 'id' => $hash));
				$this->el->removeLock(array('key' => 'send', 'id' => $sid));
				$this->modx->sendRedirect($this->el->getLink('login', $properties));

				break;
		}

	}

}
