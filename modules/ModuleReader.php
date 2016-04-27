<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) Heimrich & Hannot GmbH
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
	// avoid any messages -> handled sub class
	protected $blnSilentMode = false;

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

		$this->dca = $GLOBALS['TL_DCA'][$this->formHybridDataContainer];

		// Set the item from the auto_item parameter
		if (!isset($_GET['items']) && \Config::get('useAutoItem') && isset($_GET['auto_item']))
		{
			\Input::setGet('items', \Input::get('auto_item'));
		}

		$this->intId = $this->intId ?: (\Input::get('items') ?: \Input::get('id'));

		// Do not index or cache the page if no item has been specified
		if (!$this->intId)
		{
			/** @var \PageModel $objPage */
			global $objPage;

			$objPage->noSearch = 1;
			$objPage->cache = 0;
		}

		return parent::generate();
	}
	
	protected function compile()
	{
		$this->Template->headline = $this->headline;
		$this->Template->hl = $this->hl;

		$this->strFormId = $this->formHybridDataContainer . '_' . $this->id;

		if ($this->intId && !is_numeric($this->intId))
		{
			$strItemClass = \Model::getClassFromTable($this->formHybridDataContainer);

			if (($objItem = $strItemClass::findOneByAlias($this->intId)) !== null)
			{
				$this->intId = $objItem->id;
			}
		}

		if (!$this->intId)
		{
			if (!$this->blnSilentMode)
				StatusMessage::addError($GLOBALS['TL_LANG']['formhybrid_list']['noIdFound'], $this->id, 'noidfound');
			$this->Template->invalid = true;
		}
		else
		{
			if (!$this->checkEntityExists($this->intId))
			{
				if (!$this->blnSilentMode)
					StatusMessage::addError($GLOBALS['TL_LANG']['formhybrid_list']['notExisting'], $this->id, 'noentity');
				$this->Template->invalid = true;
			}
			else
			{
				if ($this->checkPermission($this->intId))
				{
					$strItemClass = \Model::getClassFromTable($this->formHybridDataContainer);

					if (($objItem = $strItemClass::findByPk($this->intId)) !== null)
					{
						// redirect on specific field value
						DC_Hybrid::doFieldDependentRedirect($this, $objItem);

						// page title
						if ($this->setPageTitle)
						{
							global $objPage;
							$objPage->pageTitle = $objItem->{$this->pageTitleField};

							if ($this->pageTitlePattern)
							{
								$objPage->pageTitle = preg_replace_callback('@%([^%]+)%@i', function ($arrMatches) use ($objItem) {
									return $objItem->{$arrMatches[1]};
								}, $this->pageTitlePattern);
							}
						}

						if (\Input::get('isAjax'))
						{
							$objModalWrapper = new \FrontendTemplate($this->modalTpl ?: 'formhybrid_reader_modal_bootstrap');
							$objModalWrapper->setData($this->arrData);
							$objModalWrapper->item = $this->replaceInsertTags($this->parseItem($objItem));
							die($objModalWrapper->parse());
						}
						else
						{
							$this->Template->item = $this->replaceInsertTags($this->parseItem($objItem));
						}
					}
				}
				else
				{
					if (!$this->blnSilentMode)
						StatusMessage::addError($GLOBALS['TL_LANG']['formhybrid_list']['noPermission'], $this->id, 'nopermission');
					$this->Template->invalid = true;
				}
			}
		}
	}

	protected function parseItem($objItem, $strClass='', $intCount=0)
	{
		// work on a cloned item for supporting multiple reader modules on a single page
		$objItemTmp = unserialize(serialize($objItem));

		// prepare item
		$objDc = new \DC_Table($this->formHybridDataContainer);
		$objDc->activeRecord = $objItemTmp;

		// untransformed values in the raw array
		$objItemTmp->raw = $objItemTmp->row();

		// transform and escape values
		foreach ($objItemTmp->row() as $strField => $varValue)
		{
			if ($strField == 'raw')
				continue;

			$varValue = Helper::getFormattedValueByDca($varValue, $this->formHybridDataContainer, $this->dca['fields'][$strField], $objItemTmp, $objDc);
			$objItemTmp->{$strField} = FormHelper::escapeAllEntities($this->formHybridDataContainer, $strField, $varValue);
		}

		if ($this->publishedField)
		{
			$objItemTmp->isPublished = ($this->invertPublishedField ?
					!$objItemTmp->{$this->publishedField} : $objItemTmp->{$this->publishedField});
		}

		$objTemplate = new \FrontendTemplate($this->itemTemplate);

		// items contain module and item params (higher priority: item)
		$objTemplate->setData($objItemTmp->row() + $this->arrData);
		$objTemplate->class = $strClass;
		$objTemplate->formHybridDataContainer = $this->formHybridDataContainer;
		$objTemplate->useDummyImage = $this->useDummyImage;
		$objTemplate->dummyImage = $this->dummyImage;

		// HOOK: add custom logic
		if (isset($GLOBALS['TL_HOOKS']['parseItems']) && is_array($GLOBALS['TL_HOOKS']['parseItems']))
		{
			foreach ($GLOBALS['TL_HOOKS']['parseItems'] as $callback)
			{
				$this->import($callback[0]);
				$this->$callback[0]->$callback[1]($objTemplate, $objItemTmp, $this);
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
}
