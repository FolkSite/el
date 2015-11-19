<?php

/** @var array $scriptProperties */
$scriptProperties['snippetName'] = $modx->getOption('snippetName', $scriptProperties, $this->get('name'), true);
$scriptProperties['propkey'] = $modx->getOption('propkey', $scriptProperties, sha1(serialize($this->get('properties'))), true);
$scriptProperties['objectName'] = $modx->getOption('objectName', $scriptProperties, 'el', true);

$scriptProperties['tplLogin'] = $modx->getOption('tplLogin', $scriptProperties, 'tpl.el.login', true);
$scriptProperties['tplLogout'] = $modx->getOption('tplLogout', $scriptProperties, 'tpl.el.logout', true);
$scriptProperties['tplLink'] = $modx->getOption('tplActivate', $scriptProperties, 'tpl.el.activate', true);
$scriptProperties['ttlLink'] = $modx->getOption('ttlLink', $scriptProperties, 600, true);
$scriptProperties['ttlLock'] = $modx->getOption('ttlLock', $scriptProperties, 600, true);
$scriptProperties['groups'] = $modx->getOption('groups', $scriptProperties, '', true);
$scriptProperties['loginContext'] = $modx->getOption('loginContext', $scriptProperties, $modx->context->key, true);
$scriptProperties['addContexts'] = $modx->getOption('addContexts', $scriptProperties, '', true);
$scriptProperties['resourceLogin'] = $modx->getOption('resourceLogin', $scriptProperties, $modx->resource->id, true);
$scriptProperties['resourceLogout'] = $modx->getOption('resourceLogout', $scriptProperties, $modx->resource->id, true);

$scriptProperties['namespace'] = $modx->getOption('namespace', $scriptProperties, 'el', true);
$scriptProperties['path'] = $modx->getOption('path', $scriptProperties, 'controllers/web/ellogin', true);
$scriptProperties['location'] = $modx->getOption('location', $scriptProperties, 1, true);

/** @var modSnippet $snippet */
if ($snippet = $modx->getObject('modSnippet', array('name' => 'ecc'))) {
	$snippet->_cacheable = false;
	$snippet->_processed = false;
	return $snippet->process($scriptProperties);
}
