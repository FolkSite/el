<?php

/** elLogin */
class elLogin extends eccBaseController
{
	/* @var el $el */
	public $el;

	/** @inheritdoc} */
	public function getLanguageTopics()
	{
		return array('el:default');
	}

	/** @inheritdoc} */
	public function loadCustomJsCss()
	{
		if (!isset($this->modx->loadedjscripts[$this->config['objectName']])) {
			$pls = $this->makePlaceholders($this->el->config);
			foreach ($this->config as $k => $v) {
				if (is_string($v)) {
					$this->config[$k] = str_replace($pls['pl'], $pls['vl'], $v);
				}
			}
			if ($this->config['jqueryJs']) {
				$this->modx->regClientScript(preg_replace('#(\n|\t)#', '', '
				<script type="text/javascript">
					if (typeof jQuery == "undefined") {
						document.write("<script src=\"' . $this->config['jqueryJs'] . '\" type=\"text/javascript\"><\/script>");
					}
				</script>
				'), true);
			}
			if ($this->config['frontendMainCss']) {
				$this->modx->regClientCSS($this->config['frontendMainCss']);
			}
			if ($this->config['frontendMainJs']) {
				$this->modx->regClientScript($this->config['frontendMainJs']);
			}
			if ($this->config['frontendCss']) {
				$this->modx->regClientCSS($this->config['frontendCss']);
			}
			if ($this->config['frontendJs']) {
				$this->modx->regClientScript($this->config['frontendJs']);
			}
		}

		return $this->modx->loadedjscripts[$this->config['objectName']] = 1;
	}

	/** @inheritdoc} */
	public function initialize($ctx = 'web')
	{
		$this->modx->error->errors = array();
		$this->modx->error->message = '';

		$config = $this->modx->toJSON(array(
			'connectorUrl' => $this->config['actionUrl'],
			'namespace' => $this->config['namespace'],
			'controller' => $this->config['controller'],
			'path' => $this->config['path'],
		));
		$this->regTopScript("eccConfig.{$this->config['namespace']}={$config};");

		/* @var el $el */
		$corePath = $this->modx->getOption('el_core_path', null, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/el/');
		if (!$this->el = $this->modx->getService('el', 'el', $corePath . 'model/el/', array('core_path' => $corePath))) {
			return false;
		}
		$this->el->initialize();

		$this->config = array_merge($this->config, array(
			'jqueryJs' => $this->config['assetsUrl'] . 'vendor/jquery/jquery.min.js',
		));
		$this->el->saveProperties($this->config);

		$topics = $this->getLanguageTopics();
		foreach ($topics as $topic) {
			$this->modx->lexicon->load($topic);
		}

		$this->loadCustomJsCss();

		return true;
	}

	/** @inheritdoc} */
	public function DefaultAction()
	{
		$authenticated = $this->modx->user->isAuthenticated($this->modx->context->key);
		$send = $this->el->getLock(array('key' => 'send', 'id' => session_id()));
		$send = (int)isset($send['key']);

		$pls = array(
			'authenticated' => $authenticated,
			'send' => $send,
			'propkey' => $this->el->getPropertiesKey($this->config),
		);

		switch (true) {
			case $authenticated:
				$pls = array_merge(
					$pls,
					$this->el->processObject($this->modx->user->Profile, true, true),
					$this->el->processObject($this->modx->user, true, true)
				);
				return $this->modx->getChunk($this->config['tplLogout'], $pls);
			default:
			case !$authenticated:
				return $this->modx->getChunk($this->config['tplLogin'], $pls);
		}
	}

	/** @inheritdoc} */
	public function defaultProcessorAction($data = array())
	{
		$data = $this->el->validateData($this->el->prepareData($data));

		return $this->el->runProcessor($data['action'], $data, $json = true, MODX_CORE_PATH . 'components/el/processors/web/');
	}

}
