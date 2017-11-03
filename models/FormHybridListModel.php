<?php

namespace HeimrichHannot\FormHybridList;

abstract class FormHybridListModel extends \Model
{
    protected static $strTable;
    protected static $strAdditionalSql;
    protected static $strAdditionalWhereSql;
    protected static $strAdditionalSelectSql;
    protected static $strAdditionalHavingSql;
    protected static $strAdditionalGroupBy;

    public static function findBy($strColumn, $varValue, array $arrOptions = [])
    {
        $arrOptions = array_merge
        (
            [
                'column' => $strColumn,
                'value'  => $varValue,
                'return' => 'Collection'
            ],

            $arrOptions
        );

        return static::find($arrOptions);
    }

    public static function findAll(array $arrOptions = [])
    {
        $arrOptions = array_merge
        (
            [
                'return' => 'Collection'
            ],

            $arrOptions
        );

        return static::find($arrOptions);
    }

    protected static function find(array $arrOptions)
    {
        if (static::$strTable == '') {
            return null;
        }

        $arrOptions['table']               = static::$strTable;
        $arrOptions['additionalWhereSql']  = static::$strAdditionalWhereSql;
        $arrOptions['additionalSelectSql'] = static::$strAdditionalSelectSql;
        $arrOptions['additionalSql']       = static::$strAdditionalSql;

        if ($arrOptions['additionalSql']) {
            $arrOptions['group'] = static::$strAdditionalGroupBy;
        }

        if (static::$strAdditionalHavingSql) {
            $arrOptions['having'] = static::$strAdditionalHavingSql;
        }

        $strQuery = static::buildFindQuery($arrOptions);

        $objStatement = \Database::getInstance()->prepare($strQuery);

        // Defaults for limit and offset
        if (!isset($arrOptions['limit'])) {
            $arrOptions['limit'] = 0;
        }
        if (!isset($arrOptions['offset'])) {
            $arrOptions['offset'] = 0;
        }

        // Limit
        if ($arrOptions['limit'] > 0 || $arrOptions['offset'] > 0) {
            $objStatement->limit($arrOptions['limit'], $arrOptions['offset']);
        }

        $objStatement = static::preFind($objStatement);
        $objResult    = $objStatement->execute($arrOptions['value']);

        if ($objResult->numRows < 1) {
            return null;
        }

        $objResult = static::postFind($objResult);

        if ($arrOptions['return'] == 'Model') {
            $strPk = static::$strPk;
            $intPk = $objResult->{$strPk};

            // Try to load from the registry
            $objModel = \Model\Registry::getInstance()->fetch(static::$strTable, $intPk);

            if ($objModel !== null) {
                return $objModel->mergeRow($objResult->row());
            }

            return static::createModelFromDbResult($objResult);
        } else {
            return static::createCollectionFromDbResult($objResult, static::$strTable);
        }
    }


    public static function countBy($strColumn = null, $varValue = null, array $arrOptions = [])
    {
        if (static::$strTable == '') {
            return 0;
        }

        $strQuery = static::buildCountQuery(
            [
                'table'             => static::$strTable,
                'column'            => $strColumn,
                'value'             => $varValue,
                'additionalSql'     => static::$strAdditionalSql,
                'additionalGroupBy' => static::$strAdditionalGroupBy
            ]
        );

        return (int)\Database::getInstance()->prepare($strQuery)->execute($varValue)->count;
    }

    protected static function buildFindQuery(array $arrOptions)
    {
        return FormHybridListQueryBuilder::find($arrOptions);
    }

    protected static function buildCountQuery(array $arrOptions)
    {
        return FormHybridListQueryBuilder::count($arrOptions);
    }

    public static function setTable($strTable)
    {
        static::$strTable = $strTable;
    }

    public static function setAdditionalSql($strAdditionalSql)
    {
        static::$strAdditionalSql = html_entity_decode($strAdditionalSql);
    }

    public static function setAdditionalGroupBy($strAdditionalGroupBy)
    {
        static::$strAdditionalGroupBy = html_entity_decode($strAdditionalGroupBy);
    }

    public static function removeAdditionalSql()
    {
        static::$strAdditionalSql = '';
    }

    public static function removeAdditionalGroupBy()
    {
        static::$strAdditionalGroupBy = '';
    }

    public static function setAdditionalWhereSql($strAdditionalWhereSql)
    {
        static::$strAdditionalWhereSql = html_entity_decode($strAdditionalWhereSql);
    }

    public static function setAdditionalHavingSql($strAdditionalHavingSql)
    {
        static::$strAdditionalHavingSql = html_entity_decode($strAdditionalHavingSql);
    }

    public static function setAdditionalSelectSql($strAdditionalSelectSql)
    {
        static::$strAdditionalSelectSql = html_entity_decode($strAdditionalSelectSql);
    }

    public static function removeAdditionalSelectSql()
    {
        static::$strAdditionalSelectSql = '';
    }

}
