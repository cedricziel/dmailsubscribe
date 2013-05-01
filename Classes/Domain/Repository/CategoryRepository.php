<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Björn Fromme <fromme@dreipunktnull.come>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Category Repository
 *
 * @package Dmailsubscribe
 * @subpackage Domain\Repository
 */
class Tx_Dmailsubscribe_Domain_Repository_CategoryRepository extends Tx_Extbase_Persistence_Repository {

	/**
	 * Fetches all categories from provided storage pids
	 * or - if omitted - all available
	 *
	 * @param array $storagePageIds
	 * @return Tx_Extbase_Persistence_QueryResultInterface
	 */
	public function findAllInPids(array $storagePageIds = array()) {
		$query = $this->createQuery();

		if (0 === count($storagePageIds)) {
			$query->getQuerySettings()->setRespectStoragePage(FALSE);
		} else {
			$query->getQuerySettings()->setStoragePageIds($storagePageIds);
		}

		return $query->execute();
	}

	/**
	 * @param array $uids
	 * @param array $storagePageIds
	 * @return NULL|Tx_Extbase_Persistence_QueryResultInterface
	 */
	public function findAllByUids(array $uids, array $storagePageIds = array()) {
		$result = NULL;

		$query = $this->createQuery();

		if (0 === count($storagePageIds)) {
			$query->getQuerySettings()->setRespectStoragePage(FALSE);
		} else {
			$query->getQuerySettings()->setStoragePageIds($storagePageIds);
		}

		if (0 < count($uids)) {
			$query->matching($query->in('uid', $uids));
			$result = $query->execute();
		}

		return $result;
	}

}
