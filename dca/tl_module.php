<?php

$arrDca = &$GLOBALS['TL_DCA']['tl_module'];

/**
 * Palettes
 */
// reader
$arrDca['palettes'][MODULE_FORMHYBRID_READER] = '{title_legend},name,headline,type;{entity_legend},formHybridDataContainer;{security_legend},addShowConditions;{redirect_legend},formHybridAddFieldDependentRedirect;{misc_legend},imgSize,useDummyImage,setPageTitle;{template_legend},itemTemplate,modalTpl,customTpl;{comment_legend:hide},com_template;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$arrDca['palettes'][MODULE_FORMHYBRID_MEMBER_READER] = str_replace('imgSize', 'imgSize,memberContentArchiveTags,memberContentArchiveTeaserTag', $arrDca['palettes'][MODULE_FORMHYBRID_READER]);

// list
$arrDca['palettes'][MODULE_FORMHYBRID_LIST] = '{title_legend},name,headline,type;{entity_legend},formHybridDataContainer;{list_legend},numberOfItems,perPage,addAjaxPagination,skipFirst,skipInstances,showItemCount,emptyText,showInitialResults,formHybridAddHashToAction,isTableList,addDetailsCol;{filter_legend},sortingMode,itemSorting,hideFilter,filterHeadline,customFilterFields,hideUnpublishedItems,publishedField,invertPublishedField,filterArchives,formHybridAddDefaultValues,conjunctiveMultipleFields,addDisjunctiveFieldGroups,additionalWhereSql,additionalSelectSql,additionalSql;{misc_legend},imgSize,useDummyImage,useModal;{template_legend:hide},formHybridTemplate,formHybridCustomSubTemplates,itemTemplate,customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$arrDca['palettes'][MODULE_FORMHYBRID_MEMBER_LIST] = str_replace(array(
	'filterArchives',
	'imgSize'
), array(
	'filterGroups',
	'imgSize,memberContentArchiveTags,memberContentArchiveTeaserTag'
), $arrDca['palettes'][MODULE_FORMHYBRID_LIST]);

$arrDca['palettes'][MODULE_FORMHYBRID_NEWS_LIST] = $arrDca['palettes'][MODULE_FORMHYBRID_LIST];

/**
 * Subpalettes
 */
$arrDca['palettes']['__selector__'][] = 'isTableList';
$arrDca['palettes']['__selector__'][] = 'addDetailsCol';
$arrDca['palettes']['__selector__'][] = 'useDummyImage';
$arrDca['palettes']['__selector__'][] = 'addDisjunctiveFieldGroups';
$arrDca['palettes']['__selector__'][] = 'addShowConditions';
$arrDca['palettes']['__selector__'][] = 'useModal';
$arrDca['palettes']['__selector__'][] = 'setPageTitle';
$arrDca['palettes']['__selector__'][] = 'addAjaxPagination';

$arrDca['subpalettes']['isTableList'] = 'tableFields,hasHeader,sortingHeader';
$arrDca['subpalettes']['addDetailsCol'] = 'jumpToDetails';
$arrDca['subpalettes']['useDummyImage'] = 'dummyImage';
$arrDca['subpalettes']['addDisjunctiveFieldGroups'] = 'disjunctiveFieldGroups';
$arrDca['subpalettes']['addShowConditions'] = 'showConditions';
$arrDca['subpalettes']['useModal'] = 'modalWrapperTpl,modalClass,modalInnerClass';
$arrDca['subpalettes']['setPageTitle'] = 'pageTitleField,pageTitlePattern';
$arrDca['subpalettes']['addAjaxPagination'] = 'addInfiniteScroll';

/**
 * Callbacks
 */
// adjust labels for suiting a list module
$arrDca['config']['onload_callback'][] = array('tl_module_formhybrid_list', 'adjustPalettesForLists');
$arrDca['config']['onload_callback'][] = array('tl_module_formhybrid_list', 'initSortingMode');
$arrDca['config']['onload_callback'][] = array('tl_module_formhybrid_list', 'modifyPalette');

$arrDca['fields']['formHybridDataContainer']['load_callback']['setDefaultDataContainer'] =
	array('tl_module_formhybrid_list', 'setDefaultDataContainer');

/**
 * Fields
 */
