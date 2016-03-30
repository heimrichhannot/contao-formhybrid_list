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

use HeimrichHannot\FormHybrid\DC_Hybrid;
use HeimrichHannot\FormHybrid\FormHelper;
use HeimrichHannot\StatusMessages\StatusMessage;

class ModuleReader extends \Module
{
	protected $strTemplate = 'mod_formhybrid_reader';
	protected $arrSubmitCallbacks = array();
	protected $strFormClass = 'HeimrichHannot\\FormHybridList\\ReaderForm';
	protected $objForm;

	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### FORMHYBRID READER ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		\DataContainer::loadDataContainer($this->formHybridDataContainer);
		\System::loadLanguageFile($this->formHybridDataContainer);

		// Set the item from the auto_item parameter
		if (!isset($_GET['items']) && \Config::get('useAutoItem') && isset($_GET['auto_item']))
		{
			\Input::setGet('items', \Input::get('auto_item'));
		}

		// Do not index or cache the page if no item has been specified
		if (!\Input::get('items'))
		{
			/** @var \PageModel $objPage */
			global $objPage;

			$objPage->noSearch = 1;
			$objPage->cache = 0;

			return '';
		}

		return parent::generate();
	}
	
	protected function compile()
	{
		$this->Template->headline = $this->headline;
		$this->Template->hl = $this->hl;

		$this->strFormId = $this->formHybridDataContainer . '_' . $this->id;
		$this->intId = \Input::get('items');

		if (!$this->intId)
		{
			StatusMessage::addError($GLOBALS['TL_LANG']['formhybrid_list']['noIdFound'], $this->id, 'noidfound');
			return;
		}
		else
		{
			if (!$this->checkEntityExists($this->intId))
			{
				StatusMessage::addError($GLOBALS['TL_LANG']['formhybrid_list']['notExisting'], $this->id, 'noentity');
				return;
			}

			if ($this->checkPermission($this->intId))
			{
				$strItemClass = \Model::getClassFromTable($this->formHybridDataContainer);

				if (($objItem = $strItemClass::findByPk($this->intId)) !== null)
				{
					// redirect on specific field value
					DC_Hybrid::doFieldDependentRedirect($this, $objItem);

					if (\Input::get('isAjax'))
					{
						$objModalWrapper = new \FrontendTemplate($this->modalTpl ?: 'formhybrid_reader_modal_bootstrap');
						$objModalWrapper->setData($this->arrData);
						$objModalWrapper->item = $this->parseItem($objItem);
						die($objModalWrapper->parse());
					}
					else
					{
						$this->Template->item = $this->parseItem($objItem);
					}
				}
			}
			else
			{
				StatusMessage::addError($GLOBALS['TL_LANG']['formhybrid_list']['noPermission'], $this->id, 'nopermission');
				return;
			}
		}
	}

	protected function parseItem($objItem, $strClass='', $intCount=0)
	{
		$objTemplate = new \FrontendTemplate($this->itemTemplate);

		$objTemplate->setData($objItem->row());
		$objTemplate->class = $strClass;
		$objTemplate->formHybridDataContainer = $this->formHybridDataContainer;

		$objDc = new \DC_Table($this->formHybridDataContainer);
		$objDc->activeRecord = $objItem;

		// untransformed values in the raw array
		$objItem->raw = $objItem->row();

		// transform and escape values
		foreach ($objItem->row() as $strField => $varValue)
		{
			$varValue = FormHelper::getFormatedValueByDca($varValue, $this->dca['fields'][$strField], $objDc);
			$objItem->{$strField} = FormHelper::escapeAllEntities($this->formHybridDataContainer, $strField, $varValue);
		}

		if ($this->publishedField)
		{
			$objItem->isPublished = ($this->invertPublishedField ?
					!$objItem->{$this->publishedField} : $objItem->{$this->publishedField});
		}

		// HOOK: add custom logic
		if (isset($GLOBALS['TL_HOOKS']['parseItems']) && is_array($GLOBALS['TL_HOOKS']['parseItems']))
		{
			foreach ($GLOBALS['TL_HOOKS']['parseItems'] as $callback)
			{
				$this->import($callback[0]);
				$this->$callback[0]->$callback[1]($objTemplate, $objItem, $this);
			}
		}

		return $objTemplate->parse();
	}

	public function checkEntityExists($intId)
	{
		if ($strItemClass = \Model::getClassFromTable($this->formHybridDataContainer))
			return $strItemClass::findByPk($intId) !== null;
	}

	public function checkPermission($intId)
	{
		$strItemClass = \Model::getClassFromTable($this->formHybridDataContainer);

		if ($this->addShowConditions && ($objItem = $strItemClass::findByPk($intId)) !== null)
		{
			$arrConditions = deserialize($this->showConditions, true);

			if (!empty($arrConditions))
				foreach ($arrConditions as $arrCondition)
				{
					if ($objItem->{$arrCondition['field']} != $this->replaceInsertTags($arrCondition['value']))
						return false;
				}
		}

		return true;
	}

	public function setSubmitCallbacks(array $callbacks)
	{
		$this->arrSubmitCallbacks = $callbacks;
	}

	public function modifyDC(&$arrDca = null) {}
}
