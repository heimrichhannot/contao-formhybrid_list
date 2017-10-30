<?php

namespace HeimrichHannot\FormHybridList\Backend;

use HeimrichHannot\Haste\Dca\General;

class Module extends \Backend
{
	public static function adjustPalettesForLists(\DataContainer $objDc)
	{
		\Controller::loadDataContainer('tl_module');
		\System::loadLanguageFile('tl_module');
		
		if (($objModule = \ModuleModel::findByPk($objDc->id)) !== null) {
			$arrDca = &$GLOBALS['TL_DCA']['tl_module'];
			
			if (\HeimrichHannot\Haste\Util\Module::isSubModuleOf(
				$objModule->type,
				'HeimrichHannot\FormHybridList\ModuleList'
			)
			) {
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
		
		if (($objModule = \ModuleModel::findByPk($objDc->id)) !== null) {
			$arrDca = &$GLOBALS['TL_DCA']['tl_module'];
			
			switch ($objModule->sortingMode) {
				case OPTION_FORMHYBRID_SORTINGMODE_TEXT:
					$arrDca['fields']['itemSorting']['inputType'] = 'text';
					break;
			}
		}
	}
	
	public function getSortingOptions(\DataContainer $objDc)
	{
		if ($strDc = $objDc->activeRecord->formHybridDataContainer) {
			\Controller::loadDataContainer($strDc);
			\System::loadLanguageFile($strDc);
			
			$arrOptions = [];
			
			foreach ($GLOBALS['TL_DCA'][$strDc]['fields'] as $strField => $arrData) {
				$arrOptions[$strField . '_asc']  = $strField . $GLOBALS['TL_LANG']['tl_module']['itemSorting']['asc'];
				$arrOptions[$strField . '_desc'] = $strField . $GLOBALS['TL_LANG']['tl_module']['itemSorting']['desc'];
			}
			
			asort($arrOptions);
			
			return ['random' => $GLOBALS['TL_LANG']['tl_module']['itemSorting']['random']] + $arrOptions;
		}
	}
	
	public function getArchives(\DataContainer $objDc)
	{
		if ($strDc = $objDc->activeRecord->formHybridDataContainer) {
			\Controller::loadDataContainer($strDc);
			\System::loadLanguageFile($strDc);
			
			$arrDca = $GLOBALS['TL_DCA'][$strDc];
			
			if ($strParentTable = $arrDca['config']['ptable']) {
				if ($strItemClass = \Model::getClassFromTable($strParentTable)) {
					$arrOptions = [];
					if (($objItems = $strItemClass::findAll()) !== null) {
						$arrTitleSynonyms = ['name', 'title'];
						
						while ($objItems->next()) {
							$strLabel = '';
							foreach ($arrTitleSynonyms as $strTitleSynonym) {
								if ($objItems->{$strTitleSynonym}) {
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
	
	public static function getFields($objDc)
	{
		if (!$objDc->activeRecord->formHybridDataContainer) {
			return [];
		}
		
		return General::getFields($objDc->activeRecord->formHybridDataContainer, false);
	}
	
	public static function getTextFields(\DataContainer $objDc)
	{
		if (!$objDc->activeRecord->formHybridDataContainer) {
			return [];
		}
		
		return General::getFields($objDc->activeRecord->formHybridDataContainer, false, 'text');
	}
	
	public static function getBooleanFields(\DataContainer $objDc)
	{
		if (!$objDc->activeRecord->formHybridDataContainer) {
			return [];
		}
		
		return General::getFields(
			$objDc->activeRecord->formHybridDataContainer,
			false,
			['radio', 'checkbox']
		);
	}
	
	public static function getMultipleFields(\DataContainer $objDc)
	{
		if (!$objDc->activeRecord->formHybridDataContainer) {
			return [];
		}
		
		return General::getFields(
			$objDc->activeRecord->formHybridDataContainer,
			false,
			['checkbox', 'select']
		);
	}
	
	public static function modifyPalette(\DataContainer $objDc)
	{
		\Controller::loadDataContainer('tl_module');
		\System::loadLanguageFile('tl_module');
		
		if (($objModule = \ModuleModel::findByPk($objDc->id)) !== null) {
			$arrDca = &$GLOBALS['TL_DCA']['tl_module'];
			
			if (\HeimrichHannot\Haste\Util\Module::isSubModuleOf(
				$objModule->type,
				'HeimrichHannot\FormHybridList\ModuleReader'
			)
			) {
				unset($arrDca['fields']['itemTemplate']['options_callback']);
				$arrDca['fields']['itemTemplate']['options'] = \Controller::getTemplateGroup('formhybrid_reader_');
			}
		}
	}
	
	public static function getLists()
	{
		if(($lists = \ModuleModel::findByType(MODULE_FORMHYBRID_LIST)) === null)
			return [];
			
		return $lists->fetchEach('name');
	}
	
	public static function getFilter()
	{
		if(($filter = \ModuleModel::findByType(MODULE_FORMHYBRID_LIST_FILTER)) === null)
			return [];
		
		return $filter->fetchEach('name');
		
	}
}