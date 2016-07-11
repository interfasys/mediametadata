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
use OCA\MediaMetadata\Services\ImageMetadata;
use OCA\MediaMetadata\Services\ImageMetadataMapper;
use OCP\Files\Node;

class ImageHooks {
	protected $root;
	protected $mapper;
	protected $dataDirectory;

	/**
	 * @param Root $root
	 * @param ImageMetadataMapper $mapper
	 * @param $dataDirectory
	 */
	public function __construct(Root $root, ImageMetadataMapper $mapper, $dataDirectory) {
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

		$dimensions = getimagesize($absolutePath);

		$logger = \OC::$server->getLogger();
		$logger->log('debug', 'Image Path: '.$absolutePath, array('app' => 'MediaMetadata'));

		$imageMetadata = new ImageMetadata();

		if ($dimensions !== false) {
			list($image_width, $image_height) = $dimensions;

			$logger->log('debug', 'Image Height: '.$image_height, array('app' => 'MediaMetadata'));
			$logger->log('debug', 'Image Width: '.$image_width, array('app' => 'MediaMetadata'));

			$imageMetadata->setImageId($node->getId());
			$imageMetadata->setImageHeight($image_height);
			$imageMetadata->setImageWidth($image_width);
		}

		$exif = exif_read_data($absolutePath, 0, true);

		if($exif !== false) {
			$logger->info('EXIF Info', array('app' => 'MediaMetadata'));

			foreach ($exif as $key => $section) {
				foreach ($section as $name => $val) {
					$logger->info($key . '.' . $name . ': ' . $val, array('app' => 'MediaMetadata'));
				}
			}

			// Date Created
			if((array_key_exists('EXIF', $exif)) AND (array_key_exists('DateTimeOriginal', $exif['EXIF']))) {
				$date_created = $exif['EXIF']['DateTimeOriginal'];
				$imageMetadata->setDateCreated($date_created);
			}

			// GPS Latitude
			if((array_key_exists('GPS', $exif)) AND (array_key_exists('GPSLatitude', $exif['GPS']))) {
				$logger->notice('Keys of GPS Latitude: [0]- '.$exif['GPS']['GPSLatitude'][0].' [1]- '.$exif['GPS']['GPSLatitude'][1].' [2]- '.$exif['GPS']['GPSLatitude'][2],
					array('app' => 'Mediametadata'));

				$gpsLatitude = $this->getGPS($exif['GPS']['GPSLatitude'], $exif['GPS']['GPSLatitudeRef']);
				$imageMetadata->setGpsLatitude($gpsLatitude);
			}

			// GPS Longitude
			if((array_key_exists('GPS', $exif)) AND (array_key_exists('GPSLongitude', $exif['GPS']))) {
				$logger->notice('Keys of GPS Longitude: [0]- '.$exif['GPS']['GPSLongitude'][0].' [1]- '.$exif['GPS']['GPSLongitude'][1].' [2]- '.$exif['GPS']['GPSLongitude'][2],
					array('app' => 'Mediametadata'));

				$gpsLongitude = $this->getGPS($exif['GPS']['GPSLongitude'], $exif['GPS']['GPSLongitudeRef']);
				$imageMetadata->setGpsLongitude($gpsLongitude);
			}
		}

		if($exif !== false or $dimensions !== false) {
			$this->mapper->insert($imageMetadata);
		}
	}

	/**
	 * @param $exifGPSData
	 * @param $Hemisphere
	 * @return int
	 */
	private function getGPS($exifGPSData, $Hemisphere) {
		$degrees = count($exifGPSData) > 0 ? $this->GPStoNUM($exifGPSData[0]) : 0;
		$minutes = count($exifGPSData) > 1 ? $this->GPStoNUM($exifGPSData[1]) : 0;
		$seconds = count($exifGPSData) > 2 ? $this->GPStoNUM($exifGPSData[2]) : 0;

		$isFlip = ($Hemisphere == 'W' or $Hemisphere == 'S') ? -1 : 1;

		return $isFlip * ($degrees + $minutes / 60 + $seconds / 3600);
	}

	/**
	 * @param $Coordinate
	 * @return float|int
	 */
	private function GPStoNUM($Coordinate) {
		$parts = explode('/', $Coordinate);

		if (count($parts) <= 0)
			return 0;

		if (count($parts) == 1)
			return $parts[0];

		return floatval($parts[0]) / floatval($parts[1]);
	}
}