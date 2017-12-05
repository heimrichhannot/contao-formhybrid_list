<?php

$arrDca = &$GLOBALS['TL_DCA']['tl_module'];

/**
 * Palettes
 */
// reader
$arrDca['palettes'][MODULE_FORMHYBRID_READER] =
    '{title_legend},name,headline,type;' . '{entity_legend},formHybridDataContainer,addExistanceConditions,aliasField;'
    . '{security_legend},addShowConditions;{redirect_legend},formHybridAddFieldDependentRedirect;'
    . '{misc_legend},imgSize,useDummyImage,setPageTitle;{template_legend},itemTemplate,customTpl;'
    . '{comment_legend:hide},com_template;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$arrDca['palettes'][MODULE_FORMHYBRID_MEMBER_READER] =
    str_replace('imgSize', 'imgSize,memberContentArchiveTags,memberContentArchiveTeaserTag', $arrDca['palettes'][MODULE_FORMHYBRID_READER]);

// list
$arrDca['palettes'][MODULE_FORMHYBRID_LIST] = '{title_legend},name,headline,type;{entity_legend},formHybridIdGetParameter,formHybridDataContainer;'
    . '{security_legend},disableSessionCheck,disableAuthorCheck;'
    . '{list_legend},numberOfItems,perPage,addAjaxPagination,skipFirst,skipInstances,showItemCount,emptyText,'
    . 'showInitialResults,formHybridAddHashToAction,removeAutoItemFromAction,isTableList,addDetailsCol,addShareCol,deactivateTokens,addMasonry;'
    . '{sorting_legend},sortingMode,itemSorting;'
    . '{filter_legend},filterMode;'
    . '{misc_legend},imgSize,useDummyImage;'
    . '{template_legend:hide},formHybridTemplate,formHybridCustomSubTemplates,itemTemplate,customTpl;'
    . '{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$arrDca['palettes'][MODULE_FORMHYBRID_NEWS_LIST] = $arrDca['palettes'][MODULE_FORMHYBRID_LIST];
$arrDca['palettes'][MODULE_FORMHYBRID_MEMBER_LIST] = $arrDca['palettes'][MODULE_FORMHYBRID_LIST];

// filter

// workaround since nested subpalettes in type selectors aren't support, yet :-(
$arrDca['nestedPalettes'] = [
    'filterMode_standard' => 'filterHeadline,hideFilter,customFilterFields,formHybridAddPermanentFields,hideUnpublishedItems,'
        . 'publishedField,invertPublishedField,filterArchives,formHybridAddDefaultValues,addEntityIdFilter,conjunctiveMultipleFields,'
        . 'addDisjunctiveFieldGroups,formHybridTransformGetParamsToHiddenFields,addProximitySearch,additionalWhereSql,additionalSelectSql,additionalSql,additionalHavingSql',
    'filterMode_module'   => 'formHybridLinkedFilter'
];

$arrDca['palettes'][MODULE_FORMHYBRID_LIST_FILTER] = '{title_legend},name,headline,type;{entity_legend},formHybridIdGetParameter,formHybridDataContainer;'
    . '{filter_legend},' . str_replace('filterHeadline,', '', $arrDca['nestedPalettes']['filterMode_standard']) . ';'
    . '{redirect_legend},formHybridAction;'
    . '{template_legend:hide},formHybridTemplate;'
    . '{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

/**
 * Subpalettes
 */
$arrDca['palettes']['__selector__'] = array_merge(
    $arrDca['palettes']['__selector__'],
    [
        'isTableList',
        'addDetailsCol',
        'addShareCol',
        'useDummyImage',
        'addDisjunctiveFieldGroups',
        'addShowConditions',
        'addExistanceConditions',
        'setPageTitle',
        'addAjaxPagination',
        'addEntityIdFilter',
        'addMasonry',
        'addProximitySearch',
        'proximitySearchCoordinatesMode'
    ]
);

