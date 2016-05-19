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

namespace OCA\MediaMetadata\Hooks;


use OC\Files\Node\Root;
use OCA\MediaMetadata\Services\ImageDimension;
use OCA\MediaMetadata\Services\ImageDimensionMapper;
use OCP\Files\Node;

class ImageHooks {
	protected $root;
	protected $mapper;
	protected $dataDirectory;

	/**
	 * @param Root $root
	 * @param ImageDimensionMapper $mapper
	 * @param $datadirectory
	 */
	public function __construct(Root $root, ImageDimensionMapper $mapper, $dataDirectory) {
		$this->root = $root;
		$this->mapper = $mapper;
		$this->dataDirectory = $dataDirectory;
	}

	public function register() {
		$reference = $this;

		$callback = function (Node $node) use($reference) {
			$reference->post_create($node);
		};

		$this->root->listen('\OC\Files', 'postCreate', $callback);
	}

	/**
	 * @param Node $node
	 */
	public function post_create(Node $node) {
		$absolutePath = $this->dataDirectory.$node->getPath();

		$dimensions = getimagesize($absolutePath);

		$logger = \OC::$server->getLogger();
		$logger->log('debug', 'Image Path: '.$absolutePath, array('app' => 'MediaMetadata'));

		if ($dimensions !== false) {
			list($image_width, $image_height) = $dimensions;

			$logger->log('debug', 'Image Height: '.$image_height, array('app' => 'MediaMetadata'));
			$logger->log('debug', 'Image Width: '.$image_width, array('app' => 'MediaMetadata'));

			$imageDimension = new ImageDimension();
			$imageDimension->setImageId($node->getId());
			$imageDimension->setImageHeight($image_height);
			$imageDimension->setImageWidth($image_width);

			$this->mapper->insert($imageDimension);
		}
	}
}