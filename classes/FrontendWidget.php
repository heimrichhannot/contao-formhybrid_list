<?php

namespace HeimrichHannot\FormHybrid;


/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package formhybrid
 * @author Dennis Patzer <d.patzer@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

abstract class FrontendWidget extends \Widget
{
	/**
	 * Validate the user input and set the value
	 */
	public static function validateGetAndPost($objWidget, $strMethod)
	{
		if ($strMethod == FORMHYBRID_METHOD_GET)
			$varValue = $objWidget->validator(static::getGet($objWidget, $objWidget->strName));
		else
			$varValue = $objWidget->validator($objWidget->getPost($objWidget->strName));

		if ($objWidget->hasErrors()) {
			$objWidget->class = 'error';
		}

		$objWidget->varValue = $varValue;
	}


	/**
	 * Find and return a $_GET variable
	 *
	 * @param string $strKey The variable name
	 *
	 * @return mixed The variable value
	 */
	protected static function getGet($objWidget, $strKey)
	{
		// Support arrays (thanks to Andreas Schempp)
		$arrParts = explode('[', str_replace(']', '', $strKey));

		if (!empty($arrParts)) {
			$varValue = \Input::get(
				array_shift($arrParts), $objWidget->decodeEntities
			);

			foreach ($arrParts as $part) {
				if (!is_array($varValue)) {
					break;
				}

				$varValue = $varValue[$part];
			}

			return $varValue;
		}

		return \Input::get($strKey, $objWidget->decodeEntities);
	}

	/**
	 * Check whether an option is checked
	 *
	 * @param array $arrOption The options array
	 *
	 * @return string The "checked" attribute or an empty string
	 */
	protected function isChecked($arrOption)
	{
		if (empty($this->varValue) && empty($_GET) && $arrOption['default']) {
			return static::optionChecked(1, 1);
		}

		return static::optionChecked($arrOption['value'], $this->varValue);
	}


	/**
	 * Check whether an option is selected
	 *
	 * @param array $arrOption The options array
	 *
	 * @return string The "selected" attribute or an empty string
	 */
	protected function isSelected($arrOption)
	{
		if (empty($this->varValue) && empty($_GET) && $arrOption['default']) {
			return static::optionSelected(1, 1);
		}

		return static::optionSelected($arrOption['value'], $this->varValue);
	}

}