$arrDca['subpalettes'] = array_merge(
    $arrDca['subpalettes'],
    [
        'isTableList'                                                                                                                  => 'tableFields,hasHeader,sortingHeader,useSelectSorting',
        'addDetailsCol'                                                                                                                => 'useModalExplanation,useModal,jumpToDetails',
        'addShareCol'                                                                                                                  => 'jumpToShare,shareAutoItem',
        'useDummyImage'                                                                                                                => 'dummyImage',
        'addDisjunctiveFieldGroups'                                                                                                    => 'disjunctiveFieldGroups',
        'addShowConditions'                                                                                                            => 'showConditions',
        'addExistanceConditions'                                                                                                       => 'existanceConditions,appendIdToUrlOnFound',
        'setPageTitle'                                                                                                                 => 'pageTitleField,pageTitlePattern',
        'addAjaxPagination'                                                                                                            => 'addInfiniteScroll',
        'addEntityIdFilter'                                                                                                            => 'entityFilterIds',
        'addMasonry'                                                                                                                   => 'masonryStampContentElements',
        'addProximitySearch'                                                                                                           => 'proximitySearchSteps,proximitySearchAllowGeoLocation,proximitySearchCityField,proximitySearchPostalField,proximitySearchStateField,proximitySearchCountryFallback,proximitySearchCountryField,proximitySearchCoordinatesMode',
        'proximitySearchCoordinatesMode_' . \HeimrichHannot\FormHybridList\FormHybridList::PROXIMITY_SEARCH_COORDINATES_MODE_COMPOUND  => 'proximitySearchCoordinatesField',
        'proximitySearchCoordinatesMode_' . \HeimrichHannot\FormHybridList\FormHybridList::PROXIMITY_SEARCH_COORDINATES_MODE_SEPARATED => 'proximitySearchLatField,proximitySearchLongField'
    ]
);

/**
 * Callbacks
 */
// adjust labels for suiting a list module
$arrDca['config']['onload_callback'][] = ['HeimrichHannot\FormHybridList\Backend\Module', 'adjustPalettesForLists'];
$arrDca['config']['onload_callback'][] = ['HeimrichHannot\FormHybridList\Backend\Module', 'initSortingMode'];
$arrDca['config']['onload_callback'][] = ['HeimrichHannot\FormHybridList\Backend\Module', 'modifyPalette'];

/**
 * Fields
 */
