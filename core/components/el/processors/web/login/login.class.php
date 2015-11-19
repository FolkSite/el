<?php


class modelLoginProcessor extends modObjectProcessor
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
		$email = $this->getProperty('email');
		if (empty($email)) {
			return $this->el->failure($this->el->lexicon('err_email_ns'));
		}

		$propKey = $this->getProperty('propkey');
		if (empty($propKey)) {
			return $this->el->failure($this->el->lexicon('err_propkey_ns'));
		}

		$properties = $this->el->getProperties($propKey);
		if (empty($properties)) {
			return $this->el->failure($this->el->lexicon('err_properties_ns'));
		}

		$userId = $this->el->getUserId($email);
		if (empty($userId)) {

			$locked = $this->el->addLock(array('key' => 'email', 'id' => session_id()), array('ttlLock' => $properties['ttlLock']));
			if ($locked !== true) {
				return $this->el->failure($this->el->lexicon('err_limit_action'));
			}

			$create = $this->el->createUser(array('email' => $email, 'groups' => $properties['groups']));
			if (!$create) {
				return $this->el->failure($this->el->lexicon('err_create_user'));
			}

			$userId = $this->el->getUserId($email);
		}

		$hash = $this->el->getHash(array('id' => $userId));
		$locked = $this->el->addLock(array(
			'key' => 'link',
			'id' => $hash,
			'user' => $userId,
			'properties' => $properties
		), array('ttlLock' => $properties['ttlLink']));
		if ($locked !== true) {
			return $this->el->failure($this->el->lexicon('err_login_link_send'));
		}

		$linkLogin = $this->el->getLink('login', $properties, array('email' => $email, 'hash' => $hash));
		$linkLogout = $this->el->getLink('logout', $properties);

		if (!$this->el->sendEmail(array(
			'user' => $userId,
			'body' => $this->modx->getChunk($properties['tplLink'], array_merge(
				$this->el->getUserData($userId),
				array(
					'link_login' => $linkLogin,
					'link_logout' => $linkLogout
				)))
		))
		) {
			return $this->el->failure($this->el->lexicon('err_email_send', array('errors' => $this->modx->mail->mailer->errorInfo)));
		}

		$locked = $this->el->addLock(array(
			'key' => 'send',
			'id' => session_id()
		), array('ttlLock' => $properties['ttlLink']));
		if ($locked !== true) {
			return $this->el->failure($this->el->lexicon('err_login_link_send'));
		}

		$array = array(
			'process' => array(
				'id' => '0',
				'type' => 'user',
				'output' => $this->el->processSnippet($properties)
			),
			'properties' => array(
			),
		);

		return $this->success($this->el->lexicon('login_link_send'), $array);
	}
}

return 'modelLoginProcessor';