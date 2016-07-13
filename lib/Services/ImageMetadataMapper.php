<?php
/**
 * ownCloud - mediametadata
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Jalpreet Singh Nanda (:imjalpreet) <jalpreetnanda@gmail.com>
 * @copyright Jalpreet Singh Nanda (:imjalpreet) 2016
 */

namespace OCA\MediaMetadata\Services;


use OCP\AppFramework\Db\Mapper;
use OCP\IDb;

class ImageMetadataMapper extends Mapper {
	/**
	 * @param IDb $database
	 */
	public function __construct(IDb $database) {
		parent::__construct($database, 'mediametadata', '\OCA\MediaMetadata\Services\ImageMetadata');
	}

	/**
	 * @param $imageID
	 * @return \OCP\AppFramework\Db\Entity
	 */
	public function find($imageID) {
		$sql = 'SELECT * FROM *PREFIX*mediametadata WHERE image_id = ?';
		return $this->findEntity($sql, [$imageID]);
	}
}