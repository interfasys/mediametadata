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
		$logger->debug('Image Path: {absolutePath}', array('app' => 'MediaMetadata', 'absolutePath' => $absoluteImagePath));

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
		$exif = exif_read_data($absoluteImagePath, 0, true);

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
