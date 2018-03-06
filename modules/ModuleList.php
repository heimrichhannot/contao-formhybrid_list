<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) Heimrich & Hannot GmbH
 *
 * @package formhybrid_list
 * @author  Dennis Patzer
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\FormHybridList;

use Contao\Controller;
use Contao\Database;
use Contao\StringUtil;
use Haste\DateTime\DateTime;
use HeimrichHannot\Blocks\BlockModuleModel;
use HeimrichHannot\Haste\Database\QueryHelper;
use HeimrichHannot\Haste\Dca\DC_HastePlus;
use HeimrichHannot\Haste\Util\Url;
use HeimrichHannot\FormHybrid\FormHelper;
use HeimrichHannot\Haste\Dca\General;
use HeimrichHannot\Haste\Util\Arrays;
use HeimrichHannot\Haste\Util\FormSubmission;
use HeimrichHannot\HastePlus\Environment;
use HeimrichHannot\Modal\ModalModel;
use HeimrichHannot\Request\Request;
use HeimrichHannot\StatusMessages\StatusMessage;

class ModuleList extends \Module
{
    protected $arrSkipInstances                 = [];
    protected $arrItems                         = [];
    protected $objItems;
    protected $objItemsComplete;
    protected $arrInitialFilter                 = [];
    protected $arrColumns                       = [];
    protected $arrValues                        = [];
    protected $arrOptions                       = [];
    protected $arrSkippedFilterFields           = [];
    protected $arrDisjunctionFieldGroups        = [];
    protected $arrDisjunctionFieldGroupsColumns = [];
    protected $objFilterForm;
    protected $strWrapperId                     = 'formhybrid-list_';
    protected $strWrapperClass                  = 'formhybrid-list';

    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate           = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD'][$this->type][0] ?: $this->type) . ' ###';
            $objTemplate->title    = $this->headline;
            $objTemplate->id       = $this->id;
            $objTemplate->link     = $this->name;
            $objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        \DataContainer::loadDataContainer($this->formHybridDataContainer);
        \System::loadLanguageFile($this->formHybridDataContainer);

        $this->dca = $GLOBALS['TL_DCA'][$this->formHybridDataContainer];

        $this->strWrapperId .= $this->id;

        $this->strTemplate  = $this->customTpl ?: ($this->strTemplate ?: ($this->isTableList ? 'mod_formhybrid_list_table' : 'mod_formhybrid_list'));
        $this->itemTemplate = $this->itemTemplate ?: ($this->isTableList ? 'formhybrid_list_item_table_default' : 'formhybrid_list_item_default');

        $x = parent::generate();