$arrFields = array(
	'addDetailsCol' => array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['addDetailsCol'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class' => 'w50 clr', 'submitOnChange' => true),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	'jumpToDetails' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['jumpToDetails'],
		'exclude'                 => true,
		'inputType'               => 'pageTree',
		'foreignKey'              => 'tl_page.title',
		'eval'                    => array('fieldType'=>'radio', 'tl_class'=>'w50 clr'),
		'sql'                     => "int(10) unsigned NOT NULL default '0'",
		'relation'                => array('type'=>'hasOne', 'load'=>'eager')
	),
	'sortingMode' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['sortingMode'],
		'exclude'                 => true,
		'inputType'               => 'select',
		'options'                 => array(OPTION_FORMHYBRID_SORTINGMODE_FIELD, OPTION_FORMHYBRID_SORTINGMODE_TEXT),
		'reference'               => &$GLOBALS['TL_LANG']['tl_module']['sortingMode'],
		'eval'                    => array('tl_class'=>'w50', 'submitOnChange' => true),
		'sql'                     => "varchar(16) NOT NULL default 'field'"
	),
	'itemSorting' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['itemSorting'],
		'exclude'                 => true,
		'inputType'               => 'select',
		'options_callback'        => array('tl_module_formhybrid_list', 'getSortingOptions'),
		'eval'                    => array('tl_class'=>'w50', 'includeBlankOption' => true, 'chosen' => true, 'mandatory' => true),
		'sql'                     => "varchar(255) NOT NULL default ''"
	),
	'hideFilter' => array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['hideFilter'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class' => 'w50'),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	'showItemCount' => array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['showItemCount'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class' => 'w50'),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	'showInitialResults' => array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['showInitialResults'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class' => 'w50'),
		'sql'                     => "char(1) NOT NULL default '1'"
	),
	'addAjaxPagination' => array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['addAjaxPagination'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('submitOnChange' => true, 'tl_class' => 'w50'),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	'addInfiniteScroll' => array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['addInfiniteScroll'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class' => 'w50'),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	'isTableList' => array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['isTableList'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class' => 'w50 clr', 'submitOnChange' => true),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	'hasHeader' => array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['hasHeader'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class' => 'w50'),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	'sortingHeader' => array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['sortingHeader'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class' => 'w50'),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	'tableFields' => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_module']['tableFields'],
		'inputType'        => 'checkboxWizard',
		'options_callback' => array('tl_module_formhybrid_list', 'getFields'),
		'exclude'          => true,
		'eval'             => array('multiple' => true, 'includeBlankOption' => true, 'tl_class' => 'w50 clr autoheight'),
		'sql'              => "blob NULL",
	),
	'customFilterFields' => array
	(
		'inputType'        => 'checkboxWizard',
		'label'            => &$GLOBALS['TL_LANG']['tl_module']['customFilterFields'],
		'options_callback' => array('tl_module_formhybrid_list', 'getFields'),
		'exclude'          => true,
		'eval'             => array('multiple' => true, 'includeBlankOption' => true,
									'tl_class' => 'w50 autoheight clr'
		),
		'sql'              => "blob NULL"
	),
	'filterArchives'    => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['filterArchives'],
		'inputType'               => 'select',
		'options_callback'        => array('tl_module_formhybrid_list', 'getArchives'),
		'eval'                    => array('multiple' => true, 'chosen' => true, 'tl_class' => 'w50'),
		'sql'                     => "blob NULL"
	),
	'filterGroups'    => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['filterGroups'],
		'inputType'               => 'select',
		'foreignKey'              => 'tl_member_group.name',
		'eval'                    => array('multiple' => true, 'chosen' => true, 'tl_class' => 'w50', 'doNotCopy'=>true),
		'sql'                     => "blob NULL"
	),
	'setPageTitle' => array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['setPageTitle'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class' => 'w50', 'submitOnChange' => true),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	'pageTitleField' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['pageTitleField'],
		'exclude'                 => true,
		'inputType'               => 'select',
		'options_callback'        => array('tl_module_formhybrid_list', 'getTextFields'),
		'eval'                    => array('maxlength'=>255, 'tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true),
		'sql'                     => "varchar(255) NOT NULL default ''"
	),
	'pageTitlePattern' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['pageTitlePattern'],
		'exclude'                 => true,
		'inputType'               => 'text',
		'eval'                    => array('maxlength'=>255, 'tl_class' => 'w50'),
		'sql'                     => "varchar(255) NOT NULL default ''"
	),
	'additionalWhereSql' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['additionalWhereSql'],
		'exclude'                 => true,
		'inputType'               => 'text',
		'eval'                    => array('maxlength'=>255, 'tl_class' => 'w50 clr'),
		'sql'                     => "varchar(255) NOT NULL default ''"
	),
	'additionalSelectSql' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['additionalSelectSql'],
		'exclude'                 => true,
		'inputType'               => 'text',
		'eval'                    => array('maxlength'=>255, 'tl_class' => 'w50'),
		'sql'                     => "varchar(255) NOT NULL default ''"
	),
	'additionalSql' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['additionalSql'],
		'exclude'                 => true,
		'inputType'               => 'text',
		'eval'                    => array('maxlength'=>255, 'tl_class' => 'w50'),
		'sql'                     => "varchar(255) NOT NULL default ''"
	),
	'hideUnpublishedItems' => array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['hideUnpublishedItems'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class' => 'w50'),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	'publishedField' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['publishedField'],
		'exclude'                 => true,
		'inputType'               => 'select',
		'options_callback'        => array('tl_module_formhybrid_list', 'getBooleanFields'),
		'eval'                    => array('maxlength'=>255, 'tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true),
		'sql'                     => "varchar(255) NOT NULL default ''"
	),
	'invertPublishedField' => array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['invertPublishedField'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class' => 'w50'),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	'emptyText' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['emptyText'],
		'exclude'                 => true,
		'inputType'               => 'text',
		'eval'                    => array('maxlength'=>255, 'tl_class' => 'w50 clr'),
		'sql'                     => "varchar(255) NOT NULL default ''"
	),
	'filterHeadline' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['filterHeadline'],
		'exclude'                 => true,
		'inputType'               => 'inputUnit',
		'options'                 => array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'),
		'eval'                    => array('maxlength'=>200, 'tl_class'=>'w50'),
		'sql'                     => "varchar(255) NOT NULL default ''"
	),
	'itemTemplate' => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_module']['itemTemplate'],
		'default'          => 'formhybrid_default',
		'exclude'          => true,
		'inputType'        => 'select',
		'options_callback' => array('tl_module_formhybrid_list', 'getFormHybridListItemTemplates'),
		'eval'             => array('tl_class' => 'w50 clr', 'includeBlankOption' => true),
		'sql'              => "varchar(255) NOT NULL default ''",
	),
	'useDummyImage' => array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['useDummyImage'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class' => 'w50', 'submitOnChange' => true),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	'dummyImage' => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_module']['dummyImage'],
		'exclude'   => true,
		'inputType' => 'fileTree',
		'eval'      => array('tl_class' => 'w50', 'filesOnly' => true, 'extensions' => Config::get('validImageTypes'),
							 'fieldType' => 'radio', 'mandatory' => true
		),
		'sql'       => "binary(16) NULL"
	),
	'conjunctiveMultipleFields' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['conjunctiveMultipleFields'],
		'exclude'                 => true,
		'inputType'               => 'select',
		'options_callback'        => array('tl_module_formhybrid_list', 'getMultipleFields'),
		'eval'                    => array('tl_class'=>'w50', 'multiple' => true, 'chosen' => true),
		'sql'                     => "blob NULL"
	),
	'addDisjunctiveFieldGroups' => array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['addDisjunctiveFieldGroups'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class' => 'w50', 'submitOnChange' => true),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	'disjunctiveFieldGroups'  => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_module']['disjunctiveFieldGroups'],
		'inputType' => 'multiColumnWizard',
		'exclude'   => true,
		'eval'      => array
		(
			'tl_class'     => 'clr',
			'columnFields' => array
			(
				'fields'   => array
				(
					'label'     => &$GLOBALS['TL_LANG']['tl_module']['disjunctiveFieldGroups']['fields'],
					'inputType' => 'select',
					'options_callback'   => array('tl_module_formhybrid_list', 'getFields'),
					'eval'      => array
					(
						'style' => 'width:783px',
						'multiple' => true,
						'chosen' => true
					),
				),
			)
		),
		'sql'       => "blob NULL",
	),
	'memberContentArchiveTags' => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_module']['memberContentArchiveTags'],
		'inputType'        => 'select',
		'eval'             => array(
			'multiple' => true,
			'tl_class' => 'w50',
			'chosen' => true
		),
		'foreignKey' => 'tl_member_content_archive_tag.title',
		'sql'        => 'blob NULL',
	),
	'memberContentArchiveTeaserTag' => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_module']['memberContentArchiveTeaserTag'],
		'inputType'        => 'select',
		'eval'             => array(
			'tl_class' => 'w50',
			'includeBlankOption' => true
		),
		'foreignKey' => 'tl_member_content_archive_tag.title',
		'sql'        => 'blob NULL',
	),
	'useModal' => array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['useModal'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class' => 'w50 clr', 'submitOnChange' => true),
		'sql'                     => "char(1) NOT NULL default ''"
	),
	'modalWrapperTpl' => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_module']['modalWrapperTpl'],
		'exclude'          => true,
		'inputType'        => 'select',
		'default'          => 'formhybrid_reader_modal_wrapper_bootstrap',
		'options_callback' => array('tl_module_formhybrid_list', 'getFormHybridReaderModalWrapperTemplates'),
		'eval'             => array('tl_class' => 'w50 clr'),
		'sql'              => "varchar(255) NOT NULL default ''",
	),
	'modalTpl' => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_module']['modalTpl'],
		'exclude'          => true,
		'inputType'        => 'select',
		'default'          => 'formhybrid_reader_modal_bootstrap',
		'options_callback' => array('tl_module_formhybrid_list', 'getFormHybridReaderModalTemplates'),
		'eval'             => array('tl_class' => 'w50'),
		'sql'              => "varchar(255) NOT NULL default ''",
	),
	'modalClass' => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_module']['modalClass'],
		'exclude'          => true,
		'inputType'        => 'text',
		'eval'             => array('tl_class' => 'w50'),
		'sql'              => "varchar(255) NOT NULL default ''",
	),
	'modalInnerClass' => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_module']['modalInnerClass'],
		'exclude'          => true,
		'inputType'        => 'text',
		'eval'             => array('tl_class' => 'w50'),
		'sql'              => "varchar(255) NOT NULL default ''",
	)
);

