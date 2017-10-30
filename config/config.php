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
define('MODULE_FORMHYBRID_READER', 'formhybrid_reader');
define('MODULE_FORMHYBRID_MEMBER_READER', 'formhybrid_member_reader');
define('MODULE_FORMHYBRID_LISTS', 'formhybrid_lists');
define('MODULE_FORMHYBRID_LIST', 'formhybrid_list');
define('MODULE_FORMHYBRID_MEMBER_LIST', 'formhybrid_list_member');
define('MODULE_FORMHYBRID_NEWS_LIST', 'formhybrid_list_news');
define('MODULE_FORMHYBRID_LIST_FILTER','formhybrid_list_filter');
define('OPTION_FORMHYBRID_SORTINGMODE_FIELD', 'field');
define('OPTION_FORMHYBRID_SORTINGMODE_TEXT', 'text');

/**
 * Frontend modules
 */
// reader
$GLOBALS['FE_MOD']['formhybrid_list'][MODULE_FORMHYBRID_READER]        = 'HeimrichHannot\FormHybridList\ModuleReader';
$GLOBALS['FE_MOD']['formhybrid_list'][MODULE_FORMHYBRID_MEMBER_READER] = 'HeimrichHannot\FormHybridList\ModuleMemberReader';

// list
$GLOBALS['FE_MOD']['formhybrid_list'][MODULE_FORMHYBRID_LIST]        = 'HeimrichHannot\FormHybridList\ModuleList';
$GLOBALS['FE_MOD']['formhybrid_list'][MODULE_FORMHYBRID_MEMBER_LIST] = 'HeimrichHannot\FormHybridList\ModuleMemberList';
$GLOBALS['FE_MOD']['formhybrid_list'][MODULE_FORMHYBRID_NEWS_LIST]   = 'HeimrichHannot\FormHybridList\ModuleNewsList';

// filter
$GLOBALS['FE_MOD']['formhybrid_list'][MODULE_FORMHYBRID_LIST_FILTER]        = 'HeimrichHannot\FormHybridList\ModuleFilter';

/**
 * Assets
 */
if (TL_MODE == 'FE')
{
    $strMasonryPath = version_compare(VERSION, '4.0', '<') ? 'assets/components/masonry' : 'assets/masonry';
    $strImagesLoadedPath = version_compare(VERSION, '4.0', '<') ? 'assets/components/masonry-imagesloaded' : 'assets/masonry-imagesloaded';

    // css
    $GLOBALS['TL_USER_CSS']['formhybrid_list'] = 'system/modules/formhybrid_list/assets/css/style.css|static';

    // js
    $GLOBALS['TL_JAVASCRIPT']['formhybrid_list_infinite_scroll'] =
        'system/modules/formhybrid_list/assets/vendor/jscroll-2.3.5/jquery.jscroll.min.js|static';

    $GLOBALS['TL_JAVASCRIPT']['formhybrid_list_masonry'] = $strMasonryPath . '/masonry.pkgd.min.js|static';

    $GLOBALS['TL_JAVASCRIPT']['formhybrid_list_masonry_imagesloaded'] = $strImagesLoadedPath . '/imagesloaded.pkgd.min.js|static';

    $GLOBALS['TL_JAVASCRIPT']['formhybrid_list'] = 'system/modules/formhybrid_list/assets/js/jquery.formhybrid_list.js|static';
}

/**
 * Insert tags
 */
$GLOBALS['TL_HOOKS']['replaceInsertTags']['formHybridList'] = ['HeimrichHannot\FormHybridList\FormHybridList', 'addInsertTags'];
