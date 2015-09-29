<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package formhybrid_list
 * @author Dennis Patzer <d.patzer@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\FormHybridList;

class ListFilterForm extends \HeimrichHannot\FormHybrid\Form
{
	protected $isFilterForm = true;

	public function __construct($objModule)
	{
		$this->strMethod = FORMHYBRID_METHOD_GET;
		$this->isFilterForm = true;
		$objModule->formHybridTemplate = $objModule->formHybridTemplate ?: 'formhybrid_list_filter';

		if ($objModule->addCustomFilterFields)
		{
			$objModule->formHybridEditable = $objModule->customFilterFields;
		}

		parent::__construct($objModule);
	}
	
	protected function onSubmitCallback(\DataContainer $dc) {
		$this->submission = $dc;

		if (is_array($this->arrSubmitCallbacks) && !empty($this->arrSubmitCallbacks))
		{
			foreach ($this->arrSubmitCallbacks as $arrCallback)
			{
				if (is_array($arrCallback) && !empty($arrCallback))
				{
					$arrCallback[0]::$arrCallback[1]($dc);
				}
			}
		}
	}
	
	protected function compile() {}
	
	protected function generateSubmitField()
	{
		$this->arrFields[FORMHYBRID_LIST_NAME_FILTER] = $this->generateField(FORMHYBRID_LIST_NAME_FILTER, array(
			'inputType' => 'submit',
			'label'		=> &$GLOBALS['TL_LANG']['formhybrid_list']['filter'],
			'eval' => array('class' => 'filter')
		));
	}
}
