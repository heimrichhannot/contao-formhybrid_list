<?php

$arrDca = &$GLOBALS['TL_DCA']['tl_module'];

/**
 * Palettes
 */
// reader
$arrDca['palettes'][MODULE_FORMHYBRID_READER] =
    '{title_legend},name,headline,type;' . '{entity_legend},formHybridDataContainer,addExistanceConditions,aliasField;'
    . '{security_legend},addShowConditions;{redirect_legend},formHybridAddFieldDependentRedirect;'
    . '{misc_legend},imgSize,useDummyImage,setPageTitle;{template_legend},itemTemplate,modalTpl,customTpl;'
    . '{comment_legend:hide},com_template;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$arrDca['palettes'][MODULE_FORMHYBRID_MEMBER_READER] =
    str_replace('imgSize', 'imgSize,memberContentArchiveTags,memberContentArchiveTeaserTag', $arrDca['palettes'][MODULE_FORMHYBRID_READER]);

// list
$arrDca['palettes'][MODULE_FORMHYBRID_LIST] = '{title_legend},name,headline,type;{entity_legend},formHybridIdGetParameter,formHybridDataContainer;'
                                              . '{list_legend},numberOfItems,perPage,addAjaxPagination,skipFirst,skipInstances,showItemCount,emptyText,'
                                              . 'showInitialResults,formHybridAddHashToAction,isTableList,addDetailsCol,addShareCol,deactivateTokens,addMasonry;'
                                              . '{filter_legend},sortingMode,itemSorting,hideFilter,filterHeadline,customFilterFields,formHybridAddPermanentFields,hideUnpublishedItems,'
                                              . 'publishedField,invertPublishedField,filterArchives,formHybridAddDefaultValues,conjunctiveMultipleFields,'
                                              . 'addDisjunctiveFieldGroups,formHybridTransformGetParamsToHiddenFields,additionalWhereSql,additionalSelectSql,additionalSql,additionalHavingSql;'
                                              . '{misc_legend},imgSize,useDummyImage,useModal;'
                                              . '{template_legend:hide},formHybridTemplate,formHybridCustomSubTemplates,itemTemplate,customTpl;'
                                              . '{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$arrDca['palettes'][MODULE_FORMHYBRID_MEMBER_LIST] = str_replace(
    [
        'filterArchives',
        'imgSize'
    ],
    [
        'filterGroups',
        'imgSize,memberContentArchiveTags,memberContentArchiveTeaserTag'
    ],
    $arrDca['palettes'][MODULE_FORMHYBRID_LIST]
);

$arrDca['palettes'][MODULE_FORMHYBRID_NEWS_LIST] = $arrDca['palettes'][MODULE_FORMHYBRID_LIST];

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
        'useModal',
        'setPageTitle',
        'addAjaxPagination',
        'addMasonry'
    ]
);

$arrDca['subpalettes'] = array_merge(
    $arrDca['subpalettes'],
    [
        'isTableList'               => 'tableFields,hasHeader,sortingHeader',
        'addDetailsCol'             => 'jumpToDetails',
        'addShareCol'               => 'jumpToShare,shareAutoItem',
        'useDummyImage'             => 'dummyImage',
        'addDisjunctiveFieldGroups' => 'disjunctiveFieldGroups',
        'addShowConditions'         => 'showConditions',
        'addExistanceConditions'    => 'existanceConditions',
        'useModal'                  => 'modalWrapperTpl,modalClass,modalInnerClass,useModalWrapperSync',
        'setPageTitle'              => 'pageTitleField,pageTitlePattern',
        'addAjaxPagination'         => 'addInfiniteScroll',
        'addMasonry'                => 'masonryCols,masonryStampContentElements'
    ]
);

/**
 * Callbacks
 */
// adjust labels for suiting a list module
$arrDca['config']['onload_callback'][] = ['tl_module_formhybrid_list', 'adjustPalettesForLists'];
$arrDca['config']['onload_callback'][] = ['tl_module_formhybrid_list', 'initSortingMode'];
$arrDca['config']['onload_callback'][] = ['tl_module_formhybrid_list', 'modifyPalette'];

