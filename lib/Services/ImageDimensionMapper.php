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

class ImageDimensionMapper extends Mapper {
	/**
	 * @param IDb $database
	 */
	public function __construct(IDb $database) {
		parent::__construct($database, 'mediametadata_image_size', '\OCA\MediaMetadata\Services\ImageDimension');
	}


	//TODO: This method is not being used right now
	/**
	 * @param $imageID
	 * @return \OCP\AppFramework\Db\Entity
	 */
	public function find($imageID) {
		$sql = 'SELECT * FROM *PREFIX*mediametadata_image_size WHERE image_id = ?';
		return $this->findEntity($sql, [$imageID]);
	}
}