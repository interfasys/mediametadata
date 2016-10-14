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


use OCP\Files\Node;

class StoreMetadata {
	protected $imageDimensionMapper;

	/**
	 * @param ImageDimensionMapper $mapper
	 */
	public function __construct(ImageDimensionMapper $mapper) {
		$this->imageDimensionMapper = $mapper;
	}

	/**
	 * @param $metadata
	 * @param Node $node
	 * @return bool
	 */
	public function store($metadata, Node $node) {
		$imageDimension = new ImageDimension();

		//Image ID
		$imageDimension->setImageId($node->getId());
		//Image Height
		if(array_key_exists('imageHeight', $metadata)) {
			$imageDimension->setImageHeight($metadata['imageHeight']);
		}
		//Image Width
		if(array_key_exists('imageWidth', $metadata)) {
			$imageDimension->setImageWidth($metadata['imageWidth']);
		}

		//EXIF Data
		//Date Created
		if(array_key_exists('EXIFData', $metadata) && array_key_exists('dateCreated', $metadata['EXIFData'])) {
			$imageDimension->setDateCreated($metadata['EXIFData']['dateCreated']);
		}

		//GPS Latitude
		if(array_key_exists('EXIFData', $metadata) && array_key_exists('gpsLatitude', $metadata['EXIFData'])) {
			$imageDimension->setGpsLatitude($metadata['EXIFData']['gpsLatitude']);
		}

		//GPS Longitude
		if(array_key_exists('EXIFData', $metadata) && array_key_exists('gpsLongitude', $metadata['EXIFData'])) {
			$imageDimension->setGpsLongitude($metadata['EXIFData']['gpsLongitude']);
		}

		// IPTC Data
		// Date Created
		if(array_key_exists('IPTCData', $metadata) && array_key_exists('dateCreated', $metadata['IPTCData'])) {
			$imageDimension->setDateCreated($metadata['IPTCData']['dateCreated']);
		}

		//Insert to Database
		$entity = $this->imageDimensionMapper->insert($imageDimension);

		if($entity->getId() == null) {
			return false;
		}

		return true;
	}
}