        return $x;
    }

    protected function compile()
    {
        $strAct = \Input::get('act');

        if ($this->formHybridLinkedFilter && ($objFilterModule = \ModuleModel::findByPk($this->formHybridLinkedFilter)) !== null && $this->filterMode == OPTION_FORMHYBRID_FILTERMODE_MODULE) {
            $this->objFilterContext = $objFilterModule;
            $this->objFilterForm      = new ListFilterForm($this);
        }
        else
        {
            $this->objFilterContext = $this;
            
            if (!$this->hideFilter) {
                $this->objFilterForm        = new ListFilterForm($this);
                $this->Template->filterForm = $this->objFilterForm->generate();
            }
        }

        if ($this->objFilterContext->addProximitySearch) {
            $arrFilterFields = deserialize($this->objFilterContext->customFilterFields, true);

            if (!in_array(FormHybridList::PROXIMITY_SEARCH_LOCATION, $arrFilterFields)) {
                $arrFilterFields[]        = FormHybridList::PROXIMITY_SEARCH_LOCATION;
                $this->objFilterContext->customFilterFields = serialize($arrFilterFields);
            }
        }

        if ($strAct == FormHybridList::ACT_SHARE && $this->addShareCol) {
            $strUrl = \Input::get('url');
            $intId  = \Input::get($this->formHybridIdGetParameter);

            $strModelClass = \Model::getClassFromTable($this->formHybridDataContainer);
            if (($objEntity = $strModelClass::findByPk($intId)) !== null) {
                $intNow = time();

                if (FormHybridList::shareTokenExpiredOrEmpty($objEntity, $intNow)) {
                    $strShareToken             = str_replace('.', '', uniqid('', true));
                    $objEntity->shareToken     = $strShareToken;
                    $objEntity->shareTokenTime = $intNow;
                    $objEntity->save();
                }

                if ($this->shareAutoItem) {
                    $strShareUrl = $strUrl . '/' . $objEntity->shareToken;
                } else {
                    $strShareUrl = Url::addQueryString('share=' . $objEntity->shareToken, $strUrl);
                }

                die($strShareUrl);
            }
        }

        $this->Template->headline          = $this->headline;
        $this->Template->hl                = $this->hl;
        $this->Template->wrapperClass      = $this->strWrapperClass;
        $this->Template->wrapperId         = $this->strWrapperId;
        $this->Template->addInfiniteScroll = $this->addInfiniteScroll;

        if ($this->addMasonry) {
            $this->Template->addMasonry = true;
            $arrStamps                  = [];

            foreach (deserialize($this->masonryStampContentElements, true) as $arrStamp) {
                $arrStamps[] = [
                    'content' => BlockModuleModel::generateContent($arrStamp['stampBlock']),
                    'class'   => $arrStamp['stampCssClass']
                ];
            }

            $this->Template->masonryStampContentElements = $arrStamps;
        }

        $this->arrSkipInstances             = deserialize($this->skipInstances, true);
        $this->arrTableFields               = deserialize($this->tableFields, true);
        $this->addDefaultValues             = $this->objFilterContext->formHybridAddDefaultValues;
        $this->arrDefaultValues             = deserialize($this->objFilterContext->formHybridDefaultValues, true);
        $this->arrConjunctiveMultipleFields = deserialize($this->objFilterContext->conjunctiveMultipleFields, true);

        $this->addDataAttributes();

        if ($this->objFilterContext->addDisjunctiveFieldGroups) {
            $arrResult = [];

            foreach (deserialize($this->objFilterContext->disjunctiveFieldGroups, true) as $intGroup => $arrGroup) {
                $arrResult[$intGroup] = $arrGroup['fields'];
            }

            $this->arrDisjunctionFieldGroups = $arrResult;
        }

        $this->Template->currentSorting = $this->getCurrentSorting();

        global $objPage;
        $this->listUrl = \Controller::generateFrontendUrl($objPage->row());

        $this->addColumns();

        // set initial filters
        $this->initInitialFilters();

        // entity filter
        $this->initEntityFilter();

        // set default filter values
        if ($this->addDefaultValues || $this->saveFilterToSession) {
            $this->applyDefaultFilters();
        }

        if ($this->hasHeader) {
            $this->Template->header = $this->getHeader();
        }

        if (!$this->objFilterContext->hideFilter && $this->objFilterForm->isSubmitted() && !$this->objFilterForm->doNotSubmit()
        ) {
            // submission ain't formatted
            list($objItems, $this->Template->count) = $this->getItems($this->objFilterForm->getSubmission(false));
            $this->Template->isSubmitted = $this->objFilterForm->isSubmitted();
        } elseif ($this->showInitialResults) {
            list($objItems, $this->Template->count) = $this->getItems();
        }

        // Add the items
        if ($objItems !== null) {
            $this->Template->items = $this->parseItems($objItems);
        }
    }

    protected function addDataAttributes()
    {
        $arrData = [];

        if ($this->addInfiniteScroll) {
            $arrData[] = 'data-infinitescroll="1"';
        }

        if ($this->addMasonry) {
            $arrData[] = 'data-fhl-masonry="1"';
        }

        if ($this->addProximitySearch) {
            $arrData[] = 'data-fhl-prox-search="1"';

            if ($this->proximitySearchCityField) {
                $arrData[] = 'data-fhl-prox-search-city="' . $this->proximitySearchCityField . '"';
            }

            if ($this->proximitySearchPostalField) {
                $arrData[] = 'data-fhl-prox-search-postal="' . $this->proximitySearchPostalField . '"';
            }

            if ($this->proximitySearchStateField) {
                $arrData[] = 'data-fhl-prox-search-state="' . $this->proximitySearchStateField . '"';
            }

            if ($this->proximitySearchCountryField) {
                $arrData[] = 'data-fhl-prox-search-country="' . $this->proximitySearchCountryField . '"';
            }
        }

        $this->Template->configData = implode(' ', $arrData);
    }

    protected function getItems($objFilterSubmission = null)
    {
        // IMPORTANT: set the table for the generic model class
        FormHybridListModel::setTable($this->formHybridDataContainer);

        FormHybridListModel::setAdditionalWhereSql($this->replaceInsertTags($this->objFilterContext->additionalWhereSql, false));

        FormHybridListModel::setAdditionalSelectSql($this->replaceInsertTags($this->objFilterContext->additionalSelectSql, false));

        FormHybridListModel::setAdditionalHavingSql($this->replaceInsertTags($this->objFilterContext->additionalHavingSql, false));

        FormHybridListModel::setAdditionalSql($this->replaceInsertTags($this->objFilterContext->additionalSql, false));

        if ($this->objFilterContext->additionalSql) {
            FormHybridListModel::setAdditionalGroupBy("$this->formHybridDataContainer.id");
        }

        // set filter values
        if (!$this->objFilterContext->hideFilter && $objFilterSubmission) {
            $arrFilterFields = $this->objFilterContext->customFilterFields;

            $this->applyFilters($objFilterSubmission, $arrFilterFields);
        }

        // offset
        $offset = intval($this->skipFirst);

        // limit
        $limit = null;
        if ($this->numberOfItems > 0) {
            $limit = $this->numberOfItems;
        }

        // total number of items
        if (count($this->arrColumns) > 0) {
            $this->objItems = FormHybridListModel::findBy($this->arrColumns, $this->arrValues, $this->arrOptions);
        } else {
            $this->objItems = FormHybridListModel::findAll($this->arrOptions);
        }

        // save non paginated items
        $this->objItemsComplete = $this->objItems;
        
        // TODO: write a count method that works with GROUP BY
        $intTotal = 0;
        if ($this->objItems !== null) {
            $intTotal = $this->objItems->count();
        }

        $this->Template->empty = false;

        if ($intTotal < 1) {
            $this->Template->empty     = true;
            $this->Template->emptyText = $this->emptyText ?: $GLOBALS['TL_LANG']['formhybrid_list']['empty'];

            foreach ($this->arrColumns as $strColumn) {
                $this->Template->emptyText = str_replace('##$strColumn##', $this->arrValues[$strColumn], $this->Template->emptyText);
            }
        }

        // sorting
        $arrCurrentSorting = $this->getCurrentSorting();

        if ($arrCurrentSorting['order'] == 'random') {
            $intRandomSeed             = \Input::get(FormHybridList::PARAM_RANDOM) ?: rand(1, 500);
            $this->arrOptions['order'] = 'RAND("' . intval($intRandomSeed) . '")';
            list($offset, $limit) = $this->splitResults($offset, $intTotal, $limit, $intRandomSeed);
        } else {
            if (!empty($arrCurrentSorting)) {
                $this->arrOptions['order'] =
                    (($this->sortingMode == OPTION_FORMHYBRID_SORTINGMODE_TEXT ? '' : $this->formHybridDataContainer . '.')
                        . $arrCurrentSorting['order']
                        . ' ' . strtoupper($arrCurrentSorting['sort']));
            }

            list($offset, $limit) = $this->splitResults($offset, $intTotal, $limit);
        }

        
        // split the results
        $this->arrOptions['limit']  = $limit;
        $this->arrOptions['offset'] = $offset;

        // Get the items
        if (count($this->arrColumns) > 0) {
            $this->objItems = FormHybridListModel::findBy($this->arrColumns, $this->arrValues, $this->arrOptions);
        } else {
            $this->objItems = FormHybridListModel::findAll($this->arrOptions);
        }

        // IMPORTANT: remove additional sql from the model
        FormHybridListModel::removeAdditionalSql();
        FormHybridListModel::removeAdditionalGroupBy();

        if ($this->objItems !== null) {
            while ($this->objItems->next()) {
                $arrItem = $this->generateFields($this->objItems);

                $this->addItemColumns($this->objItems, $arrItem);

                $this->arrItems[] = $arrItem;
            }
        } else {
            $this->Template->empty = true;

            return [[], 0];
        }

        return [$this->arrItems,$intTotal];
    }

    public function addColumns()
    {
    }

    public function addItemColumns($objItem, &$arrItem)
    {
        global $objPage;

        // details url
        if (($objPageJumpTo = \PageModel::findByPk($this->jumpToDetails)) !== null || $objPageJumpTo = $objPage) {
            $arrItem['detailsUrl'] = \Controller::generateFrontendUrl(
                $objPageJumpTo->row(),
                '/' . General::getAliasIfAvailable($objItem)
            );
        }

        // share url
        $this->addShareColumn($objItem, $arrItem);

        $arrItem['listUrl'] = $this->listUrl;
    }

    public function addShareColumn($objItem, &$arrItem)
    {
        global $objPage;

        if (($objPageJumpTo = \PageModel::findByPk($this->jumpToShare)) !== null || $objPageJumpTo = $objPage) {
            $strShareUrl = \Environment::get('url') . '/' . \Controller::generateFrontendUrl($objPageJumpTo->row());

            $strUrl = Url::addQueryString('act=share', Url::getCurrentUrlWithoutParameters());
            $strUrl = Url::addQueryString('url=' . urlencode($strShareUrl), $strUrl);
            $strUrl = Url::addQueryString($this->formHybridIdGetParameter . '=' . $objItem->id, $strUrl);

            $arrItem['shareUrl'] = $strUrl;
        }
    }

    protected function generateFields($objItem)
    {
        $arrItem = [];
        $arrDca  = &$GLOBALS['TL_DCA'][$this->formHybridDataContainer];

        // always add id
        $arrItem['raw']['id'] = $objItem->id;

        $objDc               = new DC_HastePlus($this->formHybridDataContainer);
        $objDc->id           = $objItem->id;
        $objDc->activeRecord = $objItem;

        if ($this->isTableList) {
            foreach ($this->arrTableFields as $strField) {
                $arrItem['fields'][$strField] = FormSubmission::prepareSpecialValueForPrint(
                    $objItem->{$strField},
                    $this->dca['fields'][$strField],
                    $this->formHybridDataContainer,
                    $objDc,
                    $objItem
                );

                if (is_array($arrDca['fields'][$strField]['load_callback'])) {
                    foreach ($arrDca['fields'][$strField]['load_callback'] as $callback) {
                        $this->import($callback[0]);
                        $arrItem['fields'][$strField] = $this->{$callback[0]}->{$callback[1]}($arrItem['fields'][$strField], $objDc);
                    }
                }

                // anti-xss: escape everything besides some tags
                $arrItem['fields'][$strField] =
                    FormHelper::escapeAllEntities($this->formHybridDataContainer, $strField, $arrItem['fields'][$strField]);
            }
        } else {
            foreach ($arrDca['fields'] as $strField => $arrData) {
                $arrItem['fields'][$strField] = FormSubmission::prepareSpecialValueForPrint(
                    $objItem->{$strField},
                    $this->dca['fields'][$strField],
                    $this->formHybridDataContainer,
                    $objDc,
                    $objItem
                );

                // anti-xss: escape everything besides some tags
                $arrItem['fields'][$strField] =
                    FormHelper::escapeAllEntities($this->formHybridDataContainer, $strField, $arrItem['fields'][$strField]);
            }
        }

        // add raw values
        foreach ($GLOBALS['TL_DCA'][$this->formHybridDataContainer]['fields'] as $strField => $arrData) {
            $arrItem['raw'][$strField] = $objItem->{$strField};
        }

        if ($this->objFilterContext->publishedField) {
            $arrItem['isPublished'] = ($this->objFilterContext->invertPublishedField ? !$objItem->{$this->objFilterContext->publishedField} : $objItem->{$this->objFilterContext->publishedField});
        }

        return $arrItem;
    }

    protected function parseItems($arrItems)
    {
        $limit = count($arrItems);

        if ($limit < 1) {
            return [];
        }

        $count     = 0;
        $arrResult = [];

        foreach ($arrItems as $arrItem) {
            $arrResult[] = $this->parseItem(
                $arrItem,
                'item item' . '_' . ++$count . (($count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % 2)
                    == 0) ? ' odd' : ' even'),
                $count
            );
        }

        return $arrResult;
    }

    protected function parseItem($arrItem, $strClass = '', $intCount = 0)
    {
        return $this->getItem($arrItem, $strClass, $intCount)->parse();
    }

    protected function getItem($arrItem, $strClass = '', $intCount = 0)
    {
        $objTemplate = new \FrontendTemplate($this->itemTemplate);

        $objTemplate->setData($arrItem['fields']);

        foreach ($arrItem as $strKey => $varValue) {
            $objTemplate->{$strKey} = $varValue;
        }

        $objTemplate->class                   = $strClass;
        $objTemplate->count                   = $intCount;
        $objTemplate->useDummyImage           = $this->useDummyImage;
        $objTemplate->dummyImage              = $this->dummyImage;
        $objTemplate->formHybridDataContainer = $this->formHybridDataContainer;
        $objTemplate->addDetailsCol           = $this->addDetailsCol;
        $objTemplate->useModal                = $this->useModal;
        $objTemplate->jumpToDetails           = $this->jumpToDetails;

        global $objPage;

        if (($objPageJumpTo = \PageModel::findByPk($this->jumpToDetails)) !== null || $objPageJumpTo = $objPage) {
            if (($objModal = ModalModel::findPublishedByTargetPage($objPageJumpTo)) !== null) {
                $objTemplate->modal = $objModal;
            }
        }

        $objTemplate->addShareCol = $this->addShareCol;
        $objTemplate->module      = $this;
        $objTemplate->imgSize     = deserialize($this->imgSize, true);
        $varIdAlias               = ltrim(
            ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ? '/' : '/items/') . ((!\Config::get('disableAlias')
                && $arrItem['raw']['alias']
                != '') ? $arrItem['raw']['alias'] : $arrItem['raw']['id']),
            '/'
        );
        $objTemplate->idAlias     = $varIdAlias;
        $objTemplate->active      = $varIdAlias && \Input::get('items') == $varIdAlias;

        $this->runBeforeTemplateParsing($objTemplate, $arrItem);

        // HOOK: add custom logic
        if (isset($GLOBALS['TL_HOOKS']['parseItems']) && is_array($GLOBALS['TL_HOOKS']['parseItems'])) {
            foreach ($GLOBALS['TL_HOOKS']['parseItems'] as $callback) {
                $this->import($callback[0]);
                $this->{$callback[0]}->{$callback[1]}($objTemplate, $arrItem, $this);
            }
        }

        return $objTemplate;
    }

    protected function runBeforeTemplateParsing($objTemplate, $arrItem)
    {
    }

    protected function initInitialFilters()
    {
        $t = $this->formHybridDataContainer;

        // groups
        if ($this->objFilterContext->filterGroups) {
            $arrFilterGroups = deserialize($this->objFilterContext->filterGroups, true);

            if (!empty($arrFilterGroups)) {
                $this->arrColumns['groups'] = 'groups REGEXP (' . implode(
                        '|',
                        array_map(
                            function ($value) {
                                return '\'"' . $value . '"\'';
                            },
                            $arrFilterGroups
                        )
                    ) . ')';
            }
        } elseif ($this->objFilterContext->filterArchives) // archives
        {
            $arrFilterArchives = deserialize($this->objFilterContext->filterArchives, true);

            if (!empty($arrFilterArchives)) {
                $this->arrColumns['pid'] = $t . '.pid IN (' . implode(',', $arrFilterArchives) . ')';
            }
        }

        // hide unpublished
        if ($this->objFilterContext->hideUnpublishedItems) {
            $this->arrColumns[$this->objFilterContext->publishedField] =
                $t . '.' . $this->objFilterContext->publishedField . '=' . ($this->objFilterContext->invertPublishedField ? '0' : '1');

            if (Database::getInstance()->fieldExists('start', $t) &&
                Database::getInstance()->fieldExists('stop', $t))
            {
                $condition = $this->arrColumns[$this->objFilterContext->publishedField];

                $time = \Date::floorToMinute();
                $condition .= " AND ($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "')";

                $this->arrColumns[$this->objFilterContext->publishedField] = $condition;
            }
        }

        // check session if not logged in...
        if (!FE_USER_LOGGED_IN)
        {
            if (!$this->disableSessionCheck)
            {
                if (!\Database::getInstance()->fieldExists(General::PROPERTY_SESSION_ID, $this->formHybridDataContainer))
                {
                    throw new \Exception(
                        sprintf(
                            'No session field in %s available, either create field %s or set `disableSessionCheck` to true.',
                            $this->formHybridDataContainer,
                            General::PROPERTY_SESSION_ID
                        )
                    );
                }

                $this->arrColumns[General::PROPERTY_SESSION_ID] = $this->formHybridDataContainer . '.' . General::PROPERTY_SESSION_ID . '="' . session_id() . '"';
            }
        } // ...and check member id if logged in
        else
        {
            if (!$this->disableAuthorCheck)
            {
                if (!\Database::getInstance()->fieldExists(General::PROPERTY_AUTHOR_TYPE, $this->formHybridDataContainer))
                {
                    throw new \Exception(
                        sprintf(
                            'No session field in %s available, either create field %s or set `disableAuthorCheck` to true.',
                            $this->formHybridDataContainer,
                            General::PROPERTY_AUTHOR_TYPE
                        )
                    );
                }

                $this->arrColumns[General::PROPERTY_AUTHOR_TYPE] = $this->formHybridDataContainer . '.' . General::PROPERTY_AUTHOR_TYPE . '="' . General::AUTHOR_TYPE_MEMBER . '"';

                if (!\Database::getInstance()->fieldExists(General::PROPERTY_AUTHOR, $this->formHybridDataContainer))
                {
                    throw new \Exception(
                        sprintf(
                            'No session field in %s available, either create field %s or set `disableAuthorCheck` to true.',
                            $this->formHybridDataContainer,
                            General::PROPERTY_AUTHOR
                        )
                    );
                }

                $this->arrColumns[General::PROPERTY_AUTHOR] = $this->formHybridDataContainer . '.' . General::PROPERTY_AUTHOR . '=' . \FrontendUser::getInstance()->id;
            }
        }
    }

    protected function initEntityFilter()
    {
        if ($this->objFilterContext->addEntityIdFilter)
        {
            $entityFilterIds = StringUtil::deserialize($this->objFilterContext->entityFilterIds, true);

            if (!empty($entityFilterIds))
            {
                $this->arrColumns['id'] = $this->formHybridDataContainer . '.id IN (' . implode(',', $entityFilterIds) . ')';
            }
        }
    }

    protected function applyDefaultFilters()
    {
        foreach ($this->arrDefaultValues as $arrDefaultValue) {
            $strField      = $arrDefaultValue['field'];
            $varValue      = deserialize($arrDefaultValue['value'],true);
            $blnSkipColumn = false;
            $blnSkipValue  = false;
            
            // special handling for tags
            if (in_array('tags', \ModuleLoader::getActive())
                && $GLOBALS['TL_DCA'][$this->formHybridDataContainer]['fields'][$strField]['inputType'] == 'tag'
            ) {
                $arrTags   = explode(',', $varValue);
                $strColumn = '';
                foreach ($arrTags as $i => $strTag) {
                    if ($i == 0) {
                        $strColumn .= "tl_tag.tag='$strTag'";
                    } else {
                        $strColumn .= " OR tl_tag.tag='$strTag'";
                    }
                }
                $blnSkipValue = true;
            }
            elseif(FORMHYBRID_LIST_FREE_TEXT_FIELD == $strField) {
                list($strColumn, $varValue) = $this->prepareFreetextSearchWhereClause($this->replaceInsertTags($varValue, false));
            }
            else {
                $strColumn = $this->prefixFieldWithTable($strField) . '=?';
                $varValue  = $this->replaceInsertTags($varValue, false);
            }
            
            
            
            if($this->objFilterContext->addProximitySearch)
            {
                switch ($strField) {
                    case $this->objFilterContext->proximitySearchPostalField:
                    case $this->objFilterContext->proximitySearchCityField:
                    case FormHybridList::PROXIMITY_SEARCH_USE_LOCATION:
                    case FormHybridList::PROXIMITY_SEARCH_LOCATION:
                        $blnSkipColumn = true;
                        $blnSkipValue  = true;
            
                        break;
                    case FormHybridList::PROXIMITY_SEARCH_RADIUS:
                        $data = $this->getParameterFromDefaultValues();
                        
                        if(empty($data))
                        {
                            $blnSkipColumn = true;
                            $blnSkipValue  = true;
                            break;
                        }
                        
                        list($strColumn, $varValue) = $this->prepareProximitySearchWhereClause($data);
            
                        // no location, postal and city
                        if ($strColumn === false) {
                            $blnSkipColumn = true;
                            $blnSkipValue  = true;
                        } else {
                            $strColumn = $this->prefixFieldWithTable($strColumn);
                        }
            
                        break;
                }
            }
            

            $this->customizeDefaultFilters($strField, $strColumn, $varValue, $blnSkipValue, $blnSkipColumn);

            $this->doApplyDefaultFilters($strField, $strColumn, $varValue, $blnSkipValue, $blnSkipColumn);
        }
    }

    protected function customizeDefaultFilters(&$strField, &$strColumn, &$varValue, &$blnSkipValue, &$blnSkipColumn)
    {
    }

    protected function doApplyDefaultFilters($strField, $strColumn, $varValue, $blnSkipValue = false, $blnSkipColumn = false)
    {
        if (!$blnSkipColumn) {
            $this->arrColumns[$strField] = $strColumn;
        }

        if (!$blnSkipValue) {
            if (is_array($varValue)) {
                foreach ($varValue as $i => $v) {
                    $this->arrValues[$strField . '_' . $i] = $v;
                }
            } else {
                $this->arrValues[$strField] = $varValue;
            }
        }
    }

    protected function applyFilters($objFilterSubmission, $arrFilterFields)
    {
        if ($this->objFilterContext->addDisjunctiveFieldGroups) {
            // add empty values to processed columns
            foreach ($this->arrDisjunctionFieldGroups as $intPosition => $arrGroup) {
                foreach ($arrGroup as $strFilterField) {
                    if (!\Input::get($strFilterField)) {
                        $this->arrDisjunctionFieldGroupsColumns[$intPosition][$strFilterField] = null;
                    }
                }
            }
        }

        foreach ($objFilterSubmission->row() as $strField => $varValue) {
            if (in_array($strField, $this->arrSkippedFilterFields)) {
                continue;
            }

            if (in_array($strField, deserialize($arrFilterFields, true))) {
                if (is_array($varValue) || strlen(trim($varValue)) > 0) {
                    // remove existing values in order to keep the order
                    if (isset($this->arrColumns[$strField])) {
                        unset($this->arrColumns[$strField]);
                    }

                    if (isset($this->arrValues[$strField])) {
                        unset($this->arrValues[$strField]);
                    }

                    $arrDca = $GLOBALS['TL_DCA'][$this->formHybridDataContainer]['fields'][$strField];

                    $blnSkipColumn = false;
                    $blnSkipValue  = false;


                    switch ($arrDca['inputType']) {
                        case 'tag':
                            $arrTags   = explode(',', urldecode($varValue));
                            $strColumn = '';
                            foreach ($arrTags as $i => $strTag) {
                                if ($i == 0) {
                                    $strColumn .= "tl_tag.tag='$strTag'";
                                } else {
                                    $strColumn .= " OR tl_tag.tag='$strTag'";
                                }
                            }

                            $blnSkipValue = true;
                            break;
                        case 'text':
                        case 'textarea':
                        case 'password':
                                $strColumn = $this->prefixFieldWithTable($strField) . " LIKE ?";
                                $varValue  = $this->replaceInsertTags('%' . $varValue . '%', false);
                            break;
                        default:
                            // In ListFilterForm checkbox gets eval value isBoolean and inputType is transformed to select
                            if ($arrDca['inputType'] == 'select' && $arrDca['eval']['isBoolean']) {
                                $strColumn = $this->prefixFieldWithTable($strField) . ' = ?';
                                $varValue  = $varValue == '0' ? '' : '1';
                            } else {
                                if ($arrDca['eval']['multiple'] && is_array(\Input::get($strField))) {
                                    $strColumn    = static::generateMultipleValueMatchSql(
                                        $this->prefixFieldWithTable($strField),
                                        $varValue,
                                        in_array($strField, $this->arrConjunctiveMultipleFields)
                                    );
                                    $blnSkipValue = true;
                                } else {
                                    $strColumn = $this->prefixFieldWithTable($strField) . '=?';
                                    $varValue  = $this->replaceInsertTags($varValue, false);
                                }
                            }
                            break;
                    }

                    if ($this->objFilterContext->addProximitySearch) {
                        $strRadius = str_replace('km', '', Request::getGet(FormHybridList::PROXIMITY_SEARCH_RADIUS));

                        if ($strRadius) {
                            // ignore city and postal -> taken into account in prepareProximitySearchWhereClause()
                            switch ($strField) {
                                case $this->objFilterContext->proximitySearchPostalField:
                                case $this->objFilterContext->proximitySearchCityField:
                                case FormHybridList::PROXIMITY_SEARCH_USE_LOCATION:
                                case FormHybridList::PROXIMITY_SEARCH_LOCATION:
                                    $blnSkipColumn = true;
                                    $blnSkipValue  = true;

                                    break;
                                case FormHybridList::PROXIMITY_SEARCH_RADIUS:
                                    list($strColumn, $varValue) = $this->prepareProximitySearchWhereClause();

                                    // no location, postal and city
                                    if ($strColumn === false) {
                                        $blnSkipColumn = true;
                                        $blnSkipValue  = true;
                                    } else {
                                        $strColumn = $this->prefixFieldWithTable($strColumn);
                                    }

                                    break;
                            }
                        }
                    }

                    if ($this->objFilterContext->addFreetextSearch) {
                        $strFreetext = Request::getGet(FORMHYBRID_LIST_FREE_TEXT_FIELD);

                        if ($strFreetext) {
                            list($strColumn, $varValue) = $this->prepareFreetextSearchWhereClause($strFreetext);
                            $blnSkipValue  = true;
                        }
                    }

                    $this->customizeFilters($strField, $strColumn, $varValue, $blnSkipValue, $blnSkipColumn);

                    if ($this->objFilterContext->addDisjunctiveFieldGroups && ($intPosition = $this->getDisjunctionGroupIndex($strField)) > -1) {
                        if (!$blnSkipColumn) {
                            $this->arrDisjunctionFieldGroupsColumns[$intPosition][$strField] = $strColumn;
                        }

                        if (!$blnSkipValue) {
                            if (is_array($varValue)) {
                                foreach ($varValue as $i => $v) {
                                    $this->arrValues[$strField . '_' . $i] = $v;
                                }
                            } else {
                                $this->arrValues[$strField] = $varValue;
                            }
                        }

                        $arrFields = $this->arrDisjunctionFieldGroups[$intPosition];
                        sort($arrFields);
                        $arrFieldsColumn = array_keys($this->arrDisjunctionFieldGroupsColumns[$intPosition]);
                        sort($arrFieldsColumn);

                        if ($arrFields == $arrFieldsColumn) {
                            $this->arrColumns[$strField] = '(' . implode(
                                    ' OR ',
                                    array_map(
                                        function ($val) {
                                            return '(' . $val . ')';
                                        },
                                        array_filter($this->arrDisjunctionFieldGroupsColumns[$intPosition])
                                    )
                                ) . ')';
                        }
                    } else {
                        $this->doApplyFilters($strField, $strColumn, $varValue, $blnSkipValue, $blnSkipColumn);
                    }
                }
            }
        }
    }

    protected function prefixFieldWithTable($strField)
    {
        if (strpos($strField, '.') === false) {
            $strField = $this->formHybridDataContainer . '.' . $strField;
        }

        return $strField;
    }

    protected function getDisjunctionGroupIndex($strField)
    {
        foreach ($this->arrDisjunctionFieldGroups as $intPosition => $arrGroup) {
            if (in_array($strField, $arrGroup)) {
                return $intPosition;
            }
        }

        return -1;
    }

    protected function customizeFilters(&$strField, &$strColumn, &$varValue, &$blnSkipValue, &$blnSkipColumn)
    {
    }

    protected function doApplyFilters($strField, $strColumn, $varValue, $blnSkipValue = false, $blnSkipColumn = false)
    {
        if (!$blnSkipColumn) {
            $this->arrColumns[$strField] = $strColumn;
        }

        if (!$blnSkipValue) {
            if (is_array($varValue)) {
                foreach ($varValue as $i => $v) {
                    $this->arrValues[$strField . '_' . $i] = $v;
                }
            } else {
                $this->arrValues[$strField] = $varValue;
            }
        }
    }

    protected function getCurrentSorting()
    {
        // user specified
        if (($strOrderField = \Input::get('order')) && ($strSort = \Input::get('sort'))) {
            // anti sql injection: check if field exists
            if (\Database::getInstance()->fieldExists($strOrderField, $this->formHybridDataContainer) && in_array($strSort, ['asc', 'desc'])) {
                $arrCurrentSorting = [
                    'order' => \Input::get('order'),
                    'sort'  => \Input::get('sort'),
                ];
            } else {
                $arrCurrentSorting = [];
            }
        } // initial
        elseif ($this->itemSorting) {
            switch ($this->sortingMode) {
                case OPTION_FORMHYBRID_SORTINGMODE_TEXT:
                    $arrCurrentSorting = [
                        'order' => $this->itemSorting
                    ];
                    break;
                default:
                    if ($this->itemSorting == 'random') {
                        $arrCurrentSorting = [
                            'order' => 'random',
                        ];
                    } else {
                        $arrCurrentSorting = [
                            'order' => preg_replace('@(.*)_(asc|desc)@i', '$1', $this->itemSorting),
                            'sort'  => (strpos($this->itemSorting, '_desc') !== false ? 'desc' : 'asc'),
                        ];
                    }
                    break;
            }
        } // default -> the first table field
        else {
            $arrCurrentSorting = [
                'order' => $this->arrTableFields[0],
                'sort'  => 'asc',
            ];
        }

        return $arrCurrentSorting;
    }

    protected function getHeader()
    {
        $arrHeader         = [];
        $arrCurrentSorting = $this->getCurrentSorting();

        foreach ($this->arrTableFields as $strName) {
            $isCurrentOrderField = ($strName == $arrCurrentSorting['order']);

            $arrField = [
                'field' => $strName,
            ];

            if ($isCurrentOrderField) {
                $arrField['class'] = ($arrCurrentSorting['sort'] == 'asc' ? 'asc' : 'desc');
                $arrField['link']  = Environment::addParametersToUri(
                    Environment::getUrl(),
                    [
                        'order' => $strName,
                        'sort'  => ($arrCurrentSorting['sort'] == 'asc' ? 'desc' : 'asc'),
                    ]
                );
            } else {
                $arrField['link'] = Environment::addParametersToUri(
                    Environment::getUrl(),
                    [
                        'order' => $strName,
                        'sort'  => 'asc',
                    ]
                );
            }

            $arrHeader[] = $arrField;
        }

        return $arrHeader;
    }

    protected function splitResults($offset, $intTotal, $limit, $randomSeed = null)
    {
        $total = $intTotal - $offset;

        // Split the results
        if ($this->perPage > 0 && (!isset($limit) || $this->numberOfItems > $this->perPage)) {
            // Adjust the overall limit
            if (isset($limit)) {
                $total = min($limit, $total);
            }

            // Get the current page
            $id   = 'page_s' . $this->id;
            $page = \Input::get($id) ?: 1;

            // Do not index or cache the page if the page number is outside the range
            if ($page < 1 || $page > max(ceil($total / $this->perPage), 1)) {
                global $objPage;
                $objPage->noSearch = 1;
                $objPage->cache    = 0;

                // Send a 404 header
                header('HTTP/1.1 404 Not Found');

                return;
            }

            // Set limit and offset
            $limit  = $this->perPage;
            $offset += (max($page, 1) - 1) * $this->perPage;

            // Overall limit
            if ($offset + $limit > $total) {
                $limit = $total - $offset;
            }

            // Add the pagination menu
            if ($this->addAjaxPagination) {
                $objPagination = new RandomPagination(
                    $randomSeed, $total, $this->perPage, $GLOBALS['TL_CONFIG']['maxPaginationLinks'], $id, new \FrontendTemplate('pagination_ajax')
                );
            } else {
                $objPagination = new RandomPagination(
                    $randomSeed, $total, $this->perPage, $GLOBALS['TL_CONFIG']['maxPaginationLinks'], $id
                );
            }

            $this->Template->pagination = $objPagination->generate("\n  ");
        }

        return [$offset, $limit];
    }

    public function modifyDC(&$arrDca = null)
    {
        foreach ($arrDca['fields'] as $strField => $arrData) {
            if ($arrData['inputType'] == 'select' && $strField != FormHybridList::PROXIMITY_SEARCH_RADIUS) {
                $arrDca['fields'][$strField]['eval']['includeBlankOption'] = true;
            }
        }

        if ($this->addProximitySearch) {
            $arrDca['fields'][FormHybridList::PROXIMITY_SEARCH_LOCATION] = [
                'inputType' => 'hidden'
            ];
        }
    }

    public static function generateMultipleValueMatchSql($strField, $varValue, $blnConjunction = false)
    {
        if ($blnConjunction) {
            $arrResult = '';
            foreach ($varValue as $strOption) {
                $arrResult[] = '(' . $strField . '="' . $strOption . '" OR ' . $strField . ' REGEXP ("\"' . $strOption . '\""))';
            }
            $strQuery = implode(' AND ', $arrResult);
        } else {
            $arrValueIn = array_map(
                function ($val) {
                    return '"' . \Controller::replaceInsertTags($val, false) . '"';
                },
                $varValue
            );

            $arrValueRegExp = array_map(
                function ($val) {
                    return '\"' . \Controller::replaceInsertTags($val, false) . '\"';
                },
                $varValue
            );

            $strQuery =
                '(' . $strField . ' IN (' . implode(',', $arrValueIn) . ') OR ' . $strField . ' REGEXP ("' . implode('|', $arrValueRegExp) . '"))';
        }

        return $strQuery;
    }

    protected function prepareProximitySearchWhereClause($data = [])
    {
        $t = $this->formHybridDataContainer;

        $strRadius   = str_replace('km', '', $this->getProximityParameter(FormHybridList::PROXIMITY_SEARCH_RADIUS,$data));
        $strLocation = $this->getProximityParameter(FormHybridList::PROXIMITY_SEARCH_LOCATION,$data);
        $strPostal   = $this->getProximityParameter($this->objFilterContext->proximitySearchPostalField,$data);
        $strCity     = $this->getProximityParameter($this->objFilterContext->proximitySearchCityField,$data);
        $strCountry  = $this->getProximityParameter($this->objFilterContext->proximitySearchCountryField,$data);

        $arrValues = [$strRadius];

        if (!$strLocation && !$strPostal && !$strCity) {
            return [false, false];
        }

        // get queried coordinates
        $strQueryLat  = '';
        $strQueryLong = '';

        if ($strLocation) {
            list($strQueryLat, $strQueryLong) = explode(',', $strLocation);
        } elseif ($strPostal || $strCity) {
            $arrQuery = [];

            if ($strPostal) {
                $arrQuery[] = $strPostal;
            } elseif ($strCity) {
                $arrQuery[] = $strCity;
            }

            // add country
            $arrCountries = \System::getCountries();
            $arrQuery[]   = $arrCountries[$strCountry ?: $this->objFilterContext->proximitySearchCountryFallback];

            $objCoordinates = General::findFuzzyAddressOnGoogleMaps(
                implode(', ', $arrQuery)
            );

            $strQueryLat  = $objCoordinates->getLatitude();
            $strQueryLong = $objCoordinates->getLongitude();
        }

        // compose WHERE clause
        $strLatField = $strLongField = '';

        switch ($this->objFilterContext->proximitySearchCoordinatesMode) {
            case FormHybridList::PROXIMITY_SEARCH_COORDINATES_MODE_SEPARATED:
                $strLatField  = $this->objFilterContext->proximitySearchLatField;
                $strLongField = $this->objFilterContext->proximitySearchLongField;
                break;
            case FormHybridList::PROXIMITY_SEARCH_COORDINATES_MODE_COMPOUND:
                $strLatField  = "LEFT($t.$this->objFilterContext->proximitySearchCoordinatesField,INSTR($t.$this->objFilterContext->proximitySearchCoordinatesField,',')-1)";
                $strLongField = "SUBSTRING_INDEX($t.$this->objFilterContext->proximitySearchCoordinatesField,',',-1)";
                break;
        }

        $strColumn = "(
                6371 * acos(
                    cos(
                        radians($strQueryLat)
                    ) * cos(
                        radians($strLatField)
                    ) * cos(
                        radians($strLongField) - radians($strQueryLong)
                    ) + sin(
                        radians($strQueryLat)
                    ) * sin(
                        radians($strLatField)
                    )
                )) < ?";

        if ($strPostal) {
            $strColumn   = "($strColumn OR $t.$this->objFilterContext->proximitySearchPostalField LIKE ?)";
            $arrValues[] = '%' . $strPostal . '%';
        } elseif ($strCity) {
            $strColumn   = "($strColumn OR $t.$this->objFilterContext->proximitySearchCityField LIKE ?)";
            $arrValues[] = '%' . $strCity . '%';
        }

        return [$strColumn, $arrValues];
    }
    
    
    
    
    /**
     * build query for freetext search
     *
     * @param $search string
     * @return array
     */
    protected function prepareFreetextSearchWhereClause($search)
    {
        $t = $this->formHybridDataContainer;
        $search = strtolower($search);
        $values = [];

        if(empty($fields = deserialize($this->freetextSearchFields,true)))
        {
            // flip because getFields returns the fields as keys
            $fields = array_flip(General::getFields($this->formHybridDataContainer));
        }

        $query = '(';

        foreach($fields as $value)
        {
            if('(' != $query)
            {
                $query .= ' OR ';
            }

            $query .= 'LOWER('.$this->prefixFieldWithTable($value).') LIKE ?';
            $values[] = '%'.$search.'%';
        }

        $query .= ')';

        return [$query, $values];
    }
    
    /**
     * set up data array for proximity search from default values (set by module or session)
     *
     * @return array
     */
    protected function getParameterFromDefaultValues()
    {
        $defaultValues = deserialize($this->formHybridDefaultValues,true);
        $data = [];
        
        foreach($defaultValues as $value)
        {
            if($this->objFilterContext->proximitySearchPostalField == $value['field'])
            {
                $data[$this->objFilterContext->proximitySearchPostalField] = $value['value'];
            }
            
            if($this->objFilterContext->proximitySearchCityField == $value['field'])
            {
                $data[$this->objFilterContext->proximitySearchCityField] = $value['value'];
            }
            
            if(FormHybridList::PROXIMITY_SEARCH_LOCATION == $value['field'])
            {
                $data[FormHybridList::PROXIMITY_SEARCH_LOCATION] = $value['value'];
            }
            
            if(FormHybridList::PROXIMITY_SEARCH_RADIUS == $value['field'])
            {
                $data[FormHybridList::PROXIMITY_SEARCH_RADIUS] = $value['value'];
            }
        }
        
        return $data;
    }

    /**
     * get parameter for proximity search
     *
     * @param string $parameter
     * @param array  $data
     *
     * @return string|null
     */
    protected function getProximityParameter(string $parameter, array $data)
    {
        if(empty($data))
        {
            return Request::getGet($parameter) ? Request::getGet($parameter) : null;
        }
        
        if(isset($data[$parameter]))
        {
            return $data[$parameter];
        }
    
        return null;
    }
}
