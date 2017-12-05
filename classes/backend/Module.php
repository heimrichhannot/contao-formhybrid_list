<?php

namespace HeimrichHannot\FormHybridList\Backend;

use Contao\Controller;
use Contao\Database;
use Contao\DataContainer;
use Contao\ModuleModel;
use Contao\StringUtil;
use HeimrichHannot\FormHybridList\FormHybridListQueryBuilder;
use HeimrichHannot\Haste\Dca\DC_HastePlus;
use HeimrichHannot\Haste\Dca\General;
use HeimrichHannot\Haste\Util\FormSubmission;

class Module extends \Backend
{
    public static function adjustPalettesForLists(\DataContainer $objDc)
    {
        \Controller::loadDataContainer('tl_module');
        \System::loadLanguageFile('tl_module');

        if (($objModule = \ModuleModel::findByPk($objDc->id)) !== null) {
            $arrDca = &$GLOBALS['TL_DCA']['tl_module'];

            if (\HeimrichHannot\Haste\Util\Module::isSubModuleOf(
                    $objModule->type,
                    'HeimrichHannot\FormHybridList\ModuleList'
                ) || \HeimrichHannot\Haste\Util\Module::isSubModuleOf(
                    $objModule->type,
                    'HeimrichHannot\FormHybridList\ModuleListFilter'
                )
            ) {
                // override labels for suiting a list module
                $arrDca['fields']['formHybridAddDefaultValues']['label'] = &$GLOBALS['TL_LANG']['tl_module']['formHybridAddDefaultFilterValues'];
                $arrDca['fields']['formHybridDefaultValues']['label']    = &$GLOBALS['TL_LANG']['tl_module']['formHybridDefaultFilterValues'];
                $arrDca['fields']['formHybridTemplate']['label']         = &$GLOBALS['TL_LANG']['tl_module']['formHybridFilterTemplate'];
            }
        }
    }

    public static function initSortingMode(\DataContainer $objDc)
    {
        \Controller::loadDataContainer('tl_module');
        \System::loadLanguageFile('tl_module');

        if (($objModule = \ModuleModel::findByPk($objDc->id)) !== null) {
            $arrDca = &$GLOBALS['TL_DCA']['tl_module'];

            switch ($objModule->sortingMode) {
                case OPTION_FORMHYBRID_SORTINGMODE_TEXT:
                    $arrDca['fields']['itemSorting']['inputType'] = 'text';
                    break;
            }
        }
    }

    public function getSortingOptions(\DataContainer $objDc)
    {
        if ($strDc = $objDc->activeRecord->formHybridDataContainer) {
            \Controller::loadDataContainer($strDc);
            \System::loadLanguageFile($strDc);

            $arrOptions = [];

            foreach ($GLOBALS['TL_DCA'][$strDc]['fields'] as $strField => $arrData) {
                $arrOptions[$strField . '_asc']  = $strField . $GLOBALS['TL_LANG']['tl_module']['itemSorting']['asc'];
                $arrOptions[$strField . '_desc'] = $strField . $GLOBALS['TL_LANG']['tl_module']['itemSorting']['desc'];
            }

            asort($arrOptions);

            return ['random' => $GLOBALS['TL_LANG']['tl_module']['itemSorting']['random']] + $arrOptions;
        }
    }

    public function getArchives(\DataContainer $objDc)
    {
        if ($strDc = $objDc->activeRecord->formHybridDataContainer) {
            \Controller::loadDataContainer($strDc);
            \System::loadLanguageFile($strDc);

            $arrDca = $GLOBALS['TL_DCA'][$strDc];

            if ($strParentTable = $arrDca['config']['ptable']) {
                if ($strItemClass = \Model::getClassFromTable($strParentTable)) {
                    $arrOptions = [];
                    if (($objItems = $strItemClass::findAll()) !== null) {
                        $arrTitleSynonyms = ['name', 'title'];

                        while ($objItems->next()) {
                            $strLabel = '';
                            foreach ($arrTitleSynonyms as $strTitleSynonym) {
                                if ($objItems->{$strTitleSynonym}) {
                                    $strLabel = $objItems->{$strTitleSynonym};
                                    break;
                                }
                            }
                            $arrOptions[$objItems->id] = $strLabel ?: 'Archiv ' . $objItems->id;
                        }
                    }

                    asort($arrOptions);

                    return $arrOptions;
                }
            }
        }
    }

