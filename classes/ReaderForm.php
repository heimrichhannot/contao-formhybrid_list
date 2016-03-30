<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package frontendedit
 * @author Dennis Patzer
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\FormHybridList;

class ReaderForm extends \HeimrichHannot\FormHybrid\Form
{
	protected $objReaderModule;

	public function __construct($objModule, array $submitCallbacks = array(), $intId = 0, $objReaderForm)
	{
		$this->strMethod = FORMHYBRID_METHOD_POST;
		$objModule->formHybridTemplate = $objModule->formHybridTemplate ?: 'formhybrid_default';
		$this->objReaderModule = $objReaderForm;
		$objModule->initiallySaveModel = true;
		$objModule->strFormClass = 'jquery-validation';
		$this->arrSubmitCallbacks = $submitCallbacks;

		parent::__construct($objModule, $intId);
	}

	public function generate()
	{
		$strResult = parent::generate();
		if ($this->intId && $this->setPageTitle)
		{
			global $objPage;
			$objPage->pageTitle = $this->objModel->{$this->pageTitleField};
		}
		return $strResult;
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
	
	public function setReaderModule($objModule)
	{
		$this->objReaderModule = $objModule;
	}

	public function modifyDC(&$arrDca = null)
	{
		$this->objReaderModule->modifyDC($arrDca);
	}
}