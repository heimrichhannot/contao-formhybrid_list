<?php

$arrDca = &$GLOBALS['TL_DCA']['tl_module'];

/**
 * Palettes
 */
$arrDca['palettes'][MODULE_FORMHYBRID_LIST] = '{title_legend},name,headline,type;{config_legend},formHybridSkipScrollingToSuccessMessage,numberOfItems,perPage,skipFirst,skipInstances,showItemCount,showInitialResults,emptyText,addDetailsCol,formHybridDataContainer,formHybridPalette,formHybridEditable,hideFilter,sortingMode,itemSorting,addCustomFilterFields,hideUnpublishedItems,publishedField,invertPublishedField,filterArchives,imgSize,useDummyImage,formHybridAddDefaultValues,additionalWhereSql,additionalSelectSql,additionalSql;{template_legend:hide},formHybridTemplate,formHybridCustomSubTemplates,itemTemplate,customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

// members
$arrDca['palettes'][MODULE_FORMHYBRID_MEMBER_LIST] = str_replace('filterArchives', 'filterGroups', $arrDca['palettes'][MODULE_FORMHYBRID_LIST]);

// news
$arrDca['palettes'][MODULE_FORMHYBRID_NEWS_LIST] = $arrDca['palettes'][MODULE_FORMHYBRID_LIST];

/**
 * Subpalettes
 */
$arrDca['palettes']['__selector__'][] = 'addCustomFilterFields';
$arrDca['palettes']['__selector__'][] = 'addDetailsCol';
$arrDca['palettes']['__selector__'][] = 'useDummyImage';
$arrDca['subpalettes']['addCustomFilterFields'] = 'customFilterFields';
$arrDca['subpalettes']['addDetailsCol'] = 'jumpToDetails';
$arrDca['subpalettes']['useDummyImage'] = 'dummyImage';

/**
 * Callbacks
 */
// adjust labels for suiting a list module
$arrDca['config']['onload_callback'][] = array('tl_module_formhybrid_list', 'adjustPalettesForLists');
$arrDca['config']['onload_callback'][] = array('tl_module_formhybrid_list', 'initSortingMode');

$arrDca['fields']['formHybridDataContainer']['load_callback']['setDefaultDataContainer'] =
	array('tl_module_formhybrid_list', 'setDefaultDataContainer');

/**
 * Fields
 */
