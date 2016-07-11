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

class ImageMetadata extends Entity implements JsonSerializable {
	protected $imageId;
	protected $imageHeight;
	protected $imageWidth;
	protected $dateCreated;
	protected $gpsLatitude;
	protected $gpsLongitude;

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		return [
			'id' => $this->id,
			'imageId'      => $this->imageId,
			'imageHeight'  => $this->imageHeight,
			'imageWidth'   => $this->imageWidth,
			'dateCreated'  => $this->dateCreated,
			'gpsLatitude'  => $this->gpsLatitude,
			'gpsLongitude' => $this->gpsLongitude
		];
	}
}