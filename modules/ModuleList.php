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

use Haste\Util\Url;
use HeimrichHannot\FormHybrid\FormHelper;
use HeimrichHannot\Haste\Dca\General;
use HeimrichHannot\Haste\Util\Arrays;
use HeimrichHannot\Haste\Util\FormSubmission;
use HeimrichHannot\HastePlus\Environment;
use HeimrichHannot\HastePlus\Files;

class ModuleList extends \Module
{
	protected $strTemplate = 'mod_formhybrid_list';
	protected $arrSkipInstances = array();
	protected $arrItems = array();
	protected $objItems;
	protected $arrInitialFilter = array();
	protected $arrColumns = array();
	protected $arrValues = array();
	protected $arrOptions = array();
	protected $arrSkippedFilterFields = array();
	protected $arrDisjunctionFieldGroups = array();
	protected $arrDisjunctionFieldGroupsColumns = array();
	protected $objFilterForm;
	protected $strWrapperId = 'formhybrid-list_';
	protected $strWrapperClass = 'formhybrid-list';

	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### FORMHYBRID LIST ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		\DataContainer::loadDataContainer($this->formHybridDataContainer);
		\System::loadLanguageFile($this->formHybridDataContainer);

		$this->dca = $GLOBALS['TL_DCA'][$this->formHybridDataContainer];

		$this->strWrapperId .= $this->id;

