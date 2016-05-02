<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) Heimrich & Hannot GmbH
 * @package formhybrid_list
 * @author Dennis Patzer
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\FormHybridList;

use HeimrichHannot\Haste\Util\Files;

class Helper extends \Controller
{
	public static function getFormattedValueByDca($value, $strTable, $arrData, $objItem, $objDc)
	{
		$value = deserialize($value);
		$rgxp = $arrData['eval']['rgxp'];
		$opts = $arrData['options'];
		$rfrc = $arrData['reference'];

		$rgxp = $arrData['eval']['rgxp'];

		// Call the options_callback to get the formated value
		if ((is_array($arrData['options_callback']) || is_callable($arrData['options_callback']))) {
			if (is_array($arrData['options_callback'])) {
				$strClass  = $arrData['options_callback'][0];
				$strMethod = $arrData['options_callback'][1];

				$objInstance = \Controller::importStatic($strClass);

				try {
					$opts = @$objInstance->$strMethod($objDc);
				} catch (\Exception $e)
				{
					\System::log("$strClass::$strMethod raised an Exception: $e->getMessage()", __METHOD__, TL_ERROR);
				}
			} elseif (is_callable($arrData['options_callback'])) {
				try {
					$opts = @$arrData['options_callback']($objDc);
				} catch (\Exception $e)
				{
					$strCallback = serialize($arrData['options_callback']);
					\System::log("$strCallback raised an Exception: $e->getMessage()", __METHOD__, TL_ERROR);
				}
			}
		}

		if ($rgxp == 'date')
		{
			$value = \Date::parse(\Config::get('dateFormat'), $value);
		}
		elseif ($rgxp == 'time')
		{
			$value = \Date::parse(\Config::get('timeFormat'), $value);
		}
		elseif ($rgxp == 'datim')
		{
			$value = \Date::parse(\Config::get('datimFormat'), $value);
		}
		elseif ($arrData['inputType'] == 'tag')
		{
			if (($arrTags = \HeimrichHannot\TagsPlus\TagsPlus::loadTags($strTable, $objItem->id)) !== null)
				$value = implode(', ', $arrTags);
		}
		elseif ($arrData['inputType'] == 'multifileupload')
		{
			if (is_array($value))
			{
				$value = array_map(function($val) {
					return Files::getPathFromUuid($val);
				}, $value);
			}
			else
			{
				$value = Files::getPathFromUuid($value);
			}
		}
		elseif (is_array($value))
		{
			if (!$rfrc)
			{
				$value = array_map(function($value) use ($opts) {
					return isset($opts[$value]) ? $opts[$value] : $value;
				}, $value);
			}

			$value = static::flattenArray($value);

			$value = array_filter($value); // remove empty elements

			$value = implode(', ', array_map(function($value) use ($rfrc) {
				if (is_array($rfrc))
				{
					return isset($rfrc[$value]) ? ((is_array($rfrc[$value])) ? $rfrc[$value][0] : $rfrc[$value]) : $value;
				}
				else
					return $value;
			}, $value));
		}
		elseif (is_array($opts) && array_is_assoc($opts))
		{
			$value = isset($opts[$value]) ? $opts[$value] : $value;
		}
		elseif (is_array($rfrc))
		{
			$value = isset($rfrc[$value]) ? ((is_array($rfrc[$value])) ? $rfrc[$value][0] : $rfrc[$value]) : $value;
		}
		elseif ($arrData['inputType'] == 'fileTree')
		{
			if ($arrData['eval']['multiple'] && is_array($value))
			{
				$value = array_map(function($val) {
					$strPath = Files::getPathFromUuid($val);
					return $strPath ?: $val;
				}, $value);
			}
			else
			{
				$strPath = Files::getPathFromUuid($value);
				$value = $strPath ?: $value;
			}
		}
		elseif (\Validator::isBinaryUuid($value))
		{
			$value = \StringUtil::binToUuid($value);
		}

		// Convert special characters (see #1890)
		return $value;
	}

	public static function flattenArray(array $array)
	{
		$return = array();
		array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
		return $return;
	}

	public static function getItem($strTable, $intId)
	{
		$strItemClass = \Model::getClassFromTable($strTable);
		return $strItemClass::findByPk($intId);
	}
}
