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
use OCA\MediaMetadata\Services\ExtractMetadata;
use OCA\MediaMetadata\Services\ImageDimension;
use OCA\MediaMetadata\Services\ImageDimensionMapper;
use OCA\MediaMetadata\Services\StoreMetadata;
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
			$reference->postCreate($node);
		};

		$this->root->listen('\OC\Files', 'postCreate', $callback);
	}

	/**
	 * @param Node $node
	 */
	public function postCreate(Node $node) {
		$absolutePath = $this->dataDirectory.$node->getPath();

		$metadataExtracter = new ExtractMetadata($absolutePath);
		$metadata = $metadataExtracter->extract();

		$dbManager = new StoreMetadata($node, $this->mapper);
		$dbManager->store($metadata);
	}
}