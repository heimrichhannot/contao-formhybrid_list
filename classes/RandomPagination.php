<?php

namespace HeimrichHannot\FormHybridList;

use HeimrichHannot\Haste\Util\Url;

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
                $strUrl = Url::addQueryString(FormHybridList::PARAM_RANDOM . '=' . $this->intRandomSeed, $strUrl);
            }

            return $strUrl;
        }
        else
        {
            $strUrl = Url::addQueryString($this->strParameter . '=' . $intPage, $strUrl);

            if ($this->intRandomSeed)
            {
                $strUrl = Url::addQueryString(FormHybridList::PARAM_RANDOM . '=' . $this->intRandomSeed, $strUrl);
            }

            return $strUrl;
        }
    }
}
