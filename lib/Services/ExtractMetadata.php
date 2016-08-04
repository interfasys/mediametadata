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

		/**
		 * IPTC Metadata Extraction
		 */
		$iptcData = $this->extractIPTCData($absoluteImagePath);

		$metadata['IPTCData'] = $iptcData;

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

	/**
	 * @param $absoluteImagePath
	 * @return array
	 */
	private function extractIPTCData($absoluteImagePath) {
		$dimensions = getimagesize($absoluteImagePath, $info);

		$this->output_iptc_data($absoluteImagePath);

		$logger = \OC::$server->getLogger();

		$iptcData = array();

		/**
		 * IPTC Metadata Extraction
		 */
		if(is_array($info)) {
			if (isset($info["APP13"])) {
				$iptc = iptcparse($info["APP13"]);
				if (is_array($iptc)) {
					if (array_key_exists('2#027', $iptc)) {
						$logger->info('Location extracted from IPTC: {location}',
							array(
								'app' => 'MediaMetadata',
								'location' => $iptc['2#027'][0]
							)
						);
						$iptcData['Location'] = $iptc['2#027'][0];
					}
					if (array_key_exists('2#055', $iptc)) {
						$logger->info('Date Created extracted from IPTC: {dateCreated}',
							array(
								'app' => 'MediaMetadata',
								'dateCreated' => $iptc['2#055'][0]
							)
						);
						$iptcData['dateCreated'] = $iptc['2#055'][0];
					}
					if (array_key_exists('2#090', $iptc)) {
						$logger->info('City extracted from IPTC: {city}',
							array(
								'app' => 'MediaMetadata',
								'city' => $iptc['2#090'][0]
							)
						);
						$iptcData['City'] = $iptc['2#090'][0];
					}
					if (array_key_exists('2#092', $iptc)) {
						$logger->info('Sub-Location extracted from IPTC: {sublocation}',
							array(
								'app' => 'MediaMetadata',
								'sublocation' => $iptc['2#092'][0]
							)
						);
						$iptcData['SubLocation'] = $iptc['2#092'][0];
					}
					if (array_key_exists('2#095', $iptc)) {
						$logger->info('State extracted from IPTC: {State}',
							array(
								'app' => 'MediaMetadata',
								'State' => $iptc['2#095'][0]
							)
						);
						$iptcData['State'] = $iptc['2#095'][0];
					}
					if (array_key_exists('2#101', $iptc)) {
						$logger->info('Country extracted from IPTC: {Country}',
							array(
								'app' => 'MediaMetadata',
								'Country' => $iptc['2#101'][0]
							)
						);
						$iptcData['Country'] = $iptc['2#101'][0];
					}
				}
			}
			return $iptcData;
		}
	}

	/**
	 * @param $image_path
	 */
	private function output_iptc_data( $image_path ) {
		getimagesize($image_path, $info);
		$logger = \OC::$server->getLogger();
		if(is_array($info)) {
			if(isset($info["APP13"])) {
				$iptc = iptcparse($info["APP13"]);
				if(is_array($iptc)) {
					foreach (array_keys($iptc) as $s) {
						$c = count($iptc[$s]);
						for ($i = 0; $i < $c; $i++) {
							$logger->warning($s . ' = ' . $iptc[$s][$i], array('app' => 'MediaMetadata'));
						}
					}
				}
			}
		}
	}
}
