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


class ContentFormHybridStop extends \ContentElement
{
	protected $strTemplate = 'ce_formhybrid_stop';

	public function generate()
	{
		if(TL_MODE == 'BE')
		{
			return '';
		}

		return parent::generate();
	}

	protected function compile()
	{
		$objContentStart = \Database::getInstance()->prepare("SELECT * FROM tl_content WHERE pid=? AND type=? ORDER BY sorting")->limit(1)->execute($this->pid, 'formhybridStart');

		if($objContentStart->numRows === 0) return;

		$objModule = \ModuleModel::findByPk($objContentStart->formhybridModule);

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

        $objModule->renderStop = true;
        $objModule = new $strClass($objModule, $objArticle->inColumn);

		$this->Template->content = $objModule->generate();
	}
}