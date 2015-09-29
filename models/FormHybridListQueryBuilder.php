<?php

namespace HeimrichHannot\FormHybridList;


class FormHybridListQueryBuilder
{

	public static function find(array $arrOptions)
	{
		$objBase = new \DcaExtractor($arrOptions['table']);
		$strAdditionalSql = $arrOptions['additionalSql'] ? ' ' . $arrOptions['additionalSql']: '';

		if (!$objBase->hasRelations())
		{
			$strQuery = "SELECT * FROM " . $arrOptions['table'] . $strAdditionalSql;
		}
		else
		{
			$arrJoins = array();
			$arrFields = array($arrOptions['table'] . ".*");
			$intCount = 0;

			foreach ($objBase->getRelations() as $strKey=>$arrConfig)
			{
				// Automatically join the single-relation records
				if ($arrConfig['load'] == 'eager' || $arrOptions['eager'])
				{
					if ($arrConfig['type'] == 'hasOne' || $arrConfig['type'] == 'belongsTo')
					{
						++$intCount;
						$objRelated = new \DcaExtractor($arrConfig['table']);

						foreach (array_keys($objRelated->getFields()) as $strField)
						{
							$arrFields[] = 'j' . $intCount . '.' . $strField . ' AS ' . $strKey . '__' . $strField;
						}

						$arrJoins[] = " LEFT JOIN " . $arrConfig['table'] . " j$intCount ON " . $arrOptions['table'] . "." . $strKey . "=j$intCount." . $arrConfig['field'];
					}
				}
			}

			// Generate the query
			$strQuery = "SELECT " . implode(', ', $arrFields) . " FROM " . $arrOptions['table'] . $strAdditionalSql . implode("", $arrJoins);
		}

		// Where condition
		if ($arrOptions['column'] !== null)
		{
			$strQuery .= " WHERE " . (is_array($arrOptions['column']) ? implode(" AND ", $arrOptions['column']) : $arrOptions['table'] . '.' . $arrOptions['column'] . "=?");
		}

		// Group by
		if ($arrOptions['group'] !== null)
		{
			$strQuery .= " GROUP BY " . $arrOptions['group'];
		}

		// Having (see #6446)
		if ($arrOptions['having'] !== null)
		{
			$strQuery .= " HAVING " . $arrOptions['having'];
		}

		// Order by
		if ($arrOptions['order'] !== null)
		{
			$strQuery .= " ORDER BY " . $arrOptions['order'];
		}

		return $strQuery;
	}


	/**
	 * Build a query based on the given options to count the number of records
	 *
	 * @param array $arrOptions The options array
	 *
	 * @return string The query string
	 */
	public static function count(array $arrOptions)
	{
		$strQuery = "SELECT COUNT(*) AS count FROM " . $arrOptions['table'];

		if ($arrOptions['additionalSql'])
			$strQuery .= ' ' . $arrOptions['additionalSql'];

		if ($arrOptions['column'] !== null)
		{
			$strQuery .= " WHERE " . (is_array($arrOptions['column']) ? implode(" AND ", $arrOptions['column']) : $arrOptions['table'] . '.' . $arrOptions['column'] . "=?");
		}

		if ($arrOptions['additionalGroupBy'])
			$strQuery .= ' GROUP BY ' . $arrOptions['additionalGroupBy'];

		return $strQuery;
	}
}
