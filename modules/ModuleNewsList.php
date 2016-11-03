<?php

namespace HeimrichHannot\FormHybridList;

use HeimrichHannot\FormHybrid\DC_Hybrid;
use HeimrichHannot\FormHybridList\ModuleList;
use HeimrichHannot\FormHybridList\ModuleMemberList;
use HeimrichHannot\Haste\Util\Arrays;
use HeimrichHannot\Haste\Util\Files;
use HeimrichHannot\MemberContentArchives\MemberContentArchiveModel;

class ModuleNewsList extends ModuleList
{
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### FORMHYBRID NEWS LIST ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}

	protected function generateFields($objItem)
	{
		$arrItem = parent::generateFields($objItem);

		global $objPage;

		$arrItem['fields']['newsHeadline'] = $objItem->headline;
		$arrItem['fields']['subHeadline'] = $objItem->subheadline;
		$arrItem['fields']['hasSubHeadline'] = $objItem->subheadline ? true : false;
		$arrItem['fields']['linkHeadline'] = ModuleNews::generateLink($this, $objItem->headline, $objItem, false);
		$arrItem['fields']['more'] = ModuleNews::generateLink($this, $GLOBALS['TL_LANG']['MSC']['more'], $objItem, false, true);
		$arrItem['fields']['link'] = ModuleNews::generateNewsUrl($this, $objItem, false);
		$arrItem['fields']['text'] = '';

		// Clean the RTE output
		if ($objItem->teaser != '')
		{
			if ($objPage->outputFormat == 'xhtml')
			{
				$arrItem['fields']['teaser'] = \StringUtil::toXhtml($objItem->teaser);
			}
			else
			{
				$arrItem['fields']['teaser'] = \StringUtil::toHtml5($objItem->teaser);
			}

			$arrItem['fields']['teaser'] = \StringUtil::encodeEmail($arrItem['fields']['teaser']);
		}

		// Display the "read more" button for external/article links
		if ($objItem->source != 'default')
		{
			$arrItem['fields']['text'] = true;
		}

		// Compile the news text
		else
		{
			$objElement = \ContentModel::findPublishedByPidAndTable($objItem->id, 'tl_news');

			if ($objElement !== null)
			{
				while ($objElement->next())
				{
					$arrItem['fields']['text'] .= $this->getContentElement($objElement->current());
				}
			}
		}

		$arrMeta = ModuleNews::getMetaFields($this, $objItem);

		// Add the meta information
		$arrItem['fields']['date'] = $arrMeta['date'];
		$arrItem['fields']['hasMetaFields'] = !empty($arrMeta);
		$arrItem['fields']['numberOfComments'] = $arrMeta['ccount'];
		$arrItem['fields']['commentCount'] = $arrMeta['comments'];
		$arrItem['fields']['timestamp'] = $objItem->date;
		$arrItem['fields']['author'] = $arrMeta['author'];
		$arrItem['fields']['datetime'] = date('Y-m-d\TH:i:sP', $objItem->date);

		$arrItem['fields']['addImage'] = false;

		// Add an image
		$this->addImage($objItem, 'singleSRC', $arrItem);

		// enclosures are added in runBeforeTemplateParsing

		return $arrItem;
	}

	protected function runBeforeTemplateParsing($objTemplate, $arrItem)
	{
		if ($arrItem['fields']['addImage'] && $arrItem['fields']['singleSRC'] != '')
		{
			if (is_file(TL_ROOT . '/' . $arrItem['fields']['singleSRC'])) {
				$this->addImageToTemplate($objTemplate, $arrItem['fields']);
			}
		}
		elseif ($this->useDummyImage && $this->dummyImage)
		{
			$arrItem['fields']['addImage'] = true;
			$arrItem['fields']['singleSRC'] = Files::getPathFromUuid($this->dummyImage);

			if (is_file(TL_ROOT . '/' . $arrItem['fields']['singleSRC'])) {
				$this->addImageToTemplate($objTemplate, $arrItem['fields']);
			}
		}

		if ($arrItem['fields']['addEnclosure'])
		{
			if (!is_array($arrItem['fields']['enclosure']))
				$arrItem['fields']['enclosure'] = array($arrItem['fields']['enclosure']);
			$this->addEnclosuresToTemplate($objTemplate, $arrItem['fields']);
		}
	}
}