    public function getFormHybridListItemTemplates()
    {
        return \Controller::getTemplateGroup('formhybrid_list_item_');
    }

    public static function getFields($objDc)
    {
        if (!$objDc->activeRecord->formHybridDataContainer) {
            return [];
        }

        return General::getFields($objDc->activeRecord->formHybridDataContainer, false);
    }

    public static function getTextFields(\DataContainer $objDc)
    {
        if (!$objDc->activeRecord->formHybridDataContainer) {
            return [];
        }

        return General::getFields($objDc->activeRecord->formHybridDataContainer, false, 'text');
    }

    public static function getBooleanFields(\DataContainer $objDc)
    {
        if (!$objDc->activeRecord->formHybridDataContainer) {
            return [];
        }

        return General::getFields(
            $objDc->activeRecord->formHybridDataContainer,
            false,
            ['radio', 'checkbox']
        );
    }

    public static function getMultipleFields(\DataContainer $objDc)
    {
        if (!$objDc->activeRecord->formHybridDataContainer) {
            return [];
        }

        return General::getFields(
            $objDc->activeRecord->formHybridDataContainer,
            false,
            ['checkbox', 'select']
        );
    }

    public static function getEntitiesAsOptions(DataContainer $dc)
    {
        $options = [];

        if (($module = ModuleModel::findByPk($dc->id)) === null || !$module->formHybridDataContainer) {
            return [];
        }

        Controller::loadDataContainer($module->formHybridDataContainer);

        $dca = &$GLOBALS['TL_DCA'][$module->formHybridDataContainer];

        // compute filter context
        if ($module->formHybridLinkedFilter && ($filterModule = ModuleModel::findByPk($module->formHybridLinkedFilter)) !== null
            && $module->filterMode == OPTION_FORMHYBRID_FILTERMODE_MODULE
        ) {
            $filterContext = $filterModule;
        } else {
            $filterContext = $module;
        }

        // create query
        $modelOptions = [
            'table' => $module->formHybridDataContainer
        ];

        $table = $modelOptions['table'];

        $columns = $values = [];

        $modelOptions['additionalSelectSql'] = $filterContext->additionalSelectSql;
        $modelOptions['additionalSql']       = $filterContext->additionalSql;
        $modelOptions['additionalWhereSql']  = $filterContext->additionalWhereSql;

        if ($filterContext->additionalSql) {
            $arrOptions['group'] = "$table.id";
        }

        if ($filterContext->additionalHavingSql) {
            $arrOptions['having'] = $filterContext->additionalHavingSql;
        }

        // filters

        // filter archives/groups
        switch ($table) {
            case 'tl_member':
                $filterGroups = deserialize($filterContext->filterGroups, true);

                if (!empty($filterGroups)) {
                    $columns[] = 'groups REGEXP (' . implode(
                            '|',
                            array_map(
                                function ($value) {
                                    return '\'"' . $value . '"\'';
                                },
                                $filterGroups
                            )
                        ) . ')';
                }
                break;
            default:
                $filterArchives = StringUtil::deserialize($module->filterArchives, true);

                if (!empty($filterArchives) && isset($dca['config']['ptable']) && $dca['config']['ptable']) {
                    $columns[] = 'pid IN (' . implode(',', $filterArchives) . ')';
                }
                break;
        }

        if (!empty($columns))
        {
            $modelOptions['column'] = $columns;
        }

        if (!empty($values))
        {
            $modelOptions['value']  = $values;
        }

        // TODO take into account all other filters
        $items = Database::getInstance()->execute(FormHybridListQueryBuilder::find($modelOptions));
        $pattern = 'ID%id%';

        if (isset($GLOBALS['FORMHYBRID_LIST']['ENTITY_ID_FILTER_MAPPING'][$table]))
        {
            $pattern = $GLOBALS['FORMHYBRID_LIST']['ENTITY_ID_FILTER_MAPPING'][$table];
        }
        else {
            if (isset($dca['fields']['title']))
            {
                $pattern = '%title% :ID%id%';
            }
            elseif (isset($dca['fields']['headline']))
            {
                $pattern = '%headline% : ID%id%';
            }
        }

        if ($items->numRows > 0)
        {
            while ($items->next())
            {
                $dc               = new DC_HastePlus($table);
                $dc->id           = $items->id;
                $dc->activeRecord = $items;

                $options[$items->id] = preg_replace_callback(
                    '@%([^%]+)%@i',
                    function ($matches) use ($items, $dca, $dc, $table, $pattern) {
                        return FormSubmission::prepareSpecialValueForPrint(
                            $items->{$matches[1]},
                            $dca['fields'][$matches[1]],
                            $table,
                            $dc
                        );
                    },
                    $pattern
                );
            }
        }

        asort($options);

        return $options;
    }