$arrDca['fields'] += $arrFields;

$arrDca['fields']['formHybridCustomSubTemplates']['eval']['tl_class'] = 'w50';

$arrDca['fields']['addShowConditions']			= $arrDca['fields']['formHybridAddDefaultValues'];
$arrDca['fields']['addShowConditions']['label'] = &$GLOBALS['TL_LANG']['tl_module']['addShowConditions'];
$arrDca['fields']['showConditions']				= $arrDca['fields']['formHybridDefaultValues'];
$arrDca['fields']['showConditions']['label']	= &$GLOBALS['TL_LANG']['tl_module']['showConditions'];

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
					$objModule->type, 'HeimrichHannot\FormHybridList\ModuleList'))
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
			return array();

		return \HeimrichHannot\Haste\Dca\General::getFields($objDc->activeRecord->formHybridDataContainer, false);
	}

	public static function getTextFields(\DataContainer $objDc) {
		if (!$objDc->activeRecord->formHybridDataContainer)
			return array();

		return \HeimrichHannot\Haste\Dca\General::getFields($objDc->activeRecord->formHybridDataContainer, false, 'text');
	}

	public static function getBooleanFields(\DataContainer $objDc) {
		if (!$objDc->activeRecord->formHybridDataContainer)
			return array();

		return \HeimrichHannot\Haste\Dca\General::getFields($objDc->activeRecord->formHybridDataContainer, false,
			array('radio', 'checkbox'));
	}

	public static function getMultipleFields(\DataContainer $objDc) {
		if (!$objDc->activeRecord->formHybridDataContainer)
			return array();

		return \HeimrichHannot\Haste\Dca\General::getFields($objDc->activeRecord->formHybridDataContainer, false,
				array('checkbox', 'select'));
	}

	public static function modifyPalette(\DataContainer $objDc)
	{
		\Controller::loadDataContainer('tl_module');
		\System::loadLanguageFile('tl_module');

		if (($objModule = \ModuleModel::findByPk($objDc->id)) !== null)
		{
			$arrDca = &$GLOBALS['TL_DCA']['tl_module'];

			if (\HeimrichHannot\Haste\Util\Module::isSubModuleOf(
					$objModule->type, 'HeimrichHannot\FormHybridList\ModuleReader'))
			{
				unset($arrDca['fields']['itemTemplate']['options_callback']);
				$arrDca['fields']['itemTemplate']['options'] = \Controller::getTemplateGroup('formhybrid_reader_');
			}
		}
	}
}