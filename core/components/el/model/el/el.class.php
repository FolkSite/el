<?php

/**
 * The base class for el.
 */
class el
{

	/* @var modX $modx */
	public $modx;
	/** @var string $namespace */
	public $namespace = 'el';
	/* @var array The array of config */
	public $config = array();

	/** @var array $initialized */
	public $initialized = array();

	/**
	 * @param modX  $modx
	 * @param array $config
	 */
	function __construct(modX &$modx, array $config = array())
	{
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('el_core_path', $config, $this->modx->getOption('core_path') . 'components/el/');
		$assetsUrl = $this->modx->getOption('el_assets_url', $config, $this->modx->getOption('assets_url') . 'components/el/');
		$connectorUrl = $assetsUrl . 'connector.php';

		$this->config = array_merge(array(
			'assetsBasePath'  => MODX_ASSETS_PATH,
			'assetsBaseUrl'   => MODX_ASSETS_URL,
			'assetsUrl'       => $assetsUrl,
			'cssUrl'          => $assetsUrl . 'css/',
			'jsUrl'           => $assetsUrl . 'js/',
			'imagesUrl'       => $assetsUrl . 'images/',
			'connectorUrl'    => $connectorUrl,
			'actionUrl'       => $assetsUrl . 'action.php',

			'corePath'        => $corePath,
			'modelPath'       => $corePath . 'model/',
			'chunksPath'      => $corePath . 'elements/chunks/',
			'templatesPath'   => $corePath . 'elements/templates/',
			'chunkSuffix'     => '.chunk.tpl',
			'snippetsPath'    => $corePath . 'elements/snippets/',
			'processorsPath'  => $corePath . 'processors/',

			'prepareResponse' => true,
			'jsonResponse'    => true,

		), $config);

		$this->modx->addPackage('el', $this->config['modelPath']);
		$this->modx->lexicon->load('el:default');
		$this->namespace = $this->getOption('namespace', $config, 'el');
	}

	/**
	 * @param       $n
	 * @param array $p
	 */
	public function __call($n, array$p)
	{
		echo __METHOD__ . ' says: ' . $n;
	}

	/**
	 * @param       $key
	 * @param array $config
	 * @param null  $default
	 *
	 * @return mixed|null
	 */
	public function getOption($key, $config = array(), $default = null)
	{
		$option = $default;
		if (!empty($key) AND is_string($key)) {
			if ($config != null AND array_key_exists($key, $config)) {
				$option = $config[$key];
			} elseif (array_key_exists($key, $this->config)) {
				$option = $this->config[$key];
			} elseif (array_key_exists("{$this->namespace}_{$key}", $this->modx->config)) {
				$option = $this->modx->getOption("{$this->namespace}_{$key}");
			}
		}

		return $option;
	}

	/**
	 * Initializes component into different contexts.
	 *
	 * @param string $ctx The context to load. Defaults to web.
	 * @param array  $scriptProperties
	 *
	 * @return boolean
	 */
	public function initialize($ctx = 'web', $scriptProperties = array())
	{
		$this->config = array_merge($this->config, $scriptProperties);
		$this->config['ctx'] = $ctx;

		if (!empty($this->initialized[$ctx])) {
			return true;
		}

		switch ($ctx) {
			case 'mgr':
				break;
			default:
				if (!defined('MODX_API_MODE') OR !MODX_API_MODE) {
					$config = $this->modx->toJSON(array(
						'assetsBaseUrl' => $this->config['assetsBaseUrl'],
						'assetsUrl'     => $this->config['assetsUrl'],
						'actionUrl'     => $this->config['actionUrl'],
						'defaults'      => array(
							'yes'      => $this->lexicon('yes'),
							'no'       => $this->lexicon('no'),
							'message'  => array(
								'title' => array(
									'success' => $this->lexicon('title_ms_success'),
									'error'   => $this->lexicon('title_ms_error'),
									'info'    => $this->lexicon('title_ms_info'),
								),
							),
							'confirm'  => array(
								'title' => array(
									'success' => $this->lexicon('title_cms_success'),
									'error'   => $this->lexicon('title_cms_error'),
									'info'    => $this->lexicon('title_cms_info'),
								)
							),
							'selector' => array(
								'view' => $this->getOption('selector_view', null, '.el-view')
							)
						)
					));
					$script = "<script type=\"text/javascript\">elConfig={$config}</script>";
					if (!isset($this->modx->jscripts[$script])) {
						$this->modx->regClientStartupScript($script, true);
					}
					$this->initialized[$ctx] = true;
				}
				break;
		}

		return true;
	}

