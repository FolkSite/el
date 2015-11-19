<?php

abstract class elEventPlugin
{
	/** @var modX $modx */
	protected $modx;
	/** @var el $el */
	protected $el;
	/** @var array $scriptProperties */
	protected $scriptProperties;

	public function __construct($modx, &$scriptProperties)
	{
		$this->scriptProperties =& $scriptProperties;
		$this->modx = $modx;
		$this->el = $this->modx->el;

		if (!is_object($this->el)) {
			$this->el = $this->modx->getService('el');
		}

	}

	abstract public function run();
}