/**
 * Fields
 */
$arrFields = [
    'addDetailsCol'               => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addDetailsCol'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'jumpToDetails'               => [
        'label'      => &$GLOBALS['TL_LANG']['tl_module']['jumpToDetails'],
        'exclude'    => true,
        'inputType'  => 'pageTree',
        'foreignKey' => 'tl_page.title',
        'eval'       => ['fieldType' => 'radio', 'tl_class' => 'w50 clr'],
        'sql'        => "int(10) unsigned NOT NULL default '0'",
        'relation'   => ['type' => 'hasOne', 'load' => 'eager']
    ],
    'addShareCol'                 => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addShareCol'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'jumpToShare'                 => [
        'label'      => &$GLOBALS['TL_LANG']['tl_module']['jumpToShare'],
        'exclude'    => true,
        'inputType'  => 'pageTree',
        'foreignKey' => 'tl_page.title',
        'eval'       => ['fieldType' => 'radio', 'tl_class' => 'w50 clr'],
        'sql'        => "int(10) unsigned NOT NULL default '0'",
        'relation'   => ['type' => 'hasOne', 'load' => 'eager']
    ],
    'shareAutoItem'               => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['shareAutoItem'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'sortingMode'                 => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['sortingMode'],
        'exclude'   => true,
        'inputType' => 'select',
        'options'   => [OPTION_FORMHYBRID_SORTINGMODE_FIELD, OPTION_FORMHYBRID_SORTINGMODE_TEXT],
        'reference' => &$GLOBALS['TL_LANG']['tl_module']['sortingMode'],
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql'       => "varchar(16) NOT NULL default 'field'"
    ],
    'itemSorting'                 => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['itemSorting'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => ['tl_module_formhybrid_list', 'getSortingOptions'],
        'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true, 'mandatory' => true],
        'sql'              => "varchar(255) NOT NULL default ''"
    ],
    'hideFilter'                  => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['hideFilter'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'showItemCount'               => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['showItemCount'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'showInitialResults'          => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['showInitialResults'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default '1'"
    ],
    'addAjaxPagination'           => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addAjaxPagination'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'addInfiniteScroll'           => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addInfiniteScroll'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'addMasonry'                  => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addMasonry'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'masonryCols'                 => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['masonryCols'],
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['tl_class' => 'w50', 'rgxp' => 'digit', 'mandatory' => true],
        'sql'       => "int(8) unsigned NOT NULL default '3'"
    ],
    'masonryStampContentElements' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['masonryStampContentElements'],
        'inputType' => 'multiColumnEditor',
        'eval'      => [
            'multiColumnEditor' => [
                'minRowCount' => 0,
                'fields' => [
                    'stampBlock' => [
                        'label'            => &$GLOBALS['TL_LANG']['tl_module']['stampBlock'],
                        'exclude'          => true,
                        'inputType'        => 'select',
                        'options_callback' => ['HeimrichHannot\Blocks\Backend\Content', 'getBlocks'],
                        'eval'             => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true]
                    ],
                    'stampCssClass' => [
                        'label'                   => &$GLOBALS['TL_LANG']['tl_module']['stampCssClass'],
                        'exclude'                 => true,
                        'search'                  => true,
                        'inputType'               => 'text',
                        'eval'                    => ['maxlength' => 255, 'tl_class' => 'w50'],
                    ]
                ],
            ],
        ],
        'sql'       => "blob NULL",
    ],
    'isTableList'                 => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['isTableList'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'hasHeader'                   => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['hasHeader'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'sortingHeader'               => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['sortingHeader'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'tableFields'                 => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['tableFields'],
        'inputType'        => 'checkboxWizard',
        'options_callback' => ['tl_module_formhybrid_list', 'getFields'],
        'exclude'          => true,
        'eval'             => ['multiple' => true, 'includeBlankOption' => true, 'tl_class' => 'w50 clr autoheight'],
        'sql'              => "blob NULL",
    ],
    'customFilterFields'          => [
        'inputType'        => 'checkboxWizard',
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['customFilterFields'],
        'options_callback' => ['tl_module_formhybrid_list', 'getFields'],
        'exclude'          => true,
        'eval'             => [
            'multiple'           => true,
            'includeBlankOption' => true,
            'tl_class'           => 'w50 autoheight clr'
        ],
        'sql'              => "blob NULL"
    ],
    'filterArchives'              => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['filterArchives'],
        'inputType'        => 'select',
        'options_callback' => ['tl_module_formhybrid_list', 'getArchives'],
        'eval'             => ['multiple' => true, 'chosen' => true, 'tl_class' => 'w50'],
        'sql'              => "blob NULL"
    ],
    'filterGroups'                => [
        'label'      => &$GLOBALS['TL_LANG']['tl_module']['filterGroups'],
        'inputType'  => 'select',
        'foreignKey' => 'tl_member_group.name',
        'eval'       => ['multiple' => true, 'chosen' => true, 'tl_class' => 'w50', 'doNotCopy' => true],
        'sql'        => "blob NULL"
    ],
    'setPageTitle'                => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['setPageTitle'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'pageTitleField'              => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['pageTitleField'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => ['tl_module_formhybrid_list', 'getTextFields'],
        'eval'             => ['maxlength' => 255, 'tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true],
        'sql'              => "varchar(255) NOT NULL default ''"
    ],
    'pageTitlePattern'            => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['pageTitlePattern'],
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
        'sql'       => "varchar(255) NOT NULL default ''"
    ],
    'additionalWhereSql'          => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['additionalWhereSql'],
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'tl_class' => 'w50 clr'],
        'sql'       => "varchar(255) NOT NULL default ''"
    ],
    'additionalSelectSql'         => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['additionalSelectSql'],
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
        'sql'       => "varchar(255) NOT NULL default ''"
    ],
    'additionalSql'               => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['additionalSql'],
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
        'sql'       => "varchar(255) NOT NULL default ''"
    ],
    'additionalHavingSql'         => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['additionalHavingSql'],
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
        'sql'       => "varchar(255) NOT NULL default ''"
    ],
    'hideUnpublishedItems'        => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['hideUnpublishedItems'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'publishedField'              => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['publishedField'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => ['tl_module_formhybrid_list', 'getBooleanFields'],
        'eval'             => ['maxlength' => 255, 'tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true],
        'sql'              => "varchar(255) NOT NULL default ''"
    ],
    'invertPublishedField'        => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['invertPublishedField'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'emptyText'                   => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['emptyText'],
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'tl_class' => 'w50 clr'],
        'sql'       => "varchar(255) NOT NULL default ''"
    ],
    'filterHeadline'              => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['filterHeadline'],
        'exclude'   => true,
        'inputType' => 'inputUnit',
        'options'   => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
        'eval'      => ['maxlength' => 200, 'tl_class' => 'w50'],
        'sql'       => "varchar(255) NOT NULL default ''"
    ],
    'itemTemplate'                => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['itemTemplate'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => ['tl_module_formhybrid_list', 'getFormHybridListItemTemplates'],
        'eval'             => ['tl_class' => 'w50 clr', 'includeBlankOption' => true],
        'sql'              => "varchar(255) NOT NULL default ''",
    ],
    'useDummyImage'               => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['useDummyImage'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'dummyImage'                  => [
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
    'conjunctiveMultipleFields'   => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['conjunctiveMultipleFields'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => ['tl_module_formhybrid_list', 'getMultipleFields'],
        'eval'             => ['tl_class' => 'w50', 'multiple' => true, 'chosen' => true],
        'sql'              => "blob NULL"
    ],
    'addDisjunctiveFieldGroups'   => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addDisjunctiveFieldGroups'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'disjunctiveFieldGroups'      => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['disjunctiveFieldGroups'],
        'inputType' => 'multiColumnWizard',
        'exclude'   => true,
        'eval'      => [
            'tl_class'     => 'clr',
            'columnFields' => [
                'fields' => [
                    'label'            => &$GLOBALS['TL_LANG']['tl_module']['disjunctiveFieldGroups']['fields'],
                    'inputType'        => 'select',
                    'options_callback' => ['tl_module_formhybrid_list', 'getFields'],
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
    'useModal'                    => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['useModal'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'modalWrapperTpl'             => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['modalWrapperTpl'],
        'exclude'          => true,
        'inputType'        => 'select',
        'default'          => 'formhybrid_reader_modal_wrapper_bootstrap',
        'options_callback' => ['tl_module_formhybrid_list', 'getFormHybridReaderModalWrapperTemplates'],
        'eval'             => ['tl_class' => 'w50 clr'],
        'sql'              => "varchar(255) NOT NULL default ''",
    ],
    'modalTpl'                    => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['modalTpl'],
        'exclude'          => true,
        'inputType'        => 'select',
        'default'          => 'formhybrid_reader_modal_bootstrap',
        'options_callback' => ['tl_module_formhybrid_list', 'getFormHybridReaderModalTemplates'],
        'eval'             => ['tl_class' => 'w50'],
        'sql'              => "varchar(255) NOT NULL default ''",
    ],
    'modalClass'                  => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['modalClass'],
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "varchar(255) NOT NULL default ''",
    ],
    'modalInnerClass'             => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['modalInnerClass'],
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "varchar(255) NOT NULL default ''",
    ],
    'useModalWrapperSync'         => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['useModalWrapperSync'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50 clr', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'addExistanceConditions'      => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addExistanceConditions'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'aliasField'                  => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['aliasField'],
        'inputType'        => 'select',
        'exclude'          => true,
        'eval'             => [
            'tl_class'           => 'w50 clr',
            'includeBlankOption' => true,
            'submitOnChange'     => true,
            'chosen'             => true
        ],
        'options_callback' => ['tl_module_formhybrid_list', 'getTextFields'],
        'sql'              => "varchar(255) NOT NULL default ''"
    ],
    'existanceConditions'         => $arrDca['fields']['formHybridDefaultValues'],
    'addShowConditions'           => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addShowConditions'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'showConditions'              => $arrDca['fields']['formHybridDefaultValues'],
    'deactivateTokens'            => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['deactivateTokens'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ]
];

if (in_array('member_content_archives', \ModuleLoader::getActive()))
{
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

foreach (['existanceConditions', 'showConditions'] as $strField)
{
    $arrDca['fields'][$strField]['label'] = &$GLOBALS['TL_LANG']['tl_module'][$strField];
    unset($arrDca['fields'][$strField]['eval']['columnFields']['label']);
    unset($arrDca['fields'][$strField]['eval']['columnFields']['hidden']);
}

class tl_module_formhybrid_list
{
    public static function adjustPalettesForLists(\DataContainer $objDc)
    {
        \Controller::loadDataContainer('tl_module');
        \System::loadLanguageFile('tl_module');

        if (($objModule = \ModuleModel::findByPk($objDc->id)) !== null)
        {
            $arrDca = &$GLOBALS['TL_DCA']['tl_module'];

            if (\HeimrichHannot\Haste\Util\Module::isSubModuleOf(
                $objModule->type,
                'HeimrichHannot\FormHybridList\ModuleList'
            )
            )
            {
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

        if (($objModule = \ModuleModel::findByPk($objDc->id)) !== null)
        {
            $arrDca = &$GLOBALS['TL_DCA']['tl_module'];

            switch ($objModule->sortingMode)
            {
                case OPTION_FORMHYBRID_SORTINGMODE_TEXT:
                    $arrDca['fields']['itemSorting']['inputType'] = 'text';
                    break;
            }
        }
    }

    public function getSortingOptions(\DataContainer $objDc)
    {
        if ($strDc = $objDc->activeRecord->formHybridDataContainer)
        {
            \Controller::loadDataContainer($strDc);
            \System::loadLanguageFile($strDc);

            $arrOptions = [];

            foreach ($GLOBALS['TL_DCA'][$strDc]['fields'] as $strField => $arrData)
            {
                $arrOptions[$strField . '_asc']  = $strField . $GLOBALS['TL_LANG']['tl_module']['itemSorting']['asc'];
                $arrOptions[$strField . '_desc'] = $strField . $GLOBALS['TL_LANG']['tl_module']['itemSorting']['desc'];
            }

            asort($arrOptions);

            return ['random' => $GLOBALS['TL_LANG']['tl_module']['itemSorting']['random']] + $arrOptions;
        }
    }

    public function getArchives(\DataContainer $objDc)
    {
        if ($strDc = $objDc->activeRecord->formHybridDataContainer)
        {
            \Controller::loadDataContainer($strDc);
            \System::loadLanguageFile($strDc);

            $arrDca = $GLOBALS['TL_DCA'][$strDc];

            if ($strParentTable = $arrDca['config']['ptable'])
            {
                if ($strItemClass = \Model::getClassFromTable($strParentTable))
                {
                    $arrOptions = [];
                    if (($objItems = $strItemClass::findAll()) !== null)
                    {
                        $arrTitleSynonyms = ['name', 'title'];

                        while ($objItems->next())
                        {
                            $strLabel = '';
                            foreach ($arrTitleSynonyms as $strTitleSynonym)
                            {
                                if ($objItems->{$strTitleSynonym})
                                {
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

    public function getFormHybridReaderModalWrapperTemplates()
    {
        return \Controller::getTemplateGroup('formhybrid_reader_modal_wrapper_');
    }

    public function getFormHybridReaderModalTemplates()
    {
        return \Controller::getTemplateGroup('formhybrid_reader_modal_');
    }

    public static function getFields($objDc)
    {
        if (!$objDc->activeRecord->formHybridDataContainer)
        {
            return [];
        }

        return \HeimrichHannot\Haste\Dca\General::getFields($objDc->activeRecord->formHybridDataContainer, false);
    }

    public static function getTextFields(\DataContainer $objDc)
    {
        if (!$objDc->activeRecord->formHybridDataContainer)
        {
            return [];
        }

        return \HeimrichHannot\Haste\Dca\General::getFields($objDc->activeRecord->formHybridDataContainer, false, 'text');
    }

    public static function getBooleanFields(\DataContainer $objDc)
    {
        if (!$objDc->activeRecord->formHybridDataContainer)
        {
            return [];
        }

        return \HeimrichHannot\Haste\Dca\General::getFields(
            $objDc->activeRecord->formHybridDataContainer,
            false,
            ['radio', 'checkbox']
        );
    }

    public static function getMultipleFields(\DataContainer $objDc)
    {
        if (!$objDc->activeRecord->formHybridDataContainer)
        {
            return [];
        }

        return \HeimrichHannot\Haste\Dca\General::getFields(
            $objDc->activeRecord->formHybridDataContainer,
            false,
            ['checkbox', 'select']
        );
    }

    public static function modifyPalette(\DataContainer $objDc)
    {
        \Controller::loadDataContainer('tl_module');
        \System::loadLanguageFile('tl_module');

        if (($objModule = \ModuleModel::findByPk($objDc->id)) !== null)
        {
            $arrDca = &$GLOBALS['TL_DCA']['tl_module'];

            if (\HeimrichHannot\Haste\Util\Module::isSubModuleOf(
                $objModule->type,
                'HeimrichHannot\FormHybridList\ModuleReader'
            )
            )
            {
                unset($arrDca['fields']['itemTemplate']['options_callback']);
                $arrDca['fields']['itemTemplate']['options'] = \Controller::getTemplateGroup('formhybrid_reader_');
            }
        }
    }
}