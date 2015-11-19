<?php

$settings = array();

$tmp = array(


	//временные

//		'assets_path' => array(
//			'value' => '{base_path}el/assets/components/el/',
//			'xtype' => 'textfield',
//			'area' => 'el_temp',
//		),
//		'assets_url' => array(
//			'value' => '/el/assets/components/el/',
//			'xtype' => 'textfield',
//			'area' => 'el_temp',
//		),
//	'core_path' => array(
//		'value' => '{base_path}el/core/components/el/',
//		'xtype' => 'textfield',
//		'area' => 'el_temp',
//	),

	//временные


	/*
	'some_setting' => array(
		'xtype' => 'combo-boolean',
		'value' => true,
		'area' => 'el_main',
	),
*/
);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => 'el_' . $k,
			'namespace' => PKG_NAME_LOWER,
		), $v
	), '', true, true);

	$settings[] = $setting;
}

unset($tmp);
return $settings;
