<?php

$properties = array();

$tmp = array(
	'tplLogin' => array(
		'type' => 'textfield',
		'value' => 'tpl.el.login',
	),
	'tplLogout' => array(
		'type' => 'textfield',
		'value' => 'tpl.el.logout',
	),
	'tplLink' => array(
		'type' => 'textfield',
		'value' => 'tpl.el.link',
	),
	'ttlLink' => array(
		'type' => 'textfield',
		'value' => 600,
	),
	'ttlLock' => array(
		'type' => 'textfield',
		'value' => 600,
	),
	'groups' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'loginContext' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'addContexts' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'resourceLogin' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'resourceLogout' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'frontendMainCss' => array(
		'type' => 'textfield',
		'value' => '[[+assetsUrl]]css/web/main/default.css',
	),
	'frontendMainJs' => array(
		'type' => 'textfield',
		'value' => '[[+assetsUrl]]js/web/main/default.js',
	),

	'frontendCss' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'frontendJs' => array(
		'type' => 'textfield',
		'value' => '[[+assetsUrl]]js/web/login/default.js',
	),

);

foreach ($tmp as $k => $v) {
	$properties[] = array_merge(
		array(
			'name' => $k,
			'desc' => PKG_NAME_LOWER . '_prop_' . $k,
			'lexicon' => PKG_NAME_LOWER . ':properties',
		), $v
	);
}

return $properties;