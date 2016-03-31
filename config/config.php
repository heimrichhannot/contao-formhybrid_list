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
define('FORMHYBRID_LIST_BUTTON_FILTER', 'filter');

// module names
define('MODULE_FORMHYBRID_READER', 'formhybrid_reader');
define('MODULE_FORMHYBRID_MEMBER_READER', 'formhybrid_member_reader');
define('MODULE_FORMHYBRID_LISTS', 'formhybrid_lists');
define('MODULE_FORMHYBRID_LIST', 'formhybrid_list');
define('MODULE_FORMHYBRID_MEMBER_LIST', 'formhybrid_list_member');
define('MODULE_FORMHYBRID_NEWS_LIST', 'formhybrid_list_news');
define('OPTION_FORMHYBRID_SORTINGMODE_FIELD', 'field');
define('OPTION_FORMHYBRID_SORTINGMODE_TEXT', 'text');

/**
 * Frontend modules
 */
// reader
$GLOBALS['FE_MOD']['formhybrid_list'][MODULE_FORMHYBRID_READER] = 'HeimrichHannot\FormHybridList\ModuleReader';
$GLOBALS['FE_MOD']['formhybrid_list'][MODULE_FORMHYBRID_MEMBER_READER] = 'HeimrichHannot\FormHybridList\ModuleMemberReader';

// list
$GLOBALS['FE_MOD']['formhybrid_list'][MODULE_FORMHYBRID_LIST] = 'HeimrichHannot\FormHybridList\ModuleList';
$GLOBALS['FE_MOD']['formhybrid_list'][MODULE_FORMHYBRID_MEMBER_LIST] = 'HeimrichHannot\FormHybridList\ModuleMemberList';
$GLOBALS['FE_MOD']['formhybrid_list'][MODULE_FORMHYBRID_NEWS_LIST] = 'HeimrichHannot\FormHybridList\ModuleNewsList';

/**
 * Assets
 */
if (TL_MODE == 'FE')
{
	// css
	$GLOBALS['TL_CSS']['formhybrid_list'] = 'system/modules/formhybrid_list/assets/css/style.css' . (version_compare(VERSION, '3.5', '>=') ? '|static' : '');
}
