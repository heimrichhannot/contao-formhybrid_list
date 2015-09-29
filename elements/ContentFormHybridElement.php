<?php
/**
 * Contao Open Source CMS
 * 
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package anwaltverein
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\FormHybrid;


class ContentFormHybridElement extends \ContentElement
{
	protected $strTemplate = 'ce_formhybrid_element';

	public function generate()
	{
		$strClass = $GLOBALS['TL_FORMHYBRID_ELEMENTS'][$this->formhybridElement];

		if(!class_exists($strClass))
		{
			return '';
		}

		$objElement = new $strClass($this->objModel);

		return $objElement->generate();
	}



	/**
	 * Generate the content element
	 */
	protected function compile(){}
}