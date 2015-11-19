<?php


class modelLogoutProcessor extends modObjectProcessor
{
	/** @var el $el */
	public $el;

	/** {@inheritDoc} */
	public function initialize()
	{
		/** @var el $el */
		$this->el = $this->modx->getService('el');
		$this->el->initialize($this->getProperty('context', $this->modx->context->key));

		return parent::initialize();
	}

	/** {@inheritDoc} */
	public function process()
	{
		$propKey = $this->getProperty('propkey');
		if (empty($propKey)) {
			return $this->el->failure($this->el->lexicon('err_propkey_ns'));
		}

		$properties = $this->el->getProperties($propKey);
		if (empty($properties)) {
			return $this->el->failure($this->el->lexicon('err_properties_ns'));
		}

		if (!$this->el->logoutUser(array(
			'id' => $this->modx->user->id,
			'loginContext' => $properties['loginContext'],
			'addContexts' => $properties['addContexts']
		))) {
			$this->modx->log(modX::LOG_LEVEL_ERROR, "[el] Could not logout for user: {$this->modx->user->id}");
		}

		$linkLogin = $this->el->getLink('login', $properties);
		$linkLogout = $this->el->getLink('logout', $properties);

		$array = array(
			'process' => array(
				'id' => $this->modx->user->id,
				'type' => 'user',
				'output' => $this->el->processSnippet(array_merge($properties, array('id' => 0)))
			),
			'properties' => array(
				'link_login' => $linkLogin,
				'link_logout' => $linkLogout,
			),
		);

		return $this->success($this->el->lexicon('logout_success'), $array);
	}
}

return 'modelLogoutProcessor';