$arrFields = [
    'addDetailsCol'                   => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addDetailsCol'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'jumpToDetails'                   => [
        'label'      => &$GLOBALS['TL_LANG']['tl_module']['jumpToDetails'],
        'exclude'    => true,
        'inputType'  => 'pageTree',
        'foreignKey' => 'tl_page.title',
        'eval'       => ['fieldType' => 'radio', 'tl_class' => 'w50 clr'],
        'sql'        => "int(10) unsigned NOT NULL default '0'",
        'relation'   => ['type' => 'hasOne', 'load' => 'eager']
    ],
    'addShareCol'                     => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addShareCol'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'jumpToShare'                     => [
        'label'      => &$GLOBALS['TL_LANG']['tl_module']['jumpToShare'],
        'exclude'    => true,
        'inputType'  => 'pageTree',
        'foreignKey' => 'tl_page.title',
        'eval'       => ['fieldType' => 'radio', 'tl_class' => 'w50 clr'],
        'sql'        => "int(10) unsigned NOT NULL default '0'",
        'relation'   => ['type' => 'hasOne', 'load' => 'eager']
    ],
    'shareAutoItem'                   => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['shareAutoItem'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'sortingMode'                     => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['sortingMode'],
        'exclude'   => true,
        'inputType' => 'select',
        'options'   => [OPTION_FORMHYBRID_SORTINGMODE_FIELD, OPTION_FORMHYBRID_SORTINGMODE_TEXT],
        'reference' => &$GLOBALS['TL_LANG']['tl_module']['sortingMode'],
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql'       => "varchar(16) NOT NULL default 'field'"
    ],
    'filterMode'                      => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['filterMode'],
        'exclude'   => true,
        'inputType' => 'select',
        'options'   => [OPTION_FORMHYBRID_FILTERMODE_STANDARD, OPTION_FORMHYBRID_FILTERMODE_MODULE],
        'reference' => &$GLOBALS['TL_LANG']['tl_module']['filterMode'],
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true, 'includeBlankOption' => true, 'mandatory' => true],
        'sql'       => "varchar(16) NOT NULL default 'field'"
    ],
    'itemSorting'                     => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['itemSorting'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getSortingOptions'],
        'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true, 'mandatory' => true],
        'sql'              => "varchar(64) NOT NULL default ''"
    ],
    'hideFilter'                      => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['hideFilter'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'showItemCount'                   => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['showItemCount'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'showInitialResults'              => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['showInitialResults'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default '1'"
    ],
    'addAjaxPagination'               => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addAjaxPagination'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'addInfiniteScroll'               => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addInfiniteScroll'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'addMasonry'                      => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addMasonry'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'masonryStampContentElements'     => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['masonryStampContentElements'],
        'inputType' => 'multiColumnEditor',
        'eval'      => [
            'multiColumnEditor' => [
                'minRowCount' => 0,
                'fields'      => [
                    'stampBlock'    => [
                        'label'            => &$GLOBALS['TL_LANG']['tl_module']['stampBlock'],
                        'exclude'          => true,
                        'inputType'        => 'select',
                        'options_callback' => ['HeimrichHannot\Blocks\Backend\Content', 'getBlocks'],
                        'eval'             => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true]
                    ],
                    'stampCssClass' => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_module']['stampCssClass'],
                        'exclude'   => true,
                        'search'    => true,
                        'inputType' => 'text',
                        'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
                    ]
                ],
            ],
        ],
        'sql'       => "blob NULL",
    ],
    'addProximitySearch'              => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addProximitySearch'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'proximitySearchAllowGeoLocation' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['proximitySearchAllowGeoLocation'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'proximitySearchCoordinatesMode'  => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['proximitySearchCoordinatesMode'],
        'exclude'   => true,
        'filter'    => true,
        'inputType' => 'select',
        'options'   => \HeimrichHannot\FormHybridList\FormHybridList::PROXIMITY_SEARCH_COORDINATES_MODES,
        'reference' => &$GLOBALS['TL_LANG']['tl_module']['proximitySearchCoordinatesModes'],
        'eval'      => ['tl_class' => 'w50 clr', 'includeBlankOption' => true, 'submitOnChange' => true, 'mandatory' => true],
        'sql'       => "varchar(64) NOT NULL default ''"
    ],
    'proximitySearchCoordinatesField' => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['proximitySearchCoordinatesField'],
        'exclude'          => true,
        'filter'           => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getTextFields'],
        'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true, 'mandatory' => true],
        'sql'              => "varchar(64) NOT NULL default ''"
    ],
    'proximitySearchLatField'         => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['proximitySearchLatField'],
        'exclude'          => true,
        'filter'           => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getTextFields'],
        'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true, 'mandatory' => true],
        'sql'              => "varchar(64) NOT NULL default ''"
    ],
    'proximitySearchLongField'        => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['proximitySearchLongField'],
        'exclude'          => true,
        'filter'           => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getTextFields'],
        'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true, 'mandatory' => true],
        'sql'              => "varchar(64) NOT NULL default ''"
    ],
    'proximitySearchCityField'        => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['proximitySearchCityField'],
        'exclude'          => true,
        'filter'           => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getFields'],
        'eval'             => ['tl_class' => 'w50 clr', 'includeBlankOption' => true, 'chosen' => true],
        'sql'              => "varchar(64) NOT NULL default ''"
    ],
    'proximitySearchPostalField'      => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['proximitySearchPostalField'],
        'exclude'          => true,
        'filter'           => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getFields'],
        'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true],
        'sql'              => "varchar(64) NOT NULL default ''"
    ],
    'proximitySearchStateField'       => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['proximitySearchStateField'],
        'exclude'          => true,
        'filter'           => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getFields'],
        'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true],
        'sql'              => "varchar(64) NOT NULL default ''"
    ],
    'proximitySearchCountryField'     => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['proximitySearchCountryField'],
        'exclude'          => true,
        'filter'           => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getFields'],
        'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true],
        'sql'              => "varchar(64) NOT NULL default ''"
    ],
    'proximitySearchCountryFallback'  => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['proximitySearchCountryFallback'],
        'exclude'   => true,
        'filter'    => true,
        'sorting'   => true,
        'inputType' => 'select',
        'options'   => System::getCountries(),
        'eval'      => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50', 'mandatory' => true],
        'sql'       => "varchar(2) NOT NULL default ''"
    ],
    'isTableList'                     => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['isTableList'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'hasHeader'                       => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['hasHeader'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'sortingHeader'                   => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['sortingHeader'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'tableFields'                     => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['tableFields'],
        'inputType'        => 'checkboxWizard',
        'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getFields'],
        'exclude'          => true,
        'eval'             => ['multiple' => true, 'includeBlankOption' => true, 'tl_class' => 'w50 clr autoheight'],
        'sql'              => "blob NULL",
    ],
    'customFilterFields'              => [
        'inputType'        => 'checkboxWizard',
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['customFilterFields'],
        'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getFields'],
        'exclude'          => true,
        'eval'             => [
            'multiple'           => true,
            'includeBlankOption' => true,
            'tl_class'           => 'w50 autoheight clr'
        ],
        'sql'              => "blob NULL"
    ],
    'filterArchives'                  => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['filterArchives'],
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getArchives'],
        'eval'             => ['multiple' => true, 'chosen' => true, 'tl_class' => 'w50'],
        'sql'              => "blob NULL"
    ],
    'filterGroups'                    => [
        'label'      => &$GLOBALS['TL_LANG']['tl_module']['filterGroups'],
        'inputType'  => 'select',
        'foreignKey' => 'tl_member_group.name',
        'eval'       => ['multiple' => true, 'chosen' => true, 'tl_class' => 'w50', 'doNotCopy' => true],
        'sql'        => "blob NULL"
    ],
    'setPageTitle'                    => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['setPageTitle'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'pageTitleField'                  => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['pageTitleField'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getTextFields'],
        'eval'             => ['maxlength' => 64, 'tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true],
        'sql'              => "varchar(64) NOT NULL default ''"
    ],
    'pageTitlePattern'                => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['pageTitlePattern'],
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 64, 'tl_class' => 'w50'],
        'sql'       => "varchar(64) NOT NULL default ''"
    ],
    'additionalWhereSql'              => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['additionalWhereSql'],
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'tl_class' => 'w50 clr'],
        'sql'       => "varchar(255) NOT NULL default ''"
    ],
    'additionalSelectSql'             => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['additionalSelectSql'],
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
        'sql'       => "varchar(255) NOT NULL default ''"
    ],
    'additionalSql'                   => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['additionalSql'],
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'tl_class' => 'w50 clr'],
        'sql'       => "varchar(255) NOT NULL default ''"
    ],
    'additionalHavingSql'             => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['additionalHavingSql'],
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
        'sql'       => "varchar(255) NOT NULL default ''"
    ],
    'hideUnpublishedItems'            => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['hideUnpublishedItems'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50 clr'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'publishedField'                  => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['publishedField'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getBooleanFields'],
        'eval'             => ['maxlength' => 32, 'tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true],
        'sql'              => "varchar(32) NOT NULL default ''"
    ],
    'invertPublishedField'            => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['invertPublishedField'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'emptyText'                       => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['emptyText'],
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'tl_class' => 'w50 clr'],
        'sql'       => "varchar(255) NOT NULL default ''"
    ],
    'filterHeadline'                  => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['filterHeadline'],
        'exclude'   => true,
        'inputType' => 'inputUnit',
        'options'   => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
        'eval'      => ['maxlength' => 200, 'tl_class' => 'w50'],
        'sql'       => "varchar(255) NOT NULL default ''"
    ],
    'itemTemplate'                    => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['itemTemplate'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getFormHybridListItemTemplates'],
        'eval'             => ['tl_class' => 'w50 clr', 'includeBlankOption' => true],
        'sql'              => "varchar(128) NOT NULL default ''",
    ],
    'useDummyImage'                   => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['useDummyImage'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'dummyImage'                      => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['dummyImage'],
        'exclude'   => true,
        'inputType' => 'fileTree',
        'eval'      => [
            'tl_class'   => 'w50',
            'filesOnly'  => true,
            'extensions' => Config::get('validImageTypes'),
            'fieldType'  => 'radio',
            'mandatory'  => true
        ],
        'sql'       => "binary(16) NULL"
    ],
    'disableSessionCheck'             => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['disableSessionCheck'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50 clr'],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'disableAuthorCheck'              => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['disableAuthorCheck'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'conjunctiveMultipleFields'       => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['conjunctiveMultipleFields'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getMultipleFields'],
        'eval'             => ['tl_class' => 'w50', 'multiple' => true, 'chosen' => true],
        'sql'              => "blob NULL"
    ],
    'addDisjunctiveFieldGroups'       => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addDisjunctiveFieldGroups'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'disjunctiveFieldGroups'          => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['disjunctiveFieldGroups'],
        'inputType' => 'multiColumnWizard',
        'exclude'   => true,
        'eval'      => [
            'tl_class'     => 'clr',
            'columnFields' => [
                'fields' => [
                    'label'            => &$GLOBALS['TL_LANG']['tl_module']['disjunctiveFieldGroups']['fields'],
                    'inputType'        => 'select',
                    'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getFields'],
                    'eval'             => [
                        'style'    => 'width:783px',
                        'multiple' => true,
                        'chosen'   => true
                    ],
                ],
            ]
        ],
        'sql'       => "blob NULL",
    ],
    'addExistanceConditions'          => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addExistanceConditions'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'aliasField'                      => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['aliasField'],
        'inputType'        => 'select',
        'exclude'          => true,
        'eval'             => [
            'tl_class'           => 'w50 clr',
            'includeBlankOption' => true,
            'submitOnChange'     => true,
            'chosen'             => true
        ],
        'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getTextFields'],
        'sql'              => "varchar(64) NOT NULL default ''"
    ],
    'existanceConditions'             => $arrDca['fields']['formHybridDefaultValues'],
    'appendIdToUrlOnFound'            => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['appendIdToUrlOnFound'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'addShowConditions'               => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addShowConditions'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'showConditions'                  => $arrDca['fields']['formHybridDefaultValues'],
    'deactivateTokens'                => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['deactivateTokens'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50 clr'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'formHybridLinkedList'            => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['formHybridLinkedList'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getListModules'],
        'eval'             => ['mandatory' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
        'sql'              => 'int(10) NOT NULL'
    ],
    'formHybridLinkedFilter'          => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['formHybridLinkedFilter'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getListFilterModules'],
        'eval'             => ['includeBlankOption' => true, 'tl_class' => 'w50', 'mandatory' => true],
        'sql'              => 'int(10) NOT NULL'
    ],
    'useSelectSorting'                => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['useSelectSorting'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'addEntityIdFilter'               => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addEntityIdFilter'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'entityFilterIds'                 => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['entityFilterIds'],
        'exclude'          => true,
        'filter'           => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\FormHybridList\Backend\Module', 'getEntitiesAsOptions'],
        'eval'             => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'chosen' => true, 'multiple' => true],
        'sql'              => "blob NULL"
    ]
];

