<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package Formhybrid_list
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'HeimrichHannot',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Modules
	'HeimrichHannot\FormHybridList\ModuleMemberList'           => 'system/modules/formhybrid_list/modules/ModuleMemberList.php',
	'HeimrichHannot\FormHybridList\ModuleNews'                 => 'system/modules/formhybrid_list/modules/ModuleNews.php',
	'HeimrichHannot\FormHybridList\ModuleList'                 => 'system/modules/formhybrid_list/modules/ModuleList.php',
	'HeimrichHannot\FormHybridList\ModuleNewsList'             => 'system/modules/formhybrid_list/modules/ModuleNewsList.php',

	// Classes
	'HeimrichHannot\FormHybridList\ListFilterForm'             => 'system/modules/formhybrid_list/classes/ListFilterForm.php',

	// Models
	'HeimrichHannot\FormHybridList\FormHybridListModel'        => 'system/modules/formhybrid_list/models/FormHybridListModel.php',
	'HeimrichHannot\FormHybridList\FormHybridListQueryBuilder' => 'system/modules/formhybrid_list/models/FormHybridListQueryBuilder.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_formhybrid_list_table'          => 'system/modules/formhybrid_list/templates',
	'formhybrid_list_item_default'       => 'system/modules/formhybrid_list/templates',
	'formhybrid_list_filter'             => 'system/modules/formhybrid_list/templates',
	'formhybrid_list_item_table_default' => 'system/modules/formhybrid_list/templates',
	'mod_formhybrid_list'                => 'system/modules/formhybrid_list/templates',
));
