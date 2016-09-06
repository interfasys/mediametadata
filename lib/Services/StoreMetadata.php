<?php
/**
 * Created by PhpStorm.
 * User: imjalpreet
 * Date: 17/7/16
 * Time: 1:07 AM
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
		if (is_array($metadata)) {
			//Image Height
			if (array_key_exists('imageHeight', $metadata)) {
				$imageDimension->setImageHeight($metadata['imageHeight']);
			}
			//Image Width
			if (array_key_exists('imageWidth', $metadata)) {
				$imageDimension->setImageWidth($metadata['imageWidth']);
			}

			if (array_key_exists('EXIFData', $metadata) && is_array($metadata['EXIFData'])) {
				//EXIF Data
				//Date Created
				if (array_key_exists('EXIFData', $metadata) && array_key_exists('dateCreated', $metadata['EXIFData'])) {
					$imageDimension->setDateCreated($metadata['EXIFData']['dateCreated']);
				}

				//GPS Latitude
				if (array_key_exists('EXIFData', $metadata) && array_key_exists('gpsLatitude', $metadata['EXIFData'])) {
					$imageDimension->setGpsLatitude($metadata['EXIFData']['gpsLatitude']);
				}

				//GPS Longitude
				if (array_key_exists('EXIFData', $metadata) && array_key_exists('gpsLongitude', $metadata['EXIFData'])) {
					$imageDimension->setGpsLongitude($metadata['EXIFData']['gpsLongitude']);
				}
			}
		}
		//Insert to Database
		$entity = $this->imageDimensionMapper->insert($imageDimension);

		if($entity->getId() == null) {
			return false;
		}

		return true;
	}
}
