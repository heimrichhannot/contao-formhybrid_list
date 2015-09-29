<?php
/**
 * Contao Open Source CMS
 * 
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package formhybrid
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\FormHybrid;


class ContentFormHybridStart extends \ContentElement
{
	protected $strTemplate = 'ce_formhybrid_start';

	public function generate()
	{
		if(TL_MODE == 'BE')
		{
			$objContentStop = \Database::getInstance()->prepare("SELECT * FROM tl_content WHERE pid=? AND type=? AND sorting > ? ORDER BY sorting")->limit(1)->execute($this->pid, 'formhybridStop', $this->sorting);

			if($objContentStop->numRows === 0) return;

			$objModule = \ModuleModel::findByPk($this->formhybridModule);

			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD'][$objModule->type][0]) . ' ###';
			$objTemplate->title = $objModule->headline;
			$objTemplate->id = $objModule->id;
			$objTemplate->link = $objModule->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $objModule->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}

	protected function compile()
	{
		$objContentStop = \Database::getInstance()->prepare("SELECT * FROM tl_content WHERE pid=? AND type=? AND sorting > ? ORDER BY sorting")->limit(1)->execute($this->pid, 'formhybridStop', $this->sorting);

		if($objContentStop->numRows === 0) return;

		$objModule = \ModuleModel::findByPk($this->formhybridModule);

		if($objModule === null) return;

        $objModule->refresh();

		$strClass = \Module::findClass($objModule->type);


		// Return if the class does not exist
		if (!class_exists($strClass))
		{
			static::log('Module class "'.$strClass.'" (module "'.$objModule->type.'") does not exist', __METHOD__, TL_ERROR);
			return '';
		}

		$objArticle = \ArticleModel::findByPk($this->pid);

		if($objArticle === null) return;

        $objModule->renderStart = true;

        $objModule = new $strClass($objModule, $objArticle->inColumn);

		$this->Template->content = $objModule->generate();
	}
}