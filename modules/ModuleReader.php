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
use HeimrichHannot\Haste\Util\FormSubmission;
use HeimrichHannot\StatusMessages\StatusMessage;

class ModuleReader extends \Module
{
	protected $strTemplate = 'mod_formhybrid_reader';

	// avoid any messages -> handled sub class
	protected $blnSilentMode = false;
	protected $blnUseBlob = false;
	protected $strWrapperId = 'formhybrid-reader_';
	protected $strWrapperClass = 'formhybrid-reader';

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

		$this->strWrapperId .= $this->id;

		$this->dca = $GLOBALS['TL_DCA'][$this->formHybridDataContainer];

		// Set the item from the auto_item parameter
		if (!isset($_GET['items']) && \Config::get('useAutoItem') && isset($_GET['auto_item']))
		{
			\Input::setGet('items', \Input::get('auto_item'));
		}

		$this->intId = $this->intId ?: (\Input::get('items') ?: \Input::get('id'));

		if ($this->addExistanceConditions)
		{
			$arrConditions = deserialize($this->existanceConditions, true);
			$strItemClass = \Model::getClassFromTable($this->formHybridDataContainer);

			if (!empty($arrConditions))
			{
				$arrColumns = [];
				$arrValues = [];

				foreach ($arrConditions as $arrCondition)
				{
					$arrColumns[] = $arrCondition['field'] . '=?';
					$arrValues[] = $this->replaceInsertTags($arrCondition['value']);
				}

				if (($objItem = $strItemClass::findOneBy($arrColumns, $arrValues)) !== null)
				{
					$this->intId = $objItem->id;
				}
			}
		}

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
		$this->Template->wrapperClass = $this->strWrapperClass;
		$this->Template->wrapperId = $this->strWrapperId;

		$this->strFormId = $this->formHybridDataContainer . '_' . $this->id;

		// Do not change this order (see #6191)
		$this->Template->style = !empty($this->arrStyle) ? implode(' ', $this->arrStyle) : '';
		$this->Template->class = trim('mod_' . $this->type . ' ' . $this->cssID[1]);
		$this->Template->cssID = ($this->cssID[0] != '') ? ' id="' . $this->cssID[0] . '"' : '';

		$this->Template->inColumn = $this->strColumn;

		if ($this->Template->headline == '')
		{
			$this->Template->headline = $this->headline;
		}

		if ($this->Template->hl == '')
		{
			$this->Template->hl = $this->hl;
		}

		if (!empty($this->objModel->classes) && is_array($this->objModel->classes))
		{
			$this->Template->class .= ' ' . implode(' ', $this->objModel->classes);
		}

		if ($this->intId && !is_numeric($this->intId))
		{
			$strItemClass = \Model::getClassFromTable($this->formHybridDataContainer);

			$strAliasField = $this->aliasField ?: 'id';

			if (($objItem = $strItemClass::findOneBy($strAliasField, $this->intId)) !== null &&
				(!$this->addShareCol || !FormHybridList::shareTokenExpiredOrEmpty($objItem, time())))
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
						if ($this->blnUseBlob && $objItem->formHybridBlob)
						{
							$arrBlob = deserialize($objItem->formHybridBlob, true);

							foreach ($arrBlob as $strField => $varValue)
							{
								if ($strField == 'formHybridBlob')
									continue;

								$objItem->$strField = $varValue;
							}

							$objItem->formHybridBlob = null;
						}

						// redirect on specific field value
//						DC_Hybrid::doFieldDependentRedirect($this, $objItem);

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

						// comments
						if ($this->noComments || !in_array('comments', \ModuleLoader::getActive()) ||
							!\Database::getInstance()->fieldExists('pid', $this->formHybridDataContainer))
						{
							$this->Template->allowComments = false;
						}
						else
						{
							$objArchive = $objItem->getRelated('pid');
							$this->Template->allowComments = $objArchive->allowComments;

							if ($objArchive->allowComments)
							{
								// Adjust the comments headline level
								$intHl = min(intval(str_replace('h', '', $this->hl)), 5);
								$this->Template->hlc = 'h' . ($intHl + 1);
								
								$objComments = \System::importStatic('HeimrichHannot\\FormHybridList\\Comments');
								$arrNotifies = [];

								// Notify the system administrator
								if ($objArchive->notify != 'notify_author')
								{
									$arrNotifies[] = $GLOBALS['TL_ADMIN_EMAIL'];
								}

								// Notify the author
								if ($objArchive->notify != 'notify_admin')
								{
									if (($objAuthor = $objItem->getRelated('memberAuthor')) !== null && $objAuthor->email != '')
									{
										$arrNotifies[] = $objAuthor->email;
									}
								}

								$objConfig = new \stdClass();

								$objConfig->perPage = $objArchive->perPage;
								$objConfig->order = $objArchive->sortOrder;
								$objConfig->template = $this->com_template;
								$objConfig->requireLogin = $objArchive->requireLogin;
								$objConfig->disableCaptcha = $objArchive->disableCaptcha;
								$objConfig->bbcode = $objArchive->bbcode;
								$objConfig->moderate = $objArchive->moderate;

								$objComments->addCommentsToTemplate($this->Template, $objConfig,
									$this->formHybridDataContainer, $objItem->id, $arrNotifies);
							}
						}

						$strItem = $this->replaceInsertTags($this->parseItem($objItem));
						
						if ((\Environment::get('isAjaxRequest') && \Input::get('scope') == 'modal') || $this->useModalWrapperSync)
						{
							if (\Input::post('FORM_SUBMIT') == 'com_'. $this->formHybridDataContainer .'_'. $objItem->id)
							{
								if (\Input::post('reload'))
								{
									die();
								}
								else
								{
									$this->Template->item = $strItem;
									die($this->Template->parse());
								}
							}
							else
							{
								$objModalWrapper = new \FrontendTemplate($this->modalTpl ?: 'formhybrid_reader_modal_bootstrap');
								$objModalWrapper->setData($objItem->row() + $this->arrData);
								$this->Template->item = $strItem;
								$objModalWrapper->item = $this->Template->parse();

								// active modal from synchronous request
								if(!$this->useModalWrapperSync)
								{
									$objModalWrapper->active = true;
									die($objModalWrapper->parse());
								}

								$this->Template->item = $objModalWrapper->parse();
							}
						}
						else
						{
							$this->Template->item = $strItem;
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

			$varValue = FormSubmission::prepareSpecialValueForPrint($varValue, $this->dca['fields'][$strField],
				$this->formHybridDataContainer, $objDc, $objItemTmp);
			$objItemTmp->{$strField} = FormHelper::escapeAllEntities($this->formHybridDataContainer, $strField, $varValue);
		}

		if ($this->publishedField)
		{
			$objItemTmp->isPublished = ($this->invertPublishedField ?
					!$objItemTmp->{$this->publishedField} : $objItemTmp->{$this->publishedField});
		}

		$objTemplate = new \FrontendTemplate($this->itemTemplate ?: 'formhybrid_reader_default');

		// items contain module and item params (higher priority: item)
		$objTemplate->setData($objItemTmp->row() + $this->arrData);
		$objTemplate->class = $strClass;
		$objTemplate->formHybridDataContainer = $this->formHybridDataContainer;
		$objTemplate->useDummyImage = $this->useDummyImage;
		$objTemplate->dummyImage = $this->dummyImage;
        $objTemplate->imgSize                 = deserialize($this->imgSize, true);

        $this->runBeforeTemplateParsing($objTemplate, $objItemTmp);

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

	protected function runBeforeTemplateParsing($objTemplate, $objItem) {}

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
