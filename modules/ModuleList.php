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

use HeimrichHannot\FormHybrid\DC_Hybrid;
use HeimrichHannot\FormHybrid\FormHelper;
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

		return parent::generate();
	}

	protected function compile()
	{
		$this->Template->headline = $this->headline;
		$this->Template->hl = $this->hl;
		$this->strTemplate = $this->customTpl ?: $this->strTemplate;
		$this->arrSkipInstances = deserialize($this->skipInstances, true);
		$this->arrEditable = deserialize($this->formHybridEditable, true);
		$this->addDefaultValues = $this->formHybridAddDefaultValues;
		$this->arrDefaultValues = deserialize($this->formHybridDefaultValues, true);
		$this->Template->currentSorting = $this->getCurrentSorting();
		$this->addColumns();

		// set initial filters
		$this->initInitialFilters();

		// set default filter values
		if ($this->addDefaultValues)
		{
			$this->applyDefaultFilters();
		}

		$this->Template->header = $this->getHeader();

		if (!$this->hideFilter)
		{
			$objFilterForm = new ListFilterForm($this->objModel);
			$this->Template->filterForm = $objFilterForm->generate();
		}

		if (!$this->hideFilter && $objFilterForm->isSubmitted() && !$objFilterForm->doNotSubmit())
		{
			// submission ain't formatted
			list($objItems, $this->Template->count) = $this->getItems($objFilterForm->getSubmission(false));
		}
		else
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
		FormHybridListModel::setAdditionalSql($this->additionalSql);

		if ($this->additionalSql)
			FormHybridListModel::setAdditionalGroupBy("$this->formHybridDataContainer.id");

		// set filter values
		if (!$this->hideFilter && $objFilterSubmission)
		{
			$arrFilterFields = $this->formHybridEditable;
			if ($this->addCustomFilterFields)
			{
				$arrFilterFields = $this->customFilterFields;
			}

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
			($this->formHybridDataContainer . '.' . $arrCurrentSorting['order'] . ' ' . strtoupper($arrCurrentSorting['sort']));

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
			$arrItem['detailsUrl'] = Environment::addParametersToUri(
				\Controller::generateFrontendUrl($objPageJumpTo->row()),
				array(
					'act' => FRONTENDEDIT_ACT_SHOW,
					'id'  => $objItem->id
				)
			);
		}
	}

	protected function generateFields($objItem)
	{
		$arrItem = array();

		// always add id
		$arrItem['fields']['id'] = $objItem->id;

		foreach ($this->arrEditable as $strName)
		{
			$arrItem['fields'][$strName] = static::getFormattedValueByDca($objItem->{$strName}, $this->dca['fields'][$strName]);
			$arrItem['fields'][$strName] = FormHelper::xssClean($arrItem['fields'][$strName]);
		}

		// add raw values
		foreach ($GLOBALS['TL_DCA'][$this->formHybridDataContainer]['fields'] as $strField => $arrData)
		{
			$arrItem['raw'][$strField] = $objItem->{$strName};
		}

		if ($this->publishedField)
		{
			$arrItem['isPublished'] = ($this->invertPublishedField ?
				!$objItem->{$this->publishedField} : $objItem->{$this->publishedField});
		}

		return $arrItem;
	}

	public static function getFormattedValueByDca($value, $arrData)
	{
		$value = deserialize($value);
		$rgxp = $arrData['eval']['rgxp'];
		$opts = $arrData['options'];
		$rfrc = $arrData['reference'];

		$rgxp = $arrData['eval']['rgxp'];

		// Call the options_callback to get the formated value
		if ($rgxp == 'date')
		{
			$value = \Date::parse(\Config::get('dateFormat'), $value);
		}
		elseif ($rgxp == 'time')
		{
			$value = \Date::parse(\Config::get('timeFormat'), $value);
		}
		elseif ($rgxp == 'datim')
		{
			$value = \Date::parse(\Config::get('datimFormat'), $value);
		}
		elseif (is_array($value))
		{
			$value = static::flattenArray($value);

			$value = array_filter($value); // remove empty elements

			$value = implode(', ', array_map(function($value) use ($rfrc) {
				if (is_array($rfrc))
				{
					return isset($rfrc[$value]) ? ((is_array($rfrc[$value])) ? $rfrc[$value][0] : $rfrc[$value]) : $value;
				}
				else
					return $value;
			}, $value));
		}
		elseif (is_array($opts) && array_is_assoc($opts))
		{
			$value = isset($opts[$value]) ? $opts[$value] : $value;
		}
		elseif (is_array($rfrc))
		{
			$value = isset($rfrc[$value]) ? ((is_array($rfrc[$value])) ? $rfrc[$value][0] : $rfrc[$value]) : $value;
		}
		elseif ($arrData['inputType'] == 'fileTree')
		{
			if ($arrData['eval']['multiple'] && is_array($value))
			{
				$value = array_map(function($val) {
					$strPath = Files::getPathFromUuid($val);
					return $strPath ?: $val;
				}, $value);
			}
			else
			{
				$strPath = Files::getPathFromUuid($value);
				$value = $strPath ?: $value;
			}
		}
		elseif (\Validator::isBinaryUuid($value))
		{
			$value = \String::binToUuid($value);
		}

		// Convert special characters (see #1890)
		return $value;
	}

	public static function flattenArray(array $array)
	{
		$return = array();
		array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
		return $return;
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
			$arrResult[] = $this->parseItem($arrItem, 'item' . '_' . ++$count . (($count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % 2) == 0) ? ' odd' : ' even'), $count);
		}

		return $arrResult;
	}

	protected function parseItem($arrItem, $strClass='', $intCount=0)
	{
		$objTemplate = new \FrontendTemplate($this->itemTemplate);

		$objTemplate->setData($arrItem);
		$objTemplate->class = $strClass;
		$objTemplate->count = $intCount;
		$objTemplate->formHybridDataContainer = $this->formHybridDataContainer;
		$objTemplate->addDetailsCol = $this->addDetailsCol;
		$objTemplate->module = $this;
		$objTemplate->imgSize = deserialize($this->imgSize, true);

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

		return $objTemplate->parse();
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
							return '"' . $value . '"';
						}, $arrFilterGroups)) . ')';
			}
		} elseif ($this->filterArchives) // archives
		{
			$arrFilterArchives = deserialize($this->filterArchives, true);

			if (!empty($arrFilterArchives))
			{
				$this->arrColumns['pid'] = 'pid IN (' . implode(',', $arrFilterArchives) . ')';
			}
		}

		// hide unpublished
		if ($this->hideUnpublishedItems)
		{
			$this->arrColumns[$this->publishedField] = $this->publishedField . '=' . ($this->invertPublishedField ? '0' : '1');
		}
	}

	protected function applyDefaultFilters()
	{
		foreach ($this->arrDefaultValues as $arrDefaultValue)
		{
			$strField = $arrDefaultValue['field'];
			$varValue = deserialize($arrDefaultValue['value']);

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

				$this->arrColumns[$strField] = $strColumn;
			}
			else
			{
				$this->arrColumns[$strField] = $strField . '=?';
				$this->arrValues[$strField] = $this->replaceInsertTags($varValue);
			}
		}
	}

	protected function applyFilters($objFilterSubmission, $arrFilterFields)
	{
		foreach ($objFilterSubmission->row() as $strField => $varValue)
		{
			if (in_array($strField, deserialize($arrFilterFields, true)))
			{
				if (trim($varValue))
				{
					switch ($GLOBALS['TL_DCA'][$this->formHybridDataContainer]['fields'][$strField]['inputType'])
					{
						case 'tag':
							$arrTags = explode(',', $varValue);
							$strColumn = '';
							foreach ($arrTags as $i => $strTag)
							{
								if ($i == 0)
									$strColumn .= "tl_tag.tag='$strTag'";
								else
									$strColumn .= " OR tl_tag.tag='$strTag'";
							}

							$this->arrColumns[$strField] = $strColumn;
							break;
						case 'text':
						case 'textarea':
						case 'password':
							$this->arrColumns[$strField] = $strField . " LIKE ?";
							$this->arrValues[$strField] = $this->replaceInsertTags('%' . $varValue . '%');
							break;
						default:
							$this->arrColumns[$strField] = $strField . '=?';
							$this->arrValues[$strField] = $this->replaceInsertTags($varValue);
							break;
					}
				}
			}
		}
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
				$arrCurrentSorting = array(
					'order' => preg_replace('@(.*)_(asc|desc)@i', '$1', $this->itemSorting),
					'sort' => (strpos($this->itemSorting, '_desc') !== false ? 'desc' : 'asc')
				);
		}
		// default -> the first editable field
		else
		{
			$arrCurrentSorting = array(
				'order' => $this->arrEditable[0],
				'sort' => 'asc'
			);
		}

		return $arrCurrentSorting;
	}

	protected function getHeader()
	{
		$arrHeader = array();
		$arrCurrentSorting = $this->getCurrentSorting();

		foreach ($this->arrEditable as $strName)
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
			$objPagination = new \Pagination($total, $this->perPage, $GLOBALS['TL_CONFIG']['maxPaginationLinks'], $id);
			$this->Template->pagination = $objPagination->generate("\n  ");
		}

		return array($offset, $limit);
	}

}