	/**
	 * return lexicon message if possibly
	 *
	 * @param       $message
	 * @param array $placeholders
	 *
	 * @return string
	 */
	public function lexicon($message, $placeholders = array())
	{
		$key = '';
		if ($this->modx->lexicon->exists($message)) {
			$key = $message;
		} elseif ($this->modx->lexicon->exists($this->namespace . '_' . $message)) {
			$key = $this->namespace . '_' . $message;
		}
		if ($key !== '') {
			$message = $this->modx->lexicon->process($key, $placeholders);
		}

		return $message;
	}

	/** @inheritdoc} */
	public function prepareData(array $data = array())
	{
		while (list($key, $val) = each($data)) {
			$keyMethod = 'format' . ucfirst(str_replace('_', '', $key));
			if (!method_exists($this, $keyMethod)) {
				continue;
			}
			$data[$key] = $this->$keyMethod($val);
		}

		return $data;
	}

	/** @inheritdoc} */
	public function validateData(array $data = array())
	{
		while (list($key, $val) = each($data)) {
			$keyMethod = 'validate' . ucfirst(str_replace('_', '', $key));
			if (!method_exists($this, $keyMethod)) {
				continue;
			}
			$data[$key] = $this->$keyMethod($val);
		}

		return $data;
	}

	/** @inheritdoc} */
	public function getUserId($email = '')
	{
		$q = $this->modx->newQuery('modUserProfile');
		$q->where(array('email' => $email));
		$q->limit(1);
		$q->select('internalKey');
		if ($q->prepare() AND $q->stmt->execute() AND $id = $q->stmt->fetchAll(PDO::FETCH_COLUMN)) {
			$id = end($id);
		} else {
			$id = 0;
		}

		return $id;
	}

	/** @inheritdoc} */
	public function getUserData($id = 0, $format = true)
	{
		$data = array();
		if ($userObject = $this->modx->getObject('modUser', $id)) {
			$data = $this->processObject($userObject, $format);
		}
		if ($userObject AND $profileObject = $userObject->getOne('Profile')) {
			$data = array_merge($this->processObject($profileObject, $format, $data));
		}

		return $data;
	}

	/** @inheritdoc} */
	public function createUser(array $data = array())
	{
		$data['username'] = $this->getOption('username', $data, $data['email']);
		$data['active'] = $this->getOption('active', $data, true);
		$data['blocked'] = $this->getOption('blocked', $data, false);
		$data['groups'] = $this->getOption('groups', $data, '');

		$response = $this->runProcessor('mgr/misc/user/create', $data, $json = false, $this->config['corePath'] . 'processors/');

		return !empty($response['success']);
	}

	/** @inheritdoc} */
	public function loginUser(array $data = array())
	{
		$data['loginContext'] = $this->getOption('loginContext', $data, $this->modx->context->get('key'));
		$data['addContexts'] = $this->getOption('addContexts', $data, array());

		$response = $this->runProcessor('mgr/misc/user/login', $data, $json = false, $this->config['corePath'] . 'processors/');

		return !empty($response['success']);
	}

	/** @inheritdoc} */
	public function logoutUser(array $data = array())
	{
		$data['loginContext'] = $this->getOption('loginContext', $data, $this->modx->context->get('key'));
		$data['addContexts'] = $this->getOption('addContexts', $data, array());

		$response = $this->runProcessor('mgr/misc/user/logout', $data, $json = false, $this->config['corePath'] . 'processors/');

		return !empty($response['success']);
	}

	/** @inheritdoc} */
	public function getHash(array $data = array())
	{
		$hashValues = array();
		$fields = array('id', 'user', 'email');

		foreach ($fields as $field) {
			$hashValues[] = isset($data[$field]) ? $data[$field] : '';
		}

		$hashValues[] = strftime($this->getOption('hash_date', null, '%d'), time());
		$hashValues[] = $this->getOption('hash_salt', null, '');

		return md5(implode('#', $hashValues));
	}

	/** @inheritdoc} */
	public function getLink($type = 'login', array $data = array(), array $args = array())
	{
		if (!empty($args)) {
			$args = array_merge(array(
				'ns'     => 'el',
				'action' => $type,
			), $args);
		}

		switch (true) {
			case $type == 'login' AND !empty($data['resourceLogin']):
				$link = $this->modx->makeUrl($data['resourceLogin'], '', $args, 'full', array('xhtml_urls' => false));
				break;
			case $type == 'logout' AND !empty($data['resourceLogout']):
				$link = $this->modx->makeUrl($data['resourceLogout'], '', $args, 'full', array('xhtml_urls' => false));
				break;
			default:
				$link = '';
				break;
		}

		return $link;
	}

