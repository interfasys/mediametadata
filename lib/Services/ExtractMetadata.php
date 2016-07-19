<?php
/**
 * Created by PhpStorm.
 * User: imjalpreet
 * Date: 16/7/16
 * Time: 2:05 AM
 */

namespace OCA\MediaMetadata\Services;


class ExtractMetadata {

	public function __construct() {}

	/**
	 * @param $absoluteImagePath
	 * @return array
	 */
	public function extract($absoluteImagePath) {
		$dimensions = $this->extractImageDimensions($absoluteImagePath);

		$metadata = array();

		if($dimensions !== false) {
			list($image_width, $image_height) = $dimensions;

			$metadata['imageWidth'] = $image_width;
			$metadata['imageHeight'] = $image_height;
		}

		return $metadata;
	}

	/**
	 * @param $absoluteImagePath
	 * @return array
	 */
	private function extractImageDimensions($absoluteImagePath) {
		$dimensions = getimagesize($absoluteImagePath);

		$logger = \OC::$server->getLogger();
		$logger->log('debug', 'Image Path: {absolutePath}', array('app' => 'MediaMetadata', 'absolutePath' => $absoluteImagePath));

		if ($dimensions !== false) {
			list($image_width, $image_height) = $dimensions;

			$logger->log('debug', 'Image Height: {image_height}', array('app' => 'MediaMetadata', 'image_height' => $image_height));
			$logger->log('debug', 'Image Width: {image_width}', array('app' => 'MediaMetadata', 'image_width' => $image_width));
		}

		return $dimensions;
	}
}
