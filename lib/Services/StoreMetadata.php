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
	protected $node;
	protected $imageDimensionMapper;

	/**
	 * @param Node $node
	 */
	public function __construct(Node $node, ImageDimensionMapper $mapper) {
		$this->node = $node;
		$this->imageDimensionMapper = $mapper;
	}

	public function store($metadata) {
		$imageDimension = new ImageDimension();

		//Image ID
		$imageDimension->setImageId($this->node->getId());
		//Image Height
		if(array_key_exists('imageHeight', $metadata)) {
			$imageDimension->setImageHeight($metadata['imageHeight']);
		}
		//Image Width
		if(array_key_exists('imageWidth', $metadata)) {
			$imageDimension->setImageWidth($metadata['imageWidth']);
		}

		//Insert to Database
		$entity = $this->imageDimensionMapper->insert($imageDimension);
	}
}