		return parent::generate();
	}

	protected function compile()
	{
		$this->Template->headline = $this->headline;
		$this->Template->hl = $this->hl;
		$this->Template->wrapperClass = $this->strWrapperClass;
		$this->Template->wrapperId = $this->strWrapperId;
		$this->Template->addInfiniteScroll = $this->addInfiniteScroll;
		$this->strTemplate = $this->customTpl ?: ($this->isTableList ? 'mod_formhybrid_list_table' : $this->strTemplate);
		$this->arrSkipInstances = deserialize($this->skipInstances, true);
		$this->arrTableFields = deserialize($this->tableFields, true);
		$this->addDefaultValues = $this->formHybridAddDefaultValues;
		$this->arrDefaultValues = deserialize($this->formHybridDefaultValues, true);
		$this->arrConjunctiveMultipleFields = deserialize($this->conjunctiveMultipleFields, true);
		if ($this->addDisjunctiveFieldGroups)
		{
			$arrResult = array();

			foreach (deserialize($this->disjunctiveFieldGroups, true) as $intGroup => $arrGroup)
			{
				$arrResult[$intGroup] = $arrGroup['fields'];
			}

			$this->arrDisjunctionFieldGroups = $arrResult;
		}

		$this->Template->currentSorting = $this->getCurrentSorting();

		global $objPage;
		$this->listUrl = \Controller::generateFrontendUrl($objPage->row());

		if ($this->useModal)
		{
			$objModalWrapper = new \FrontendTemplate($this->modalWrapperTpl ?: 'formhybrid_reader_modal_wrapper_bootstrap');
			$objModalWrapper->setData($this->arrData);
			$this->Template->modalWrapper = $objModalWrapper->parse();
		}

		$this->addColumns();

		// set initial filters
		$this->initInitialFilters();

		// set default filter values
		if ($this->addDefaultValues)
		{
			$this->applyDefaultFilters();
		}

		if ($this->hasHeader)
			$this->Template->header = $this->getHeader();

		if (!$this->hideFilter)
		{
			$this->objFilterForm = new ListFilterForm($this->objModel,  $this);
			$this->Template->filterForm = $this->objFilterForm->generate();
		}

		if (!$this->hideFilter && $this->objFilterForm->isSubmitted() && !$this->objFilterForm->doNotSubmit())
		{
			// submission ain't formatted
			list($objItems, $this->Template->count) = $this->getItems($this->objFilterForm->getSubmission(false));
			$this->Template->isSubmitted = $this->objFilterForm->isSubmitted();
		}
		elseif ($this->showInitialResults)
		{
			list($objItems, $this->Template->count) = $this->getItems();
		}

		// Add the items
		if ($objItems !== null)
		{
			$this->Template->items = $this->parseItems($objItems);
		}
	}

	protected function getItems($objFilterSubmission = null)
	{
		// IMPORTANT: set the table for the generic model class
		FormHybridListModel::setTable($this->formHybridDataContainer);
		FormHybridListModel::setAdditionalWhereSql($this->replaceInsertTags($this->additionalWhereSql));
		FormHybridListModel::setAdditionalSelectSql($this->additionalSelectSql);
		FormHybridListModel::setAdditionalSql($this->additionalSql);

		if ($this->additionalSql)
			FormHybridListModel::setAdditionalGroupBy("$this->formHybridDataContainer.id");

		// set filter values
		if (!$this->hideFilter && $objFilterSubmission)
		{
			$arrFilterFields = $this->customFilterFields;

			$this->applyFilters($objFilterSubmission, $arrFilterFields);
		}

		// offset
		$offset = intval($this->skipFirst);

		// limit
		$limit = null;
		if ($this->numberOfItems > 0)
		{
			$limit = $this->numberOfItems;
		}

		// total number of items
		if (count($this->arrColumns) > 0)
			$this->objItems = FormHybridListModel::findBy($this->arrColumns, $this->arrValues, $this->arrOptions);
		else
			$this->objItems = FormHybridListModel::findAll($this->arrOptions);

		// TODO: write a count method that works with GROUP BY
		$intTotal = 0;
		if ($this->objItems !== null)
			$intTotal = $this->objItems->count();

		$this->Template->empty = false;

		if ($intTotal < 1)
		{
			$this->Template->empty = true;
			$this->Template->emptyText = $this->emptyText ?: $GLOBALS['TL_LANG']['formhybrid_list']['empty'];

			foreach ($this->arrColumns as $strColumn)
			{
				$this->Template->emptyText = str_replace('##$strColumn##', $this->arrValues[$strColumn], $this->Template->emptyText);
			}
		}

		// split results
		list($offset, $limit) = $this->splitResults($offset, $intTotal, $limit);

		$this->arrOptions['limit']  = $limit;
		$this->arrOptions['offset'] = $offset;

		$arrCurrentSorting = $this->getCurrentSorting();

		$this->arrOptions['order']  = ($arrCurrentSorting['order'] === 'random') ? 'RAND()' :
				(($this->sortingMode == OPTION_FORMHYBRID_SORTINGMODE_TEXT ? '' : $this->formHybridDataContainer . '.') .
						$arrCurrentSorting['order'] . ' ' . strtoupper($arrCurrentSorting['sort']));

		// Get the items
		if (count($this->arrColumns) > 0)
			$this->objItems = FormHybridListModel::findBy($this->arrColumns, $this->arrValues, $this->arrOptions);
		else
			$this->objItems = FormHybridListModel::findAll($this->arrOptions);

		// IMPORTANT: remove additional sql from the model
		FormHybridListModel::removeAdditionalSql();
		FormHybridListModel::removeAdditionalGroupBy();

		if ($this->objItems !== null)
		{
			while ($this->objItems->next())
			{
				$arrItem = $this->generateFields($this->objItems);

				$this->addItemColumns($this->objItems, $arrItem);

				$this->arrItems[] = $arrItem;
			}
		}
		else
		{
			$this->Template->empty = true;
			return array(array(), 0);
		}

		return array($this->arrItems, $intTotal);
	}

	public function addColumns() {}

	public function addItemColumns($objItem, &$arrItem)
	{
		// details url
		global $objPage;

		if (($objPageJumpTo = \PageModel::findByPk($this->jumpToDetails)) !== null || $objPageJumpTo = $objPage)
		{
			$arrItem['detailsUrl'] = \Controller::generateFrontendUrl(
					$objPageJumpTo->row(),
					'/' . General::getAliasIfAvailable($objItem)
			);
		}

		$arrItem['listUrl'] = $this->listUrl;
	}

	protected function generateFields($objItem)
	{
		$arrItem = array();
		$arrDca = &$GLOBALS['TL_DCA'][$this->formHybridDataContainer];

		// always add id
		$arrItem['fields']['id'] = $objItem->id;

		$objDc = new \DC_Table($this->formHybridDataContainer);
		$objDc->activeRecord = $objItem;

		if ($this->isTableList)
		{
			foreach ($this->arrTableFields as $strField)
			{
				$arrItem['fields'][$strField] = FormSubmission::prepareSpecialValueForPrint($objItem->{$strField},
					$this->dca['fields'][$strField], $this->formHybridDataContainer, $objDc, $objItem);

				if (is_array($arrDca['fields'][$strField]['load_callback'])) {
					foreach ($arrDca['fields'][$strField]['load_callback'] as $callback) {
						$this->import($callback[0]);
						$arrItem['fields'][$strField] = $this->$callback[0]->$callback[1]($arrItem['fields'][$strField], $this);
					}
				}

				// anti-xss: escape everything besides some tags
				$arrItem['fields'][$strField] = FormHelper::escapeAllEntities($this->formHybridDataContainer, $strField, $arrItem['fields'][$strField]);
			}
		}
		else
		{
			foreach ($arrDca['fields'] as $strField => $arrData)
			{
				$arrItem['fields'][$strField] = FormSubmission::prepareSpecialValueForPrint($objItem->{$strField},
					$this->dca['fields'][$strField], $this->formHybridDataContainer, $objDc, $objItem);

				// anti-xss: escape everything besides some tags
				$arrItem['fields'][$strField] = FormHelper::escapeAllEntities($this->formHybridDataContainer, $strField, $arrItem['fields'][$strField]);
			}
		}

		// add raw values
		foreach ($GLOBALS['TL_DCA'][$this->formHybridDataContainer]['fields'] as $strField => $arrData)
		{
			$arrItem['raw'][$strField] = $objItem->{$strField};
		}

		if ($this->publishedField)
		{
			$arrItem['isPublished'] = ($this->invertPublishedField ?
					!$objItem->{$this->publishedField} : $objItem->{$this->publishedField});
		}

		return $arrItem;
	}

	protected function parseItems($arrItems)
	{
		$limit = count($arrItems);

		if ($limit < 1)
		{
			return array();
		}

		$count = 0;
		$arrResult = array();

		foreach ($arrItems as $arrItem)
		{
			$arrResult[] = $this->parseItem($arrItem, 'item item' . '_' . ++$count . (($count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % 2) == 0) ? ' odd' : ' even'), $count);
		}

		return $arrResult;
	}

	protected function parseItem($arrItem, $strClass='', $intCount=0)
	{
		return $this->getItem($arrItem, $strClass, $intCount)->parse();
	}

	protected function getItem($arrItem, $strClass='', $intCount=0)
	{
		$objTemplate = new \FrontendTemplate($this->itemTemplate ?: 'formhybrid_list_item_default');

		$objTemplate->setData($arrItem);
		$objTemplate->class = $strClass;
		$objTemplate->count = $intCount;
		$objTemplate->useModal = $this->useModal;
		$objTemplate->useDummyImage = $this->useDummyImage;
		$objTemplate->dummyImage = $this->dummyImage;
		$objTemplate->formHybridDataContainer = $this->formHybridDataContainer;
		$objTemplate->addDetailsCol = $this->addDetailsCol;
		$objTemplate->module = $this;
		$objTemplate->imgSize = deserialize($this->imgSize, true);
		$varIdAlias = ltrim(((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ? '/' : '/items/') .
				((!\Config::get('disableAlias') && $arrItem['raw']['alias'] != '') ? $arrItem['raw']['alias'] : $arrItem['raw']['id']), '/');
		$objTemplate->active = $varIdAlias && \Input::get('items') == $varIdAlias;

		$this->runBeforeTemplateParsing($objTemplate, $arrItem);

		// HOOK: add custom logic
		if (isset($GLOBALS['TL_HOOKS']['parseItems']) && is_array($GLOBALS['TL_HOOKS']['parseItems']))
		{
			foreach ($GLOBALS['TL_HOOKS']['parseItems'] as $callback)
			{
				$this->import($callback[0]);
				$this->$callback[0]->$callback[1]($objTemplate, $arrItem, $this);
			}
		}

		return $objTemplate;
	}

	protected function runBeforeTemplateParsing($objTemplate, $arrItem) {}

	protected function initInitialFilters()
	{
		// filters

		// groups
		if ($this->filterGroups)
		{
			$arrFilterGroups = deserialize($this->filterGroups, true);

			if (!empty($arrFilterGroups))
			{
				$this->arrColumns['groups'] = 'groups REGEXP (' . implode('|', array_map(function($value) {
							return '\'"' . $value . '"\'';
						}, $arrFilterGroups)) . ')';
			}
		} elseif ($this->filterArchives) // archives
		{
			$arrFilterArchives = deserialize($this->filterArchives, true);

			if (!empty($arrFilterArchives))
			{
				$this->arrColumns['pid'] = $this->formHybridDataContainer . '.pid IN (' . implode(',', $arrFilterArchives) . ')';
			}
		}

		// hide unpublished
		if ($this->hideUnpublishedItems)
		{
			$this->arrColumns[$this->publishedField] = $this->formHybridDataContainer . '.' . $this->publishedField . '=' . ($this->invertPublishedField ? '0' : '1');
		}
	}

	protected function applyDefaultFilters()
	{
		foreach ($this->arrDefaultValues as $arrDefaultValue)
		{
			$strField = $arrDefaultValue['field'];
			$varValue = deserialize($arrDefaultValue['value']);
			$blnSkipValue = false;

			// special handling for tags
			if (in_array('tags', \ModuleLoader::getActive()) &&
					$GLOBALS['TL_DCA'][$this->formHybridDataContainer]['fields'][$strField]['inputType'] == 'tag')
			{
				$arrTags = explode(',', $varValue);
				$strColumn = '';

				foreach ($arrTags as $i => $strTag)
				{
					if ($i == 0)
						$strColumn .= "tl_tag.tag='$strTag'";
					else
						$strColumn .= " OR tl_tag.tag='$strTag'";
				}

				$blnSkipValue = true;
			}
			else
			{
				$strColumn = $strField . '=?';
				$varValue = $this->replaceInsertTags($varValue);
			}

			$this->customizeDefaultFilters($strField, $strColumn, $varValue, $blnSkipValue);

			$this->doApplyDefaultFilters($strField, $strColumn, $varValue, $blnSkipValue);
		}
	}

	protected function customizeDefaultFilters(&$strField, &$strColumn, &$varValue, &$blnSkipValue) {}

	protected function doApplyDefaultFilters($strField, $strColumn, $varValue, $blnSkipValue = false)
	{
		$this->arrColumns[$strField] = $strColumn;
		if (!$blnSkipValue)
			$this->arrValues[$strField] = $varValue;
	}

	protected function applyFilters($objFilterSubmission, $arrFilterFields)
	{
		if ($this->addDisjunctiveFieldGroups)
		{
			// add empty values to processed columns
			foreach ($this->arrDisjunctionFieldGroups as $intPosition => $arrGroup)
			{
				foreach ($arrGroup as $strFilterField)
				{
					if (!\Input::get($strFilterField))
					{
						$this->arrDisjunctionFieldGroupsColumns[$intPosition][$strFilterField] = null;
					}
				}
			}
		}

		foreach ($objFilterSubmission->row() as $strField => $varValue)
		{
			if (in_array($strField, $this->arrSkippedFilterFields))
				continue;

			if (in_array($strField, deserialize($arrFilterFields, true)))
			{
				if (is_array($varValue) || trim($varValue))
				{
					// remove existing values in order to keep the order
					if (isset($this->arrColumns[$strField]))
						unset($this->arrColumns[$strField]);

					if (isset($this->arrValues[$strField]))
						unset($this->arrValues[$strField]);

					$arrDca = $GLOBALS['TL_DCA'][$this->formHybridDataContainer]['fields'][$strField];

					$blnSkipValue = false;
					switch ($arrDca['inputType'])
					{
						case 'tag':
							$arrTags = explode(',', urldecode($varValue));
							$strColumn = '';
							foreach ($arrTags as $i => $strTag)
							{
								if ($i == 0)
									$strColumn .= "tl_tag.tag='$strTag'";
								else
									$strColumn .= " OR tl_tag.tag='$strTag'";
							}

							$blnSkipValue = true;
							break;
						case 'text':
						case 'textarea':
						case 'password':
							$strColumn = $strField . " LIKE ?";
							$varValue = $this->replaceInsertTags('%' . $varValue . '%');
							break;
						default:
							if ($arrDca['eval']['multiple'])
							{
								$strColumn = static::generateMultipleValueMatchSql($strField, $varValue,
										in_array($strField, $this->arrConjunctiveMultipleFields));
								$blnSkipValue = true;
							}
							else
							{
								$strColumn = $strField . '=?';
								$varValue = $this->replaceInsertTags($varValue);
							}
							break;
					}

					$this->customizeFilters($strField, $strColumn, $varValue, $blnSkipValue);

					if ($this->addDisjunctiveFieldGroups && ($intPosition = $this->getDisjunctionGroupIndex($strField)) > -1)
					{
						$this->arrDisjunctionFieldGroupsColumns[$intPosition][$strField] = $strColumn;

						if (!$blnSkipValue)
						{
							$this->arrValues[$strField] = $varValue;
						}

						$arrFields = $this->arrDisjunctionFieldGroups[$intPosition];
						sort($arrFields);
						$arrFieldsColumn = array_keys($this->arrDisjunctionFieldGroupsColumns[$intPosition]);
						sort($arrFieldsColumn);

						if ($arrFields == $arrFieldsColumn)
						{
							$this->arrColumns[$strField] = '(' . implode(' OR ', array_map(function($val) {
								return '(' . $val . ')';
							}, array_filter($this->arrDisjunctionFieldGroupsColumns[$intPosition]))) . ')';
						}
					}
					else
					{
						$this->doApplyFilters($strField, $strColumn, $varValue, $blnSkipValue);
					}
				}
			}
		}
	}

	protected function getDisjunctionGroupIndex($strField)
	{
		foreach ($this->arrDisjunctionFieldGroups as $intPosition => $arrGroup)
		{
			if (in_array($strField, $arrGroup))
				return $intPosition;
		}

		return -1;
	}

	protected function customizeFilters(&$strField, &$strColumn, &$varValue, &$blnSkipValue) {}

	protected function doApplyFilters($strField, $strColumn, $varValue, $blnSkipValue = false)
	{
		$this->arrColumns[$strField] = $strColumn;
		if (!$blnSkipValue)
			$this->arrValues[$strField] = $varValue;
	}

	protected function getCurrentSorting()
	{
		// user specified
		if (\Input::get('order') && \Input::get('sort'))
		{
			$arrCurrentSorting = array(
					'order' => \Input::get('order'),
					'sort' => \Input::get('sort')
			);
		}
		// initial
		elseif ($this->itemSorting)
		{
			if ($this->itemSorting == 'random')
				$arrCurrentSorting = array(
					'order' => 'random'
				);
			else
			{
				$arrCurrentSorting = array(
					'order' => preg_replace('@(.*)_(asc|desc)@i', '$1', $this->itemSorting),
					'sort' => (strpos($this->itemSorting, '_desc') !== false ? 'desc' : 'asc')
				);
			}
		}
		// default -> the first table field
		else
		{
			$arrCurrentSorting = array(
				'order' => $this->arrTableFields[0],
				'sort' => 'asc'
			);
		}

		return $arrCurrentSorting;
	}

	protected function getHeader()
	{
		$arrHeader = array();
		$arrCurrentSorting = $this->getCurrentSorting();

		foreach ($this->arrTableFields as $strName)
		{
			$isCurrentOrderField = ($strName == $arrCurrentSorting['order']);

			$arrField = array(
					'field' => $strName
			);

			if ($isCurrentOrderField)
			{
				$arrField['class'] = ($arrCurrentSorting['sort'] == 'asc' ? 'asc' : 'desc');
				$arrField['link'] = Environment::addParametersToUri(Environment::getUrl(), array(
						'order' => $strName,
						'sort' => ($arrCurrentSorting['sort'] == 'asc' ? 'desc' : 'asc')
				));
			}
			else
			{
				$arrField['link'] = Environment::addParametersToUri(Environment::getUrl(), array(
						'order' => $strName,
						'sort' => 'asc'
				));
			}

			$arrHeader[] = $arrField;
		}

		return $arrHeader;
	}

	protected function splitResults($offset, $intTotal, $limit)
	{
		$total = $intTotal - $offset;

		// Split the results
		if ($this->perPage > 0 && (!isset($limit) || $this->numberOfItems > $this->perPage))
		{
			// Adjust the overall limit
			if (isset($limit))
			{
				$total = min($limit, $total);
			}

			// Get the current page
			$id = 'page_s' . $this->id;
			$page = \Input::get($id) ?: 1;

			// Do not index or cache the page if the page number is outside the range
			if ($page < 1 || $page > max(ceil($total/$this->perPage), 1))
			{
				global $objPage;
				$objPage->noSearch = 1;
				$objPage->cache = 0;

				// Send a 404 header
				header('HTTP/1.1 404 Not Found');
				return;
			}

			// Set limit and offset
			$limit = $this->perPage;
			$offset += (max($page, 1) - 1) * $this->perPage;

			// Overall limit
			if ($offset + $limit > $total)
			{
				$limit = $total - $offset;
			}

			// Add the pagination menu
			if ($this->addAjaxPagination)
			{
				$objPagination = new \Pagination(
					$total, $this->perPage, $GLOBALS['TL_CONFIG']['maxPaginationLinks'], $id, new \FrontendTemplate('pagination_ajax')
				);
			}
			else
			{
				$objPagination = new \Pagination(
					$total, $this->perPage, $GLOBALS['TL_CONFIG']['maxPaginationLinks'], $id
				);
			}

			$this->Template->pagination = $objPagination->generate("\n  ");
		}

		return array($offset, $limit);
	}

	protected function addImage($objItem, $strField, &$arrItem)
	{
		if (is_array($objItem))
			$objItem = Arrays::arrayToObject($objItem);

		if ($objItem->addImage && $objItem->{$strField} != '')
		{
			$objModel = \FilesModel::findByUuid($objItem->{$strField});

			if ($objModel === null)
			{
				if (!\Validator::isUuid($objItem->{$strField}))
				{
					$arrItem['fields']['text'] = '<p class="error">'.$GLOBALS['TL_LANG']['ERR']['version2format'].'</p>';
				}
			}
			elseif (is_file(TL_ROOT . '/' . $objModel->path))
			{
				// Override the default image size
				if ($this->imgSize != '')
				{
					$size = deserialize($this->imgSize);

					if ($size[0] > 0 || $size[1] > 0)
					{
						$arrItem['fields']['size'] = $this->imgSize;
					}
				}

				$arrItem['fields']['singleSRC'] = $objModel->path;
				$arrItem['fields']['addImage'] = true;
				// addToImage is done in runBeforeTemplateParsing()
			}
		}
	}

	public function modifyDC(&$arrDca = null) {}

	public static function generateMultipleValueMatchSql($strField, $varValue, $blnConjunction = false)
	{
		if ($blnConjunction)
		{
			$arrResult = '';
			foreach ($varValue as $strOption)
			{
				$arrResult[] = '(' . $strField . '="' . $strOption . '" OR ' . $strField . ' REGEXP ("\"' . $strOption . '\""))';
			}
			$strQuery = implode(' AND ', $arrResult);
		}
		else
		{
			$arrValueIn = array_map(function($val) {
				return '"' . \Controller::replaceInsertTags($val) . '"';
			}, $varValue);

			$arrValueRegExp = array_map(function($val) {
				return '\"' . \Controller::replaceInsertTags($val) . '\"';
			}, $varValue);

			$strQuery = '(' . $strField . ' IN (' . implode(',', $arrValueIn) . ') OR ' . $strField . ' REGEXP ("' . implode('|', $arrValueRegExp) . '"))';
		}

		return $strQuery;
	}

}
