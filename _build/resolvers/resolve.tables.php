<?php

if ($object->xpdo) {
	/** @var modX $modx */
	$modx =& $object->xpdo;

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_UPGRADE:
		case xPDOTransport::ACTION_INSTALL:
			$modelPath = $modx->getOption('el_core_path', null, $modx->getOption('core_path') . 'components/el/') . 'model/';
			$modx->addPackage('el', $modelPath);

			$manager = $modx->getManager();
//			$objects = array(
//				'elItem',
//			);
//			foreach ($objects as $tmp) {
//				$manager->createObjectContainer($tmp);
//			}
			break;

		case xPDOTransport::ACTION_UNINSTALL:
			break;
	}
}
return true;
