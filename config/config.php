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
/**
 * Constants
 */
define('FORMHYBRID_LIST_BUTTON_RESET_FILTER', 'reset_filter');
// module names
/** @deprecated Use ModuleReader::TYPE instead */
define('MODULE_FORMHYBRID_READER', \HeimrichHannot\FormHybridList\ModuleReader::TYPE);
define('MODULE_FORMHYBRID_MEMBER_READER', 'formhybrid_member_reader');
define('MODULE_FORMHYBRID_LISTS', 'formhybrid_lists');
define('MODULE_FORMHYBRID_LIST', 'formhybrid_list');
define('MODULE_FORMHYBRID_MEMBER_LIST', 'formhybrid_list_member');
define('MODULE_FORMHYBRID_NEWS_LIST', 'formhybrid_list_news');
define('MODULE_FORMHYBRID_LIST_FILTER', 'formhybrid_list_filter');
define('OPTION_FORMHYBRID_SORTINGMODE_FIELD', 'field');
define('OPTION_FORMHYBRID_SORTINGMODE_TEXT', 'text');
define('OPTION_FORMHYBRID_FILTERMODE_STANDARD', 'standard');
define('OPTION_FORMHYBRID_FILTERMODE_MODULE', 'module');
define('FORMHYBRID_LIST_FREE_TEXT_FIELD', 'freetextSearch');

/**
 * Frontend modules
 */
// reader
$GLOBALS['FE_MOD']['formhybrid_list'][\HeimrichHannot\FormHybridList\ModuleReader::TYPE]        = 'HeimrichHannot\FormHybridList\ModuleReader';
$GLOBALS['FE_MOD']['formhybrid_list'][MODULE_FORMHYBRID_MEMBER_READER] = 'HeimrichHannot\FormHybridList\ModuleMemberReader';
// list
$GLOBALS['FE_MOD']['formhybrid_list'][MODULE_FORMHYBRID_LIST]        = 'HeimrichHannot\FormHybridList\ModuleList';
$GLOBALS['FE_MOD']['formhybrid_list'][MODULE_FORMHYBRID_MEMBER_LIST] = 'HeimrichHannot\FormHybridList\ModuleMemberList';
$GLOBALS['FE_MOD']['formhybrid_list'][MODULE_FORMHYBRID_NEWS_LIST]   = 'HeimrichHannot\FormHybridList\ModuleNewsList';
// filter
$GLOBALS['FE_MOD']['formhybrid_list'][MODULE_FORMHYBRID_LIST_FILTER] = 'HeimrichHannot\FormHybridList\ModuleListFilter';
/**
 * Assets
 */
$assetsPath = version_compare(VERSION, '4.0', '<') ? 'assets/components' : 'assets';

if (TL_MODE == 'FE') {
    // css
    $GLOBALS['TL_USER_CSS']['formhybrid_list'] =
        'system/modules/formhybrid_list/assets/css/style.css|static';
    // js
    $GLOBALS['TL_JAVASCRIPT']['huh_components_jscroll'] =
        $assetsPath.'/jscroll/dist/jquery.jscroll.min.js|static';
    $GLOBALS['TL_JAVASCRIPT']['huh_components_masonry'] =
        $assetsPath.'/masonry/dist/masonry.pkgd.min.js|static';
    $GLOBALS['TL_JAVASCRIPT']['huh_components_imagesloaded'] =
        $assetsPath.'/imagesloaded/dist/imagesloaded.pkgd.min.js|static';
    $GLOBALS['TL_JAVASCRIPT']['formhybrid_list'] = 'system/modules/formhybrid_list/assets/js/jquery.formhybrid_list.min.js|static';
}


$GLOBALS['TL_COMPONENTS']['formhybrid_list'] = [
    'js'  => [
        $assetsPath.'/jscroll/dist/jquery.jscroll.min.js|static',
        $assetsPath.'/masonry/dist/masonry.pkgd.min.js|static',
        $assetsPath.'/imagesloaded/dist/imagesloaded.pkgd.min.js|static',
        'system/modules/formhybrid_list/assets/js/jquery.formhybrid_list.min.js|static'
    ],
    'css' => [
        'system/modules/formhybrid_list/assets/css/style.css|static'
    ]
];

/**
 * Insert tags
 */
$GLOBALS['TL_HOOKS']['replaceInsertTags']['formHybridList'] = ['HeimrichHannot\FormHybridList\FormHybridList', 'addInsertTags'];
/**
 * Misc
 */
$GLOBALS['FORMHYBRID_LIST']['ENTITY_ID_FILTER_MAPPING'] = [
    'tl_calendar_events' => '%title% (%startDate%) : ID%id%',
    'tl_member' => '%firstname% %lastname% (%email%) : ID%id%',
    'tl_news' => '%headline% (%date%) : ID%id%',
];
