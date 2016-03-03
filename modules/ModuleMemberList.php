<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package formhybrid_list
 * @author Dennis Patzer <d.patzer@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\FormHybridList;

use HeimrichHannot\Haste\Util\Files;

class ModuleMemberList extends ModuleList
{
	protected function runBeforeTemplateParsing($objTemplate, $arrItem)
	{
		if (!$this->useDummyImage)
			return;

		if ($arrItem['fields']['addImage'] && $arrItem['fields']['singleSRC'] != '')
		{
			$this->addImage($arrItem['raw'], 'singleSRC', $arrItem);
			if (is_file(TL_ROOT . '/' . $arrItem['fields']['singleSRC'])) {
				$this->addImageToTemplate($objTemplate, $arrItem['fields']);
			}
		}
		elseif ($this->useDummyImage && $this->dummyImage)
		{
			$arrItem['fields']['addImage'] = true;
			$arrItem['fields']['singleSRC'] = Files::getPathFromUuid($this->dummyImage);

			$this->addImage($arrItem['raw'], 'singleSRC', $arrItem);

			if (is_file(TL_ROOT . '/' . $arrItem['fields']['singleSRC'])) {
				$this->addImageToTemplate($objTemplate, $arrItem['fields']);
			}
		}
	}
}
