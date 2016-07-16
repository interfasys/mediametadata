<?php
/**
 * Created by PhpStorm.
 * User: imjalpreet
 * Date: 16/7/16
 * Time: 2:05 AM
 */

namespace OCA\MediaMetadata\Services;


class ExtractMetadata {
	protected $absoluteImagePath;

	/**
	 * @param $imagePath
	 */
	public function __construct($imagePath) {
		$this->absoluteImagePath = $imagePath;
	}

	/**
	 * @return array $metadata
	 */
	public function extract() {
		$dimensions = $this->extractImageDimensions();

		$metadata = array();

		if ($dimensions !== false) {
			list($image_width, $image_height) = $dimensions;

			$metadata['imageWidth'] = $image_width;
			$metadata['imageHeight'] = $image_height;
		}

		return $metadata;
	}

	/**
	 * @return array $dimensions
	 */
	private function extractImageDimensions() {
		$dimensions = getimagesize($this->absoluteImagePath);

		$logger = \OC::$server->getLogger();
		$logger->log('debug', 'Image Path: {absolutePath}', array('app' => 'MediaMetadata', 'absolutePath' => $this->absoluteImagePath));

		if ($dimensions !== false) {
			list($image_width, $image_height) = $dimensions;

			$logger->log('debug', 'Image Height: {image_height}', array('app' => 'MediaMetadata', 'image_height' => $image_height));
			$logger->log('debug', 'Image Width: {image_width}', array('app' => 'MediaMetadata', 'image_width' => $image_width));
		}

		return $dimensions;
	}
}