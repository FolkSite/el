<?php

$corePath = $modx->getOption('el_core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/el/');
$el = $modx->getService('el', 'el', $corePath . 'model/el/', array('core_path' => $corePath));

$className = 'elEvent' . $modx->event->name;
$modx->loadClass('elEventPlugin', $el->getOption('modelPath') . 'el/events/', true, true);
$modx->loadClass($className, $el->getOption('modelPath') . 'el/events/', true, true);
if (class_exists($className)) {
	/** @var $el $handler */
	$handler = new $className($modx, $scriptProperties);
	$handler->run();
}
return;