if (in_array('member_content_archives', \ModuleLoader::getActive())) {
    $arrFields = array_merge(
        $arrFields,
        [
            'memberContentArchiveTags'      => [
                'label'      => &$GLOBALS['TL_LANG']['tl_module']['memberContentArchiveTags'],
                'inputType'  => 'select',
                'eval'       => [
                    'multiple' => true,
                    'tl_class' => 'w50',
                    'chosen'   => true
                ],
                'foreignKey' => 'tl_member_content_archive_tag.title',
                'sql'        => 'blob NULL',
            ],
            'memberContentArchiveTeaserTag' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_module']['memberContentArchiveTeaserTag'],
                'inputType'  => 'select',
                'eval'       => [
                    'tl_class'           => 'w50',
                    'includeBlankOption' => true
                ],
                'foreignKey' => 'tl_member_content_archive_tag.title',
                'sql'        => 'blob NULL',
            ]
        ]
    );
}

$arrDca['fields'] += $arrFields;

$arrDca['fields']['formHybridCustomSubTemplates']['eval']['tl_class'] = 'w50';

foreach (['existanceConditions', 'showConditions'] as $strField) {
    $arrDca['fields'][$strField]['label'] = &$GLOBALS['TL_LANG']['tl_module'][$strField];
    unset($arrDca['fields'][$strField]['eval']['columnFields']['label']);
    unset($arrDca['fields'][$strField]['eval']['columnFields']['hidden']);
}
