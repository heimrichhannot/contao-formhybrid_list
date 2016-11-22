<?php

namespace HeimrichHannot\FormHybridList;

class RandomPagination extends \Contao\Pagination
{
    protected $intRandomSeed = false;

    public function __construct(
        $intRandomSeed,
        $intRows,
        $intPerPage,
        $intNumberOfLinks = 7,
        $strParameter = 'page',
        \Template $objTemplate = null,
        $blnForceParam = false
    ) {
        $this->intRandomSeed = $intRandomSeed;

        parent::__construct($intRows, $intPerPage, $intNumberOfLinks, $strParameter, $objTemplate, $blnForceParam);
    }


    protected function linkToPage($intPage)
    {
        $strUrl = ampersand($this->strUrl);

        if ($intPage <= 1 && !$this->blnForceParam)
        {

            if ($this->intRandomSeed)
            {
                $strUrl .= $this->strVarConnector . FormHybridList::PARAM_RANDOM . '=' . $this->intRandomSeed;
            }

            return $strUrl;
        }
        else
        {
            $strUrl = $strUrl . $this->strVarConnector . $this->strParameter . '=' . $intPage;

            if ($this->intRandomSeed)
            {
                $strUrl .= '&' . FormHybridList::PARAM_RANDOM . '=' . $this->intRandomSeed;
            }

            return $strUrl;
        }
    }
}
