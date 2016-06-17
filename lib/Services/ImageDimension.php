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

class ImageDimension extends Entity {
	protected $imageId;
	protected $imageHeight;
	protected $imageWidth;
}