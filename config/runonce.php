<?php

class FormHybridListRunOnce extends \Controller
{

	public function run()
	{
		$this->updateTo220();
	}


	/**
	 * Update to version 2.2.0
	 */
	private function updateTo220()
	{
		$objDatabase = \Database::getInstance();

		if (!$objDatabase->fieldExists('tableFields', 'tl_module')) {
			return;
		}

		$objDatabase->execute('UPDATE tl_module SET tableFields = formHybridEditable, isTableList = 1, hasHeader = 1 WHERE customTpl LIKE "mod_formhybrid_list_table%"');
	}
}


/**
 * Instantiate controller
 */
$objFormHybridListRunOnce = new FormHybridListRunOnce();
$objFormHybridListRunOnce->run();