    public static function modifyPalette(\DataContainer $objDc)
    {
        \Controller::loadDataContainer('tl_module');
        \System::loadLanguageFile('tl_module');

        if (($objModule = \ModuleModel::findByPk($objDc->id)) !== null) {
            $arrDca = &$GLOBALS['TL_DCA']['tl_module'];

            if (\HeimrichHannot\Haste\Util\Module::isSubModuleOf(
                $objModule->type,
                'HeimrichHannot\FormHybridList\ModuleReader'
            )
            ) {
                unset($arrDca['fields']['itemTemplate']['options_callback']);
                $arrDca['fields']['itemTemplate']['options'] = \Controller::getTemplateGroup('formhybrid_reader_');
            }
        }

        if (($objModule = \ModuleModel::findByPk($objDc->id)) !== null) {
            $arrDca = &$GLOBALS['TL_DCA']['tl_module'];

            if (\HeimrichHannot\Haste\Util\Module::isSubModuleOf(
                $objModule->type,
                'HeimrichHannot\FormHybridList\ModuleList'
            )
            ) {
                switch ($objModule->filterMode) {
                    case OPTION_FORMHYBRID_FILTERMODE_STANDARD:
                        $arrDca['palettes'][$objModule->type] = str_replace('filterMode', 'filterMode,' . $arrDca['nestedPalettes']['filterMode_standard'], $arrDca['palettes'][$objModule->type]);
                        break;
                    case OPTION_FORMHYBRID_FILTERMODE_MODULE:
                        $arrDca['palettes'][$objModule->type] = str_replace('filterMode', 'filterMode,' . $arrDca['nestedPalettes']['filterMode_module'], $arrDca['palettes'][$objModule->type]);
                        break;
                }
            }

            if (\HeimrichHannot\Haste\Util\Module::isSubModuleOf(
                $objModule->type,
                'HeimrichHannot\FormHybridList\ModuleMemberList'
            )
            ) {
                $arrDca['palettes'][$objModule->type] = str_replace(
                    [
                        'filterArchives',
                        'imgSize'
                    ],
                    [
                        'filterGroups',
                        'imgSize,memberContentArchiveTags,memberContentArchiveTeaserTag'
                    ],
                    $arrDca['palettes'][$objModule->type]
                );
            }
        }
    }

    public static function getListModules()
    {
        $listModules = [];

        if (($modules = ModuleModel::findAll()) !== null) {
            while ($modules->next()) {
                if (\HeimrichHannot\Haste\Util\Module::isSubModuleOf($modules->type,
                    'HeimrichHannot\FormHybridList\ModuleList')
                ) {
                    $listModules[$modules->id] = $modules->name;
                }
            }
        }

        asort($listModules);

        return $listModules;
    }

    public static function getListFilterModules()
    {
        $listFilterModules = [];

        if (($modules = ModuleModel::findAll()) !== null) {
            while ($modules->next()) {
                if (\HeimrichHannot\Haste\Util\Module::isSubModuleOf($modules->type,
                    'HeimrichHannot\FormHybridList\ModuleListFilter')
                ) {
                    $listFilterModules[$modules->id] = $modules->name;
                }
            }
        }

        asort($listFilterModules);

        return $listFilterModules;
    }
}