	/** @inheritdoc} */
	public function sendEmail(array $data = array())
	{
		$user = $this->getOption('user', $data, null);
		$email = $this->getOption('email', $data, null);
		$subject = $this->getOption('subject', $data, $this->lexicon('login_email_subject'));
		$body = $this->getOption('body', $data, null);
		$sender = $this->getOption('sender', $data, $this->modx->getOption('emailsender'));
		$from = $this->getOption('from', $data, $this->modx->getOption('emailsender'));

		switch (true) {
			case is_null($user) AND is_null($email):
				return false;
			case is_null($subject) OR is_null($body):
				return false;
			case is_null($email):
				$q = $this->modx->newQuery('modUser');
				$q->innerJoin('modUserProfile', 'modUserProfile', 'modUser.id = modUserProfile.internalKey');
				$q->where(array('modUser.id' => $user, 'modUser.active' => 1, 'modUserProfile.blocked' => 0));
				$q->select('modUserProfile.email');
				if ($q->prepare() AND $q->stmt->execute() AND $email = $q->stmt->fetchAll(PDO::FETCH_COLUMN)) {
					$email = end($email);
				} else {
					return false;
				}
				break;
		}

		/* @var modPHPMailer $mail */
		if (!$mail = $this->modx->getService('mail', 'mail.modPHPMailer')) {
			return false;
		}

		$mail->setHTML(true);
		$mail->set(modMail::MAIL_SUBJECT, $subject);
		$mail->set(modMail::MAIL_BODY, $body);
		$mail->set(modMail::MAIL_SENDER, $sender);
		$mail->set(modMail::MAIL_FROM, $from);
		$mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('site_name'));
		$mail->address('to', $email);
		if (!$mail->send()) {
			$this->modx->log(xPDO::LOG_LEVEL_ERROR, 'An error occurred while trying to send the email: ' . $mail->mailer->ErrorInfo);
			$mail->reset();

			return false;
		}
		$mail->reset();

