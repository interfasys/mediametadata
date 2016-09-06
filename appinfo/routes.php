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

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\MediaMetadata\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
		/**
		 * Services
		 */
		//Retrieve Metadata of a list of files
		[
			'name' => 'metadata#get_metadata',
			'url'  => '/metadata',
			'verb' => 'GET'
		],
		/**
		 * API
		 */
		[
			'name'         => 'metadata_api#preflighted_cors', // Valid for all API end points
			'url'          => '/api/{path}',
			'verb'         => 'OPTIONS',
			'requirements' => ['path' => '.+']
		],
		[
			'name' => 'metadata_api#get_metadata',
			'url'  => '/api/metadata',
			'verb' => 'GET'
		],
    ]
];
