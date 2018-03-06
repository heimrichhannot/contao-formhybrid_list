<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) Heimrich & Hannot GmbH
 *
 * @package formhybrid_list
 * @author  Dennis Patzer
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\FormHybridList;

use Contao\Session;
use HeimrichHannot\FormHybrid\Form;
use HeimrichHannot\Haste\Util\Url;
use HeimrichHannot\Request\Request;

class ListFilterForm extends Form
{
    protected $isFilterForm = true;
    protected $objListModule;

    public function __construct($objModule)
    {
        $this->strMethod                   = FORMHYBRID_METHOD_GET;
        $this->isFilterForm                = true;
        $this->objListModule               = $objModule;
        $objModule->formHybridTemplate     = $objModule->formHybridTemplate ?: 'formhybrid_list_filter';
        $objModule->formHybridEditable     = $objModule->customFilterFields;
        $objModule->formHybridCustomSubmit = true;
        $objModule->formHybridSubmitLabel  = 'filter';

        $arrHeadline                         = deserialize($objModule->filterHeadline);
        $objModule->filterHeadline           = is_array($arrHeadline) ? $arrHeadline['value'] : $arrHeadline;
        $objModule->filterHl                 = is_array($arrHeadline) ? $arrHeadline['unit'] : 'h1';
        $objModule->formHybridSkipValidation = true;
        
        // reset session if filter is reset
        if(Request::getGet('reset'))
        {
            $this->resetSession(session_id().'_filter_'.$objModule->id);
        }
        
        // update default values when filter is saved in session
        if($objModule->saveFilterToSession && null !== ($sessionFilter = \Session::getInstance()->get(session_id().'_filter_'.$objModule->id)))
        {
            $objModule->formHybridAddDefaultValues = true;
            $objModule->formHybridDefaultValues = $this->updateDefaultValues($objModule, $sessionFilter);
        }

        parent::__construct($objModule);
    }

    protected function onSubmitCallback(\DataContainer $dc)
    {
        $this->submission = $dc;
        
        if (is_array($this->arrSubmitCallbacks) && !empty($this->arrSubmitCallbacks)) {
            foreach ($this->arrSubmitCallbacks as $arrCallback) {
                if (is_array($arrCallback) && !empty($arrCallback)) {
                    $arrCallback[0]::$arrCallback[1]($dc);
                }
            }
        }
        
        if($this->objListModule->saveFilterToSession)
        {
            $this->saveFilterToSession();
        }
    }
    
    protected function saveFilterToSession()
    {
        $submission = $this->getSubmission(false)->row();
        $module = $this->objListModule;
        $sessionFilter = [];
        
        foreach(deserialize($module->customFilterFields,true) as $value)
        {
            $sessionFilter[$value] = $submission[$value];
        }
        
        \Session::getInstance()->set(session_id().'_filter_'.$module->id, $sessionFilter);
        
        \Controller::redirect(\Haste\Util\Url::removeQueryString(array_merge(['FORM_SUBMIT'], array_keys($this->arrFields))));
    }

    protected function compile()
    {
    }

    protected function generateResetFilterField()
    {
        $url = $this->objListModule->saveFilterToSession ? \Haste\Util\Url::addQueryString('reset=true', Url::getCurrentUrlWithoutParameters())  : Url::getCurrentUrlWithoutParameters();
        
        $arrData = [
            'inputType' => 'explanation',
            'eval'      => [
                'text' => '<div class="form-group reset-filter"><a class="btn btn-default btn-lg" href="' . $url . '"><span>'
                    . $GLOBALS['TL_LANG']['formhybrid_list'][FORMHYBRID_LIST_BUTTON_RESET_FILTER][0] . '</span></a></div>',
            ],
        ];

        $this->arrFields[FORMHYBRID_LIST_BUTTON_RESET_FILTER] = $this->generateField(FORMHYBRID_LIST_BUTTON_RESET_FILTER, $arrData);
    }

    protected function generateSubmitField()
    {
        $this->generateResetFilterField();

        $strLabel = &$GLOBALS['TL_LANG']['MSC']['formhybrid']['submitLabels']['default'];
        $strClass = 'btn btn-primary btn-lg';

        if ($this->strSubmit != '' && isset($this->arrFields[$this->strSubmit]))
        {
            return false;
        }

        if ($this->customSubmit)
        {
            if ($this->submitLabel != '')
            {
                $strLabel = $GLOBALS['TL_LANG']['MSC']['formhybrid']['submitLabels'][$this->submitLabel];
            }

            $strClass = $this->submitClass;
        }

        $arrData = [
            'inputType' => 'submit',
            'label'     => is_array($strLabel) ? $strLabel : [$strLabel],
            'eval'      => ['class' => $strClass],
        ];

        $this->arrFields[FORMHYBRID_NAME_SUBMIT] = $this->generateField(FORMHYBRID_NAME_SUBMIT, $arrData);
    }

    public function modifyDC(&$arrDca = null)
    {
        foreach ($arrDca['fields'] as $strField => $arrData) {
            switch ($arrData['inputType']) {
                case 'textarea':
                    $arrDca['fields'][$strField]['inputType'] = 'text';
                    break;
                case 'checkbox':
                    // Replace boolean checkbox value with "yes" and "no"
                    if (!$arrData['eval']['multiple'] && !$arrData['eval']['skipTransformToSelect']) {
                        $arrDca['fields'][$strField]['eval']['includeBlankOption'] = true;
                        $arrDca['fields'][$strField]['eval']['isBoolean']          =
                            true; // required to be set within Modulelist::applyFilters() cause checkbox is select there
                        $arrDca['fields'][$strField]['inputType']                  = 'select';
                        $arrDca['fields'][$strField]['options']                    = ['1', '0'];
                        $arrDca['fields'][$strField]['reference']                  = &$GLOBALS['TL_LANG']['formhybrid_list']['reference']['yes_no'];
                    }
                    break;
            }
        }

        $this->objListModule->modifyDC($arrDca);

        // HOOK: add custom logic
        if (isset($GLOBALS['TL_HOOKS']['modifyDCFilter']) && is_array($GLOBALS['TL_HOOKS']['modifyDCFilter'])) {
            foreach ($GLOBALS['TL_HOOKS']['modifyDCFilter'] as $callback) {
                $this->import($callback[0]);
                $this->{$callback[0]}->{$callback[1]}($arrDca);
            }
        }
    }
    
    /**
     * update default values according to filter from session
     *
     * @param $module
     * @param $session
     *
     * @return string
     */
    protected function updateDefaultValues($module, $session)
    {
        if(empty($customFields = deserialize($module->customFilterFields,true)))
        {
            return '';
        }
        
        $defaults =  [];
        
        foreach($customFields as $field)
        {
            if('' == $session[$field])
            {
                continue;
            }
         
            $defaults[] = [
                'field' => $field,
                'value' => $session[$field],
                'label' => ''
            ];
        }
        
        $filterValues = array_merge(deserialize($module->formHybridDefaultValues,true),$defaults);
        
        return serialize($filterValues);
    }
    
    public function resetSession($key)
    {
        Session::getInstance()->set($key,null);
    }
}
