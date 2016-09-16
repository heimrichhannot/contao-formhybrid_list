<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) Heimrich & Hannot GmbH
 * @package formhybrid_list
 * @author Dennis Patzer
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\FormHybridList;

use HeimrichHannot\FormHybrid\Form;

class ListFilterForm extends Form
{
	protected $isFilterForm = true;
	protected $objListModule;

	public function __construct($objModule)
	{
		$this->strMethod = FORMHYBRID_METHOD_GET;
		$this->isFilterForm = true;
		$this->objListModule = $objModule;
		$objModule->formHybridTemplate = $objModule->formHybridTemplate ?: 'formhybrid_list_filter';
		$objModule->formHybridEditable = $objModule->customFilterFields;

		$arrHeadline = deserialize($objModule->filterHeadline);
		$objModule->filterHeadline = is_array($arrHeadline) ? $arrHeadline['value'] : $arrHeadline;
		$objModule->filterHl = is_array($arrHeadline) ? $arrHeadline['unit'] : 'h1';
		$objModule->formHybridSkipValidation = true;

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
		$this->arrFields[FORMHYBRID_LIST_BUTTON_FILTER] = $this->generateField(FORMHYBRID_LIST_BUTTON_FILTER, array(
			'inputType' => 'submit',
			'label'		=> &$GLOBALS['TL_LANG']['formhybrid_list'][FORMHYBRID_LIST_BUTTON_FILTER],
			'eval' => array('class' => 'filter')
		));
	}

	public function modifyDC(&$arrDca = null)
	{
		foreach ($arrDca['fields'] as $strField => $arrData)
		{
			switch($arrData['inputType'])
			{
				case 'textarea':
					$arrDca['fields'][$strField]['inputType'] = 'text';
				break;
				case 'checkbox':
					// Replace boolean checkbox value with "yes" and "no"
					if(!$arrData['eval']['multiple'])
					{
						$arrDca['fields'][$strField]['eval']['includeBlankOption'] = true;
						$arrDca['fields'][$strField]['eval']['isBoolean'] = true; // required to be set within Modulelist::applyFilters() cause checkbox is select there
						$arrDca['fields'][$strField]['inputType'] = 'select';
						$arrDca['fields'][$strField]['options'] = array('1', '0');
						$arrDca['fields'][$strField]['reference'] = &$GLOBALS['TL_LANG']['formhybrid_list']['reference']['yes_no'];
					}
				break;
			}
		}

		$this->objListModule->modifyDC($arrDca);
	}
}
