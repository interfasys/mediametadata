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
		 * EXIF Metadata Extraction
		 */
		$exifData = $this->extractEXIFMetadata($absoluteImagePath);

		$metadata['EXIFData'] = $exifData;

		return $metadata;
	}

	/**
	 * @param $absoluteImagePath
	 * @return array
	 */
	private function extractImageDimensions($absoluteImagePath) {
		$logger = \OC::$server->getLogger();
		$logger->debug('Image Path: {absolutePath}', array('app' => 'MediaMetadata', 'absolutePath' => $absoluteImagePath));

		try {
			$dimensions = getimagesize($absoluteImagePath);
		} catch (\Exception $e) {
			$dimensions = false;
		}

		if ($dimensions !== false) {
			list($image_width, $image_height) = $dimensions;

			$logger->debug('Image Height: {image_height}', array('app' => 'MediaMetadata', 'image_height' => $image_height));
			$logger->debug('Image Width: {image_width}', array('app' => 'MediaMetadata', 'image_width' => $image_width));
		}

		return $dimensions;
	}

	/**
	 * @param $absoluteImagePath
	 * @return array|null
	 */
	private function extractEXIFMetadata($absoluteImagePath) {
		try {
			$exif = exif_read_data($absoluteImagePath, 0, true);
		} catch (\Exception $e) {
			$exif = false;
		}

		$logger = \OC::$server->getLogger();

		$exifData = array();

		if($exif !== false) {
			$logger->debug('EXIF Info', array('app' => 'MediaMetadata'));

			foreach ($exif as $key => $section) {
				foreach ($section as $name => $val) {
					$logger->debug('{key}.{name}: {val}', array(
						'app'  => 'MediaMetadata',
						'key'  => $key,
						'name' => $name,
						'val'  => $val
					));
				}
			}

			// Date Created
			if((array_key_exists('EXIF', $exif)) && (array_key_exists('DateTimeOriginal', $exif['EXIF']))) {
				$date_created = $exif['EXIF']['DateTimeOriginal'];
				$exifData['dateCreated'] = $date_created;
			}

			// GPS Latitude
			if((array_key_exists('GPS', $exif)) && (array_key_exists('GPSLatitude', $exif['GPS']))) {
				$logger->debug('Keys of GPS Latitude: [0]- {degrees} [1]- {minutes} [2]- {seconds}', array(
					'app' 	  => 'Mediametadata',
					'degrees' => $exif['GPS']['GPSLatitude'][0],
					'minutes' => $exif['GPS']['GPSLatitude'][1],
					'seconds' => $exif['GPS']['GPSLatitude'][2]
				));

				$gpsLatitude = $this->getGPS($exif['GPS']['GPSLatitude'], $exif['GPS']['GPSLatitudeRef']);
				$exifData['gpsLatitude'] = $gpsLatitude;
			}

			// GPS Longitude
			if((array_key_exists('GPS', $exif)) && (array_key_exists('GPSLongitude', $exif['GPS']))) {
				$logger->debug('Keys of GPS Longitude: [0]- {degrees} [1]- {minutes} [2]- {seconds}', array(
					'app' 	  => 'Mediametadata',
					'degrees' => $exif['GPS']['GPSLongitude'][0],
					'minutes' => $exif['GPS']['GPSLongitude'][1],
					'seconds' => $exif['GPS']['GPSLongitude'][2]
				));

				$gpsLongitude = $this->getGPS($exif['GPS']['GPSLongitude'], $exif['GPS']['GPSLongitudeRef']);
				$exifData['gpsLongitude'] = $gpsLongitude;
			}
			return $exifData;
		}
		return null;
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

		$isFlip = ($Hemisphere == 'W' || $Hemisphere == 'S') ? -1 : 1;

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
