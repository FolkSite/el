<?php
/** @noinspection PhpIncludeInspection */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var el $el */
$el = $modx->getService('el', 'el', $modx->getOption('el_core_path', null, $modx->getOption('core_path') . 'components/el/') . 'model/el/');
$modx->lexicon->load('el:default');

// handle request
$corePath = $modx->getOption('el_core_path', null, $modx->getOption('core_path') . 'components/el/');
$path = $modx->getOption('processorsPath', $el->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
	'processors_path' => $path,
	'location' => '',
));