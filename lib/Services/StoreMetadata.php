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
		//Image Height
		if(array_key_exists('imageHeight', $metadata)) {
			$imageDimension->setImageHeight($metadata['imageHeight']);
		}
		//Image Width
		if(array_key_exists('imageWidth', $metadata)) {
			$imageDimension->setImageWidth($metadata['imageWidth']);
		}

		// IPTC Data
		// Date Created
		if(array_key_exists('IPTCData', $metadata) && array_key_exists('dateCreated', $metadata['IPTCData'])) {
			$imageDimension->setDateCreated($metadata['IPTCData']['dateCreated']);
		}

		//Insert to Database
		$entity = $this->imageDimensionMapper->insert($imageDimension);

		if($imageDimension->getId() == null) {
			return false;
		}

		return true;
	}
}
