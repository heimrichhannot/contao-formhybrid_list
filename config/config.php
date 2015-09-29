<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @package formhybrid_list
 * @author  Dennis Patzer <d.patzer@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

/**
 * Constants
 */
define('FORMHYBRID_LIST_NAME_FILTER', 'filter');

// module names
define('MODULE_FORMHYBRID_LIST', 'formhybrid_list');
define('MODULE_FORMHYBRID_MEMBER_LIST', 'formhybrid_list_member');

/**
 * Frontend modules
 */
$GLOBALS['FE_MOD']['miscellaneous'][MODULE_FORMHYBRID_LIST] = 'HeimrichHannot\FormHybridList\ModuleList';
$GLOBALS['FE_MOD']['miscellaneous'][MODULE_FORMHYBRID_MEMBER_LIST] = 'HeimrichHannot\FormHybridList\ModuleMemberList';

/**
 * Assets
 */
if (TL_MODE == 'FE')
{
	// css
	$GLOBALS['TL_CSS']['formhybrid_list'] = 'system/modules/formhybrid_list/assets/css/style.css';
}