		return true;
	}

	/** @inheritdoc} */
	public function processObject(xPDOObject $instance, $format = false, $replace = true, $keyPrefix = '', $autoPrefix = false)
	{
		$pls = $instance->toArray();
		$prefix = '';

		switch (true) {
			case $instance instanceof modUser:
				unset(
					$pls['cachepwd'],
					$pls['class_key'],
					$pls['remote_key'],
					$pls['remote_data'],
					$pls['hash_class'],
					$pls['password'],
					$pls['salt']
				);
				$prefix = 'user_';
				break;
			case $instance instanceof modUserProfile:
				$pls['gravatar'] = $pls['email'];
				$prefix = 'profile_';
				break;
			default:
				break;
		}

		if ($format) {
			while (list($key, $val) = each($pls)) {
				$keyMethod = 'format' . ucfirst(str_replace('_', '', $key));
				if (!method_exists($this, $keyMethod)) {
					continue;
				}
				if ($replace) {
					$pls[$key] = $this->$keyMethod($val);
				} else {
					$pls['format_' . $key] = $this->$keyMethod($val);
				}
			}
		}

		if ($autoPrefix) {
			$keyPrefix .= $prefix;
		}

		if (!empty($keyPrefix)) {
			$plsPrefix = array();
			foreach ($pls as $key => $value) {
				$plsPrefix[$keyPrefix . $key] = $value;
			}
			$pls = $plsPrefix;
		}

		return $pls;
	}

	/** @inheritdoc} */
	public function addLock(array $data = array(), array $options = array())
	{
		$locked = false;
		$lockedBy = $this->getLock($data);
		if (empty($lockedBy)) {
			$ttlLock = $this->getOption('ttlLock', $options, $this->modx->getOption('lock_ttl', null, 360));
			$this->modx->registry->locks->subscribe("/el/{$data['key']}/");
			$this->modx->registry->locks->send("/el/{$data['key']}/", array($data['id'] => $data), array('ttl' => $ttlLock));
			$locked = true;
		}

		return $locked;
	}

	/** @inheritdoc} */
	public function getLock(array $data = array())
	{
		$lock = 0;
		if ($this->modx->getService('registry', 'registry.modRegistry')) {
			$this->modx->registry->addRegister('locks', 'registry.modDbRegister', array('directory' => 'locks'));
			$this->modx->registry->locks->connect();
			$this->modx->registry->locks->subscribe("/el/{$data['key']}/{$data['id']}");
			if ($msgs = $this->modx->registry->locks->read(array('remove_read' => false, 'poll_limit' => 1))) {
				$lock = reset($msgs);
			}
		}

		return $lock;
	}

	/** @inheritdoc} */
	public function removeLock(array $data = array())
	{
		$removed = false;
		if ($this->modx->getService('registry', 'registry.modRegistry')) {
			$this->modx->registry->addRegister('locks', 'registry.modDbRegister', array('directory' => 'locks'));
			$this->modx->registry->locks->connect();
			$this->modx->registry->locks->subscribe("/el/{$data['key']}/{$data['id']}");
			$this->modx->registry->locks->read(array('remove_read' => true, 'poll_limit' => 1));
			$removed = true;
		}

		return $removed;
	}

	/**
	 * This method returns prepared response
	 *
	 * @param mixed $response
	 *
	 * @return array|string $response
	 */
	public function prepareResponse($response)
	{
		if ($response instanceof modProcessorResponse) {
			$output = $response->getResponse();
		} else {
			$message = $response;
			if (empty($message)) {
				$message = $this->lexicon('err_unknown');
			}
			$output = $this->failure($message);
		}
		if ($this->config['jsonResponse'] AND is_array($output)) {
			$output = $this->modx->toJSON($output);
		} elseif (!$this->config['jsonResponse'] AND !is_array($output)) {
			$output = $this->modx->fromJSON($output);
		}

		return $output;
	}

	/**
	 * Shorthand for the call of processor
	 *
	 * @access public
	 *
	 * @param string $action Path to processor
	 * @param array  $data Data to be transmitted to the processor
	 *
	 * @return mixed The result of the processor
	 */
	public function runProcessor($action = '', $data = array(), $json = true, $path = '')
	{
		if (empty($action)) {
			return false;
		}

		$this->modx->error->reset();
		/* @var modProcessorResponse $response */
		$response = $this->modx->runProcessor($action, $data, array(
			'processors_path' => !empty($path) ? $path : $this->config['processorsPath']
		));

		if (!$json) {
			$this->setJsonResponse(false);
		}
		$result = $this->config['prepareResponse'] ? $this->prepareResponse($response) : $response;
		$this->setJsonResponse();

		return $result;
	}

	/** @inheritdoc} */
	public function processSnippet(array $data = array(), $name = '')
	{
		$output = '';
		if (isset($data['snippetName'])) {
			$name = $data['snippetName'];
		}
		if ($snippet = $this->modx->getObject('modSnippet', array('name' => $name))) {
			$snippet->_cacheable = false;
			$snippet->_processed = false;
			$properties = $snippet->getProperties();
			$properties = array_merge($properties, $data);
			$output = $snippet->process($properties);
			if (strpos($output, '[[') !== false) {
				$output = $this->processTags($output);
			}
		}

		return $output;
	}

	/**
	 * Collects and processes any set of tags
	 *
	 * @param mixed   $html Source code for parse
	 * @param integer $maxIterations
	 *
	 * @return mixed $html Parsed html
	 */
	public function processTags($html, $maxIterations = 10)
	{
		if (strpos($html, '[[') !== false) {
			$this->modx->getParser()->processElementTags('', $html, false, false, '[[', ']]', array(), $maxIterations);
			$this->modx->getParser()->processElementTags('', $html, true, true, '[[', ']]', array(), $maxIterations);
		}

		return $html;
	}

	/** @inheritdoc} */
	public function getPropertiesKey(array $properties = array())
	{
		return !empty($properties['propkey']) ? $properties['propkey'] : false;
	}

	/** @inheritdoc} */
	public function saveProperties(array $properties = array())
	{
		return !empty($properties['propkey']) ? $_SESSION[$this->namespace][$properties['propkey']] = $properties : false;
	}

	/** @inheritdoc} */
	public function getProperties($key = '')
	{
		return !empty($_SESSION[$this->namespace][$key]) ? $_SESSION[$this->namespace][$key] : array();
	}

	/** @inheritdoc} */
	public function formatEmail($value = '')
	{
		return strtolower(trim($value));
	}

	/** @inheritdoc} */
	public function formatForm_key($value = '')
	{
		return trim($value);
	}

	/** @inheritdoc} */
	public function validateEmail($value = '')
	{
		return preg_match('/^[^@а-яА-Я]+@[^@а-яА-Я]+(?<!\.)\.[^\.а-яА-Я]{2,}$/m', $value) ? $value : false;
	}

	/** @inheritdoc} */
	public function validateForm_key($value = '')
	{
		return !empty($value) AND count($this->getProperties($value)) > 0 ? $value : false;
	}

	/** @inheritdoc} */
	public function setJsonResponse($json = true)
	{
		return ($this->config['jsonResponse'] = $json);
	}

	/**
	 * @param string $message
	 * @param array  $data
	 * @param array  $placeholders
	 *
	 * @return array|string
	 */
	public function failure($message = '', $data = array(), $placeholders = array())
	{
		$response = array(
			'success' => false,
			'message' => $this->lexicon($message, $placeholders),
			'data'    => $data,
		);

		return $this->config['jsonResponse'] ? $this->modx->toJSON($response) : $response;
	}

	/**
	 * @param string $message
	 * @param array  $data
	 * @param array  $placeholders
	 *
	 * @return array|string
	 */
	public function success($message = '', $data = array(), $placeholders = array())
	{
		$response = array(
			'success' => true,
			'message' => $this->lexicon($message, $placeholders),
			'data'    => $data,
		);

		return $this->config['jsonResponse'] ? $this->modx->toJSON($response) : $response;
	}

}