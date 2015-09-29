<?php
namespace HeimrichHannot\FormHybrid;

class DC_Hybrid extends \DataContainer
{
	public function __construct($strTable, $objItem, $objModule=null)
	{
		$this->objActiveRecord = $objItem;
		$this->intId = $objItem->id;
		$this->strTable = $strTable;
		$this->objModule = $objModule;
	}
}
