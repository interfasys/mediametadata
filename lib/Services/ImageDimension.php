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

use OCP\AppFramework\Db\Entity;
use JsonSerializable;

class ImageDimension extends Entity implements JsonSerializable {
	protected $imageId;
	protected $imageHeight;
	protected $imageWidth;

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		return [
			'id' => $this->id,
			'imageId' => $this->imageId,
			'imageHeight' => $this->imageHeight,
			'imageWidth' => $this->imageWidth
		];
	}
}