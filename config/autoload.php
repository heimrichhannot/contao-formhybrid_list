<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces([
    'HeimrichHannot',
]);


/**
 * Register the classes
 */
ClassLoader::addClasses([
    // Modules
    'HeimrichHannot\FormHybridList\ModuleMemberList'           => 'system/modules/formhybrid_list/modules/ModuleMemberList.php',
    'HeimrichHannot\FormHybridList\ModuleNews'                 => 'system/modules/formhybrid_list/modules/ModuleNews.php',
    'HeimrichHannot\FormHybridList\ModuleList'                 => 'system/modules/formhybrid_list/modules/ModuleList.php',
    'HeimrichHannot\FormHybridList\ModuleNewsList'             => 'system/modules/formhybrid_list/modules/ModuleNewsList.php',
    'HeimrichHannot\FormHybridList\ModuleReader'               => 'system/modules/formhybrid_list/modules/ModuleReader.php',
    'HeimrichHannot\FormHybridList\ModuleMemberReader'         => 'system/modules/formhybrid_list/modules/ModuleMemberReader.php',
    'HeimrichHannot\FormHybridList\ModuleListFilter'           => 'system/modules/formhybrid_list/modules/ModuleListFilter.php',
    
    // Classes
    'HeimrichHannot\FormHybridList\FormHybridList'             => 'system/modules/formhybrid_list/classes/FormHybridList.php',
    'HeimrichHannot\FormHybridList\Comments'                   => 'system/modules/formhybrid_list/classes/Comments.php',
    'HeimrichHannot\FormHybridList\ListFilterForm'             => 'system/modules/formhybrid_list/classes/ListFilterForm.php',
    'HeimrichHannot\FormHybridList\Backend\Module'             => 'system/modules/formhybrid_list/classes/backend/Module.php',
    'HeimrichHannot\FormHybridList\RandomPagination'           => 'system/modules/formhybrid_list/classes/RandomPagination.php',
    
    // Models
    'HeimrichHannot\FormHybridList\FormHybridListModel'        => 'system/modules/formhybrid_list/models/FormHybridListModel.php',
    'HeimrichHannot\FormHybridList\FormHybridFilterModel'      => 'system/modules/formhybrid_list/models/FormHybridFilterModel.php',
    'HeimrichHannot\FormHybridList\FormHybridListQueryBuilder' => 'system/modules/formhybrid_list/models/FormHybridListQueryBuilder.php',
]);


/**
 * Register the templates
 */
TemplateLoader::addFiles([
    'pagination_ajax'                    => 'system/modules/formhybrid_list/templates',
    'com_formhybrid_list'                => 'system/modules/formhybrid_list/templates',
    'formhybrid_list_default'            => 'system/modules/formhybrid_list/templates',
    'mod_formhybrid_list_table'          => 'system/modules/formhybrid_list/templates',
    'formhybrid_list_item_default'       => 'system/modules/formhybrid_list/templates',
    'formhybrid_reader_default'          => 'system/modules/formhybrid_list/templates',
    'formhybrid_list_item_table_default' => 'system/modules/formhybrid_list/templates',
    'mod_formhybrid_reader'              => 'system/modules/formhybrid_list/templates',
    'mod_formhybrid_list'                => 'system/modules/formhybrid_list/templates',
    'mod_formhybrid_filter'              => 'system/modules/formhybrid_list/templates',
]);