$arrDca['fields']['addDetailsCol'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['addDetailsCol'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class' => 'w50', 'submitOnChange' => true),
	'sql'                     => "char(1) NOT NULL default ''"
);

$arrDca['fields']['jumpToDetails'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['jumpToDetails'],
	'exclude'                 => true,
	'inputType'               => 'pageTree',
	'foreignKey'              => 'tl_page.title',
	'eval'                    => array('fieldType'=>'radio', 'tl_class'=>'w50 clr'),
	'sql'                     => "int(10) unsigned NOT NULL default '0'",
	'relation'                => array('type'=>'hasOne', 'load'=>'eager')
);

$arrDca['fields']['sortingMode'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['sortingMode'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array(OPTION_FORMHYBRID_SORTINGMODE_FIELD, OPTION_FORMHYBRID_SORTINGMODE_TEXT),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module']['sortingMode'],
	'eval'                    => array('tl_class'=>'w50', 'submitOnChange' => true),
	'sql'                     => "varchar(16) NOT NULL default 'field'"
);

$arrDca['fields']['itemSorting'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['itemSorting'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_formhybrid_list', 'getSortingOptions'),
	'eval'                    => array('tl_class'=>'w50', 'includeBlankOption' => true, 'chosen' => true),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$arrDca['fields']['hideFilter'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['hideFilter'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class' => 'w50'),
	'sql'                     => "char(1) NOT NULL default ''"
);

$arrDca['fields']['showItemCount'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['showItemCount'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class' => 'w50 clr'),
	'sql'                     => "char(1) NOT NULL default ''"
);

$arrDca['fields']['showInitialResults'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['showInitialResults'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class' => 'w50'),
	'sql'                     => "char(1) NOT NULL default '1'"
);

$arrDca['fields']['addCustomFilterFields'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['addCustomFilterFields'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class' => 'w50', 'submitOnChange' => true),
	'sql'                     => "char(1) NOT NULL default ''"
);

$arrDca['fields']['customFilterFields'] = array
(
	'inputType'        => 'checkboxWizard',
	'label'            => &$GLOBALS['TL_LANG']['tl_module']['customFilterFields'],
	'options_callback' => array('tl_form_hybrid_module', 'getFields'),
	'exclude'          => true,
	'eval'             => array('multiple' => true, 'includeBlankOption' => true,
								'tl_class' => 'w50 autoheight clr', 'mandatory' => true
	),
	'sql'              => "blob NULL"
);

$arrDca['fields']['filterArchives']    = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['filterArchives'],
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_formhybrid_list', 'getArchives'),
	'eval'                    => array('multiple' => true, 'chosen' => true, 'tl_class' => 'w50'),
	'sql'                     => "blob NULL"
);

$arrDca['fields']['filterGroups']    = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['filterGroups'],
	'inputType'               => 'select',
	'foreignKey'              => 'tl_member_group.name',
	'eval'                    => array('multiple' => true, 'chosen' => true, 'tl_class' => 'w50', 'doNotCopy'=>true),
	'sql'                     => "blob NULL"
);

$arrDca['fields']['pageTitleField'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['pageTitleField'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_formhybrid_list', 'getTextFields'),
	'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$arrDca['fields']['additionalWhereSql'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['additionalWhereSql'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class' => 'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$arrDca['fields']['additionalSelectSql'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['additionalSelectSql'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class' => 'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$arrDca['fields']['additionalSql'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['additionalSql'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class' => 'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$arrDca['fields']['hideUnpublishedItems'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['hideUnpublishedItems'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class' => 'w50'),
	'sql'                     => "char(1) NOT NULL default ''"
);

$arrDca['fields']['publishedField'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['publishedField'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_formhybrid_list', 'getBooleanFields'),
	'eval'                    => array('maxlength'=>255, 'tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$arrDca['fields']['invertPublishedField'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['invertPublishedField'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class' => 'w50'),
	'sql'                     => "char(1) NOT NULL default ''"
);

$arrDca['fields']['emptyText'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['emptyText'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class' => 'w50 clr'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$arrDca['fields']['itemTemplate'] = array
(
	'label'            => &$GLOBALS['TL_LANG']['tl_module']['itemTemplate'],
	'default'          => 'formhybrid_default',
	'exclude'          => true,
	'inputType'        => 'select',
	'options_callback' => array('tl_module_formhybrid_list', 'getFormHybridListItemTemplates'),
	'eval'             => array('tl_class' => 'w50 clr'),
	'sql'              => "varchar(255) NOT NULL default ''",
);

$arrDca['fields']['useDummyImage'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['useDummyImage'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class' => 'w50', 'submitOnChange' => true),
	'sql'                     => "char(1) NOT NULL default ''"
);

$arrDca['fields']['dummyImage'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_module']['dummyImage'],
	'exclude'   => true,
	'inputType' => 'fileTree',
	'eval'      => array('tl_class' => 'w50', 'filesOnly' => true, 'extensions' => Config::get('validImageTypes'),
						 'fieldType' => 'radio', 'mandatory' => true
	),
	'sql'       => "binary(16) NULL"
);

$arrDca['fields']['formHybridCustomSubTemplates']['eval']['tl_class'] = 'w50';

class tl_module_formhybrid_list {

	public function setDefaultDataContainer($varValue, $objDc)
	{
		if (TL_MODE == 'BE' && !$varValue && strpos($objDc->activeRecord->type, 'formhybrid_list_') !== false)
		{
			preg_match('@formhybrid_list_(.*)@', $objDc->activeRecord->type, $arrResult);

			if (is_array($arrResult) && count($arrResult) > 1)
				return 'tl_' . $arrResult[1];
		}

		return $varValue;
	}

	public static function adjustPalettesForLists(\DataContainer $objDc)
	{
		\Controller::loadDataContainer('tl_module');
		\System::loadLanguageFile('tl_module');

		if (($objModule = \ModuleModel::findByPk($objDc->id)) !== null)
		{
			$arrDca = &$GLOBALS['TL_DCA']['tl_module'];

			if (\HeimrichHannot\Haste\Util\Module::isSubModuleOf(
					$objModule->type, 'formhybrid_list', 'HeimrichHannot\FormHybridList\ModuleList'))
			{
				// override labels for suiting a list module
				$arrDca['fields']['formHybridAddDefaultValues']['label'] = &$GLOBALS['TL_LANG']['tl_module']['formHybridAddDefaultFilterValues'];
				$arrDca['fields']['formHybridDefaultValues']['label'] = &$GLOBALS['TL_LANG']['tl_module']['formHybridDefaultFilterValues'];
				$arrDca['fields']['formHybridTemplate']['label'] = &$GLOBALS['TL_LANG']['tl_module']['formHybridFilterTemplate'];
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

			switch ($objModule->sortingMode) {
				case OPTION_FORMHYBRID_SORTINGMODE_TEXT:
					$arrDca['fields']['itemSorting']['inputType'] = 'text';
					break;
			}
		}
	}

	public function getSortingOptions(\DataContainer $objDc) {
		if ($strDc = $objDc->activeRecord->formHybridDataContainer)
		{
			\Controller::loadDataContainer($strDc);
			\System::loadLanguageFile($strDc);

			$arrOptions = array();

			foreach($GLOBALS['TL_DCA'][$strDc]['fields'] as $strField => $arrData) {
				$strLabel = $GLOBALS['TL_LANG'][$strDc][$strField][0] ?: $strField;
				$arrOptions[$strField . '_asc']  = $strLabel . $GLOBALS['TL_LANG']['tl_module']['itemSorting']['asc'];
				$arrOptions[$strField . '_desc'] = $strLabel . $GLOBALS['TL_LANG']['tl_module']['itemSorting']['desc'];
			}

			asort($arrOptions);

			return array('random' => $GLOBALS['TL_LANG']['tl_module']['itemSorting']['random']) + $arrOptions;
		}
	}

	public function getArchives(\DataContainer $objDc) {
		if ($strDc = $objDc->activeRecord->formHybridDataContainer)
		{
			\Controller::loadDataContainer($strDc);
			\System::loadLanguageFile($strDc);

			$arrDca = $GLOBALS['TL_DCA'][$strDc];

			if ($strParentTable = $arrDca['config']['ptable'])
			{
				if ($strItemClass = \Model::getClassFromTable($strParentTable))
				{
					$arrOptions = array();
					if (($objItems = $strItemClass::findAll()) !== null)
					{
						$arrTitleSynonyms = array('name', 'title');

						while($objItems->next())
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

	public static function getTextFields(\DataContainer $objDc) {
		return static::getFields($objDc, 'text');
	}

	public static function getBooleanFields(\DataContainer $objDc) {
		return static::getFields($objDc, array('radio', 'checkbox'));
	}

	public static function getFields($objDc, $varInputType) {
		if ($strDc = $objDc->activeRecord->formHybridDataContainer)
		{
			\Controller::loadDataContainer($strDc);
			\System::loadLanguageFile($strDc);

			$arrOptions = array();

			foreach($GLOBALS['TL_DCA'][$strDc]['fields'] as $strField => $arrData) {
				if (is_array($varInputType) ? !in_array($arrData['inputType'], $varInputType) : $arrData['inputType'] != $varInputType)
					continue;

				$arrOptions[$strField] = $GLOBALS['TL_LANG'][$strDc][$strField][0] ?: $strField;
			}

			asort($arrOptions);

			return $arrOptions;
		}
	}

	public function getFormHybridListItemTemplates()
	{
		return \Controller::getTemplateGroup('formhybrid_list_item_');
	}
}