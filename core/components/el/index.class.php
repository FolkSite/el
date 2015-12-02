<?php

/**
 * Class elMainController
 */
abstract class elMainController extends modExtraManagerController {
	/** @var el $el */
	public $el;


	/**
	 * @return void
	 */
	public function initialize() {
		$corePath = $this->modx->getOption('el_core_path', null, $this->modx->getOption('core_path') . 'components/el/');
		require_once $corePath . 'model/el/el.class.php';

		$this->el = new el($this->modx);
		$this->addCss($this->el->config['cssUrl'] . 'mgr/main.css');
		$this->addJavascript($this->el->config['jsUrl'] . 'mgr/el.js');
		$this->addHtml('
		<script type="text/javascript">
			el.config = ' . $this->modx->toJSON($this->el->config) . ';
			el.config.connector_url = "' . $this->el->config['connectorUrl'] . '";
		</script>
		');

		parent::initialize();
	}


	/**
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('el:default');
	}


	/**
	 * @return bool
	 */
	public function checkPermissions() {
		return true;
	}
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends elMainController {

	/**
	 * @return string
	 */
	public static function getDefaultController() {
		return 'home';
	}
}