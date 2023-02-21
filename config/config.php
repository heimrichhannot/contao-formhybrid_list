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

use HeimrichHannot\FormHybridList\ModuleList;
use HeimrichHannot\FormHybridList\ModuleListFilter;
use HeimrichHannot\FormHybridList\ModuleMemberList;
use HeimrichHannot\FormHybridList\ModuleMemberReader;
use HeimrichHannot\FormHybridList\ModuleNewsList;
use HeimrichHannot\FormHybridList\ModuleReader;

/**
 * Constants
 */
define('FORMHYBRID_LIST_BUTTON_RESET_FILTER', 'reset_filter');
// module names
if (!defined('MODULE_FORMHYBRID_READER')) {
    /** @deprecated Use ModuleReader::TYPE instead */
    define('MODULE_FORMHYBRID_READER', ModuleReader::TYPE);
}
if (!defined('MODULE_FORMHYBRID_MEMBER_READER')) {
    /** @deprecated Use ModuleMemberReader::TYPE instead */
    define('MODULE_FORMHYBRID_MEMBER_READER', ModuleMemberReader::TYPE);
}
define('MODULE_FORMHYBRID_LISTS', 'formhybrid_lists');

if (!defined('MODULE_FORMHYBRID_LIST')) {
    /** @deprecated Use ModuleList::TYPE instead */
    define('MODULE_FORMHYBRID_LIST', ModuleList::TYPE);
}
if (!defined('MODULE_FORMHYBRID_MEMBER_LIST')) {
    /** @deprecated Use ModuleMemberList::TYPE instead */
    define('MODULE_FORMHYBRID_MEMBER_LIST', ModuleMemberList::TYPE);
}
if (!defined('MODULE_FORMHYBRID_NEWS_LIST')) {
    /** @deprecated Use ModuleNewsList::TYPE instead */
    define('MODULE_FORMHYBRID_NEWS_LIST', ModuleNewsList::TYPE);
}
if (!defined('MODULE_FORMHYBRID_LIST_FILTER')) {
    /** @deprecated Use ModuleListFilter::TYPE instead */
    define('MODULE_FORMHYBRID_LIST_FILTER', ModuleListFilter::TYPE);
}
define('OPTION_FORMHYBRID_SORTINGMODE_FIELD', 'field');
define('OPTION_FORMHYBRID_SORTINGMODE_TEXT', 'text');
define('OPTION_FORMHYBRID_FILTERMODE_STANDARD', 'standard');
define('OPTION_FORMHYBRID_FILTERMODE_MODULE', 'module');
define('FORMHYBRID_LIST_FREE_TEXT_FIELD', 'freetextSearch');

/**
 * Frontend modules
 */
// reader
$GLOBALS['FE_MOD']['formhybrid_list'][ModuleReader::TYPE]       = ModuleReader::class;
$GLOBALS['FE_MOD']['formhybrid_list'][ModuleMemberReader::TYPE] = ModuleMemberReader::class;
// list
$GLOBALS['FE_MOD']['formhybrid_list'][ModuleList::TYPE]       = ModuleList::class;
$GLOBALS['FE_MOD']['formhybrid_list'][ModuleMemberList::TYPE] = ModuleMemberList::class;
$GLOBALS['FE_MOD']['formhybrid_list'][ModuleNewsList::TYPE]  = ModuleNewsList::class;
// filter
$GLOBALS['FE_MOD']['formhybrid_list'][ModuleListFilter::TYPE] = ModuleListFilter::class;
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
$GLOBALS['TL_HOOKS']['loadDataContainer']['formHybridList'] = [
    \HeimrichHannot\FormHybridList\EventListener\Contao\LoadDataContainerListener::class,
    '__invoke'
];
/**
 * Misc
 */
$GLOBALS['FORMHYBRID_LIST']['ENTITY_ID_FILTER_MAPPING'] = [
    'tl_calendar_events' => '%title% (%startDate%) : ID%id%',
    'tl_member' => '%firstname% %lastname% (%email%) : ID%id%',
    'tl_news' => '%headline% (%date%) : ID%id%',
];
