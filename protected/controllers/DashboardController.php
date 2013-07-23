<?php

class DashboardController extends BaseController
{
    private $_queries = array();
    
    private $_user;
    
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array();
    }
    
    /**
     * (non-PHPdoc)
     * @see CController::init()
     */
    public function init()
    {
    	// parent init must be called first so that jquery is included first
    	parent::init();
		
    	// TODO: Get actual user details here
    	// get the user
    	$this->_user = new stdClass();
    	$this->_user->user_id = 1;
    	
    	// List accommodation query
    	$sql = Yii::app()->db->createCommand();
    	$sql->select('*')->from('accommodation');
    	$sql->where('accommodation_user_id = :accommodation_user_id');
    	$sql->order('accommodation_name');
    	$this->_queries['get_accommodation_listing'] = $sql;
    	
    	// Get js and css on page
    	$this->css['/js/vendors/jquery-ui-1.8.21.custom/css/ui-lightness/jquery-ui-1.8.21.custom.css'] = '/css/all.css';
    	$this->css['/css/dashboard.css'] = '/css/all.css';
    	$this->js['/js/vendors/jquery-ui-1.8.21.custom/js/jquery-ui-1.8.21.custom.min.js'] = '/js/all.js';
    	$this->js['/js/dashboard.js'] = '/js/all.js';
		// 	TODO: reinsert this
    	// $this->js['http://maps.google.com/maps/api/js?sensor=false&libraries=places'] = '/js/all.js';
    	/* FILE UPLOAD RESOURCES */
    	$this->css['/js/vendors/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css'] = '/css/all.css	';
    	$this->js['/js/vendors/plupload/js/plupload.js'] = '';
    	$this->js['/js/vendors/plupload/js/plupload.silverlight.js'] = '/js/all.js';
    	$this->js['/js/vendors/plupload/js/plupload.flash.js'] = '/js/all.js';
    	$this->js['/js/vendors/plupload/js/plupload.html4.js'] = '/js/all.js';
    	$this->js['/js/vendors/plupload/js/plupload.html5.js'] = '/js/all.js';
    	$this->js['/js/vendors/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js'] = '/js/all.js';
    	/* VALIDATOR RESOURCES */
    	$this->js['/js/vendors/jquery-validation-1.8.1/jquery.validate.js'] = '/js/all.js';
    	/* JSON RESOURCES */
    	$this->js['/js/vendors/json/json2.js'] = '/js/all.js';
		
    }

    /**
    * Gets the listings, bookings, enquiries etc of accommodation
    */
    public function actionGetAccomOverview()
    {
    	$accommodationModel = new Accommodation;

    	// get accommodation
        if ( isset($_GET['accommodation_id']) && empty($_GET['accommodation_id']) === false )
        {
            $accommodation_id = (int)$_GET['accommodation_id'];
            $accommodationModel = $accommodationModel->findByPk($accommodation_id);
		}

        $this->renderPartial(
        	'_accom_overview',
        	array(
				'accommodationModel' => $accommodationModel,
			)
		);
		
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        /**
         * TODO:
         * Retrieve the following information:
         * User info
         * accommodation that belongs to user (either multiple accommodation
         * booking queries that have been made.
         */

    	// get all accommodation beloging to this user
    	$accommodationModel = new Accommodation;
		$accommodation = 
			$this->_queries['get_accommodation_listing']->query(
				array('accommodation_user_id' => $this->_user->user_id)
			);

        $this->render('dashboard',
            array
            (
                'user_name' => 'Brian',
                'accommodation' => $accommodation,
                'accommodationModel' => $accommodationModel,
            )
        );
    }

    /**
     * Adds/edits accommodation name, description and type to db.
     *
     * @return json string
     */
    public function actionOpAddAccomDetails()
    {
        $result = array();

        // create new accom if it is being added
        if ( empty($_POST['Accommodation']['accommodation_id']) === true )
        {
            $accommodation = new Accommodation;
            $accommodation->attributes = $_POST['Accommodation'];
            $accommodation->accommodation_create_date = DATE;
            $accommodation->accommodation_user_id = $this->_user->user_id;

        }
        else // edit accommodation
        {
            $accom_id = (int)$_POST['Accommodation']['accommodation_id'];
            $accommodation = Accommodation::model()->findByPk($accom_id);
            $accommodation->attributes = $_POST['Accommodation'];
        }

        // try save accommodation
        if ( $accommodation->save() )
        {
            $result['status'] = 'success';
            $result['accommodation_id'] = $accommodation->accommodation_id;
        }
        else
        {
            $result['status'] = 'error';
            $result['errors'] = $accommodation->getErrors();
            $result['accommodation_id'] = null;
        }

        die(json_encode($result));

    }

    /**
     * Adds/edits location details of accommodation.
     *
     * @return json string
     */
    public function actionOpAddAccomLocationDetails()
    {
        $result = array();

        $accom_id = (int)$_POST['Accommodation']['accommodation_id'];
        $accommodation = Accommodation::model()->findByPk($accom_id);

        if ( $accommodation == null )
        {
            $result['status'] = 'error';
            $result['errors'] = array('accommodation not found');
            die(json_encode($result));
        }

        $location_types = array(
            'street_number',
            'sublocality',
            'route',
            'locality',
            'political',
            'administrative_area_level_1',
            'administrative_area_level_2',
            'administrative_area_level_3',
            'country',
            'postal_code',
        );
        $not_valid_locations = array(
            'street_number',
            'route',
            'postal_code',
        );
        $not_valid_locations = array_flip($not_valid_locations);
		
		// dont save if location has not changed
		$lat = (double)number_format($_POST['Accommodation']['accommodation_lat'], 6);
		$lon = (double)number_format($_POST['Accommodation']['accommodation_lng'], 6);
		if ( (double)$accommodation->accommodation_lat == $lat && (double)$accommodation->accommodation_lng == $lon )
		{
			$result['status'] = 'success';
            $result['message'] = 'Location saved';
			die(json_encode($result));
		}
		
		// delete existing location mappings as location has changed
		$connection = Yii::app()->db;
		$sql = $connection->createCommand();
		$delete_con = 'alm_accommodation_id = ' . $accommodation->accommodation_id ;
		$sql->delete('accommodation_location_mappings', $delete_con);

        // save accommodation specific details.
        $accommodation->accommodation_lat = $lat;
        $accommodation->accommodation_lng = $lon;
        $accommodation->accommodation_zoom =  $_POST['Accommodation']['accommodation_zoom'];

        $accommodation_location = '';
        $locations = array();
        $locations_to_save = array();
        $location_level_count = 0;
        // extract location data
        if ( empty($_POST['locations']) === false )
        {
            foreach ( $_POST['locations'] as $location )
            {
                $location = str_replace('\'', '"', $location);
                $location = json_decode($location, true);
                $locations[] = $location;
            }

            // build location
            foreach ( $locations as $location )
            {
                $save = true;
                $accommodation_location .= $location['long_name'] . ', ';

                // save locations only if they are of the allowed types
                foreach ( $location['types'] as $type )
                {
                    if ( isset($not_valid_locations[$type]) === true )
                    {
                        $save = false;
                    }
                }

                // check if location is of an accepted type
                if ( $save === true )
                {
                    $locations_to_save[$location_level_count] = array(
                        'long_name' => $location['long_name'],
                        'short_name' => $location['short_name'],
                        'types' => $location['types'],
                        'parent' => '',
                    );

                    $location_level_count++;
                }
            }

        }
		
		// build locations parent location strings
		foreach ( $locations_to_save as $level => $location_to_save )
		{
			$parent_location = array();

			$parent_level = ( isset($locations_to_save[($level + 1)]) === true ) ? ($level + 1) : $level;

			for($i = $parent_level; $i < $location_level_count; $i++ )
			{
				$parent_location[] = $locations_to_save[$i]['long_name'];
			}
			
			// remove duplicates $name == parent
			$parent_location = array_unique($parent_location);
			if ( $location_to_save['long_name'] == $parent_location[0] )
			{
				continue;
			}
			
			$parent_location = implode(', ', $parent_location);
			$locations_to_save[$level]['parent'] = $parent_location;

			$parent = $locations_to_save[$level]['parent'];
			$name = $locations_to_save[$level]['long_name'];

			// dont save locs if parent and name r the same
			if ( $parent === $name )
			{
				$parent = 'NULL';
			}

			// Dont save locations that already exist
			$sql = Yii::app()->db->createCommand();
			$sql->select('location_id, location_name, location_parent')->from('locations');
			$sql->where('location_name = :location_name AND location_parent = :location_parent',
				array(':location_name' => $name, 'location_parent' => $parent));
			$stmt = $sql->queryRow();
			if ( $stmt === false )
			{
				$location = new Locations;
				$location->location_name = $name;
				$location->location_parent = $parent;
				$location->location_short_name = $locations_to_save[$level]['short_name'];
				$location->save();
			}

			// Dont save associations that already exist
			$location_id = ( $stmt === false ) ? $location->location_id : $stmt['location_id'];
			$sql = Yii::app()->db->createCommand();
			$sql->select('alm_accommodation_id, alm_location_id')->from('accommodation_location_mappings');
			$sql->where('alm_accommodation_id = :alm_accommodation_id AND alm_location_id = :alm_location_id',
				array(':alm_accommodation_id' => $accom_id, 'alm_location_id' => $location_id));
			$stmt = $sql->queryRow();
			if ( $stmt === false )
			{
				$loc_mapping = new AccommodationLocationMappings();
				$loc_mapping->alm_accommodation_id = $accom_id;
				$loc_mapping->alm_location_id = $location_id;
				$loc_mapping->save();
			}
		}

        $accommodation->accommodation_location = rtrim($accommodation_location, ', ');
        if ( $accommodation->save() !== false )
        {
            $result['status'] = 'success';
            $result['message'] = 'Location saved';
        }
        else
        {
            $result['status'] = 'error';
            $result['errors'] = $accommodation->getErrors();
            $result['accommodation_id'] = null;
        }

        die(json_encode($result));

    }

    /**
     * Saves accommodation features.
     *
     * @return json string
     */
    public function actionOpAddAccommFeatures()
    {
        $result = array();

        $accom_id = (int)$_POST['Accommodation']['accommodation_id'];
        $accommodation = Accommodation::model()->findByPk($accom_id);

        if ( $accommodation == null )
        {
            $result['status'] = 'error';
            $result['errors'] = array('accommodation not found');
        }

        if ( empty($_POST['features']) === false )
        {
            // delete current features
            $connection = Yii::app()->db;
            $sql = $connection->createCommand();
            $delete_con = 'afm_accommodation_id = ' . $accom_id;
            $sql->delete('accommodation_features_mappings', $delete_con);

            foreach ( $_POST['features'] as $feature_id => $val )
            {
                $feature_id = (int)$feature_id;
                $connection = Yii::app()->db;
                $sql = $connection->createCommand();

                $params = array(
                    'afm_accommodation_id' => $accom_id,
                    'afm_feature_id' => $feature_id,
                );

                $sql->insert('accommodation_features_mappings', $params);
            }
        }

        $result['status'] = 'success';
        $result['message'] = 'Successfully saved features';

        die(json_encode($result));
    }

    /**
     * Adds images.
     * Returns a list of error codes for the uploader:
     * error codes
     * 100 - Only images with the following extensions: jpg,png,gif are allowed to be uploaded.
     * 101 - No more than 6 images can be uploaded.
     * 102 - Image is too small to be used on this site.
     * 103 - System error occurred
     *
     * @return json string
     */
    public function actionOpAddImages()
    {
        $accom_id = (int)$_POST['accommodation_id'];

    	// only allow images to be uploaded
    	$allowed_extensions = array(
    	   'jpg',
    	   'jpeg',
    	   'png',
    	   'gif',
    	);
    	$file_name = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

    	// Clean the fileName for security reasons
        $file_name = preg_replace('/[^\w\._]+/', '', $file_name);

    	$pos = strrpos($file_name, '.') + 1;
    	$ext = strtolower(substr($file_name, $pos));
        if ( in_array($ext, $allowed_extensions) === false )
        {
        	$return = array();
            $return['status'] = 'error';
            $return['message'] = 'Only images with the following extensions: '. implode(', ', $allowed_extensions) .' are allowed to be uploaded.';
            $return['error_code'] = '100';
        	die(json_encode($return));
        }

        // dont allow user to upload more images than allowed
        $connection = Yii::app()->db;
        $sql = $connection->createCommand();
        $sql->select('COUNT(ai_id) as images')->from('accommodation_images');
        $sql->where('ai_accommodation_id = ' . (int)$_REQUEST['accommodation_id']);
        $stmt = $sql->queryRow();
        $image_count = (int)$stmt['images'];
        if( $image_count >= 6 )
        {
        	$return['status'] = 'error';
	        $return['message'] = 'No more than 6 images can be uploaded.';
	        $return['error_code'] = '101';
	        die(json_encode($return));
        }

    	// 5 minutes execution time
        @set_time_limit(5 * 60);

        // Settings
        $targetDir = PATH .'/listing_images/';
        $privateTargetDir = PATH .'/original_images/';

        // Get parameters
        $chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
        $chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;

        // upload file or chunk of file
        $this->_uploadFileChunk($targetDir, $file_name, $chunk, $chunks);

		// Test if the file has been fully uploaded or not
		$image_path = $targetDir . DIRECTORY_SEPARATOR . $file_name;
		$current_file_size = filesize($image_path);
		$total_file_size = (int)$_REQUEST['total_file_size'];
		if ( $current_file_size === $total_file_size || $_REQUEST['runtime'] == 'html4' ) // html4 can only upload a whole file
		{
	        $image = Yii::app()->image->load($image_path);
	        // check if image meets the minimium size requirements
            if ( $image->width < 400 || $image->height < 400 )
            {
            	unlink($image_path);
                $return['status'] = 'error';
                $return['message'] = ' is too small to be used on this site.';
                $return['error_code'] = '102';
                die(json_encode($return));
            }

            // make a copy of the original file
            $orig_name = 'o_' . $file_name;
            $orig_path = $privateTargetDir . DIRECTORY_SEPARATOR . $orig_name;
            $copied = copy($image_path, $orig_path);

            /**
             * RESIZE IMAGE LOGIC:
             */
            // Target dimensions
			$max_width = 400;
			$max_height = 400;
			// Get current dimensions
			$old_width  = $image->width;
			$old_height = $image->height;
			// Calculate the scaling we need to do to fit the image inside our frame
            $scale = min($max_width/$old_width, $max_height/$old_height);
            // Get the new dimensions
			$new_width  = ceil($scale*$old_width);
			$new_height = ceil($scale*$old_height);

            $image->resize($new_width, $new_height)->quality(100);
	        $image->save();
	        // reload image to get resized dimensions
	        $image = Yii::app()->image->load($image_path);

	        // create thumbnail
	        $thumbnail_name = 't_' . $file_name;
	        $thumb_path = $targetDir . DIRECTORY_SEPARATOR . $thumbnail_name;
	        $copied = copy($image_path, $thumb_path);

	        // resize thumb here
	        // Target dimensions
            $thumb_max_width = 150;
            $thumb_max_height = 150;
            // Get current dimensions
			$old_width  = $image->width;
            $old_height = $image->height;
            $scale = min($thumb_max_width/$old_width, $thumb_max_height/$old_height);
			// Get the new dimensions
			$thumb_new_width  = ceil($scale*$old_width);
			$thumb_new_height = ceil($scale*$old_height);

            $image_thumb = Yii::app()->image->load($thumb_path);
            $image_thumb->resize($thumb_new_width, $thumb_new_height)->quality(100);
            $image_thumb->save();
            // reload image to get resized dimensions
            $image_thumb = Yii::app()->image->load($thumb_path);

	        // reference image into DB.
	        $AccommodationImage = new AccommodationImages;
	        $AccommodationImage->ai_accommodation_id = $accom_id;
	        $AccommodationImage->ai_name = $file_name;
	        $AccommodationImage->ai_width = $image->width;
	        $AccommodationImage->ai_height = $image->height;
	        $AccommodationImage->ai_thumb_name = $thumbnail_name;
	        $AccommodationImage->ai_thumb_width = $image_thumb->width;
	        $AccommodationImage->ai_thumb_height = $image_thumb->height;
	        $AccommodationImage->ai_original_name = $orig_name;
	        $AccommodationImage->save();

	        // return file info.
			$return = array();
			$return['status'] = 'success';
            $return['file_name'] = $file_name;
            $return['file_thumb'] = $thumbnail_name;
            $return['thumb_width'] = $image_thumb->width;
            $return['thumb_height'] = $image_thumb->height;
            // return delete url for delete image btn
            $return['delete_url'] = 
            	$this->createUrl('dashboard/OpDeleteImages', 
            		array('ai_id' => $AccommodationImage->ai_id)
            	);
            die(json_encode($return));
		}
		else
		{
			$return['status'] = 'success';
            $return['message'] = 'Chunk uploaded successfully.';
            die(json_encode($return));
		}
    }
    
    /**
     * Deletes images.
     *
     * @return json string
     */
    public function actionOpDeleteImages()
    {
    	// get image id to delete
    	$ai_id = (int)$_GET['ai_id'];
    	$return = array();
    	
    	// TODO: NB!NB! put security check here for image deletion
    	
    	// check if image reference exists in db
    	$image = AccommodationImages::model()->findByPk($ai_id);
    	
    	if ( $image !== false 
    		&& $image->delete() !== false )
    	{
    		unlink(PATH . '/listing_images/' . $image->ai_name);
    		$return['status'] = 'success';
    		$return['message'] = 'Image deleted successfully.';
    	}
    	else 
    	{
    		$return['status'] = 'error';
    		$return['message'] = 'An error ocurred whilst deleting the image.';
    	}
    	
    	die(json_encode($return)); 
    	
    }

    /**
     * Manages the logic to upload a complete file or chunks of a file
     * @param string $targetDir
     * @param string $fileName
     * @param string $chunk
     * @param string $chunks
     */
    private function _uploadFileChunk($targetDir, $fileName, $chunk, $chunks)
    {
        // HTTP headers for no cache etc
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // Make sure the fileName is unique but only if chunking is disabled
        if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName))
        {
            $ext = strrpos($fileName, '.');
            $fileName_a = substr($fileName, 0, $ext);
            $fileName_b = substr($fileName, $ext);

            $count = 1;
            while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
                $count++;

            $fileName = $fileName_a . '_' . $count . $fileName_b;
        }

        // Create target dir
        if ( file_exists($targetDir) === false )
        {
            @mkdir($targetDir, 0777, true);
        }

        // Look for the content type header
        if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
        {
        	$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
        }

        if (isset($_SERVER["CONTENT_TYPE"]))
        {
        	$contentType = $_SERVER["CONTENT_TYPE"];
        }

        // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
        if (strpos($contentType, "multipart") !== false)
        {
            if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name']))
            {
                // Open temp file
                $out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
                if ($out)
                {
                    // Read binary input stream and append it to temp file
                    $in = fopen($_FILES['file']['tmp_name'], "rb");

                    if ($in)
                    {
                        while ($buff = fread($in, 4096))
                        {
                            fwrite($out, $buff);
                        }
                    }
                    else
                    {
                    	$return = array();
			            $return['status'] = 'error';
			            $return['message'] = 'Failed to open input stream.';
			            $return['error_code'] = '103';
			            die(json_encode($return));
                    }
                    fclose($in);
                    fclose($out);
                    @unlink($_FILES['file']['tmp_name']);
                }
                else
                {
                    $return = array();
                    $return['status'] = 'error';
                    $return['message'] = 'Failed to open output stream.';
                    $return['error_code'] = '103';
                    die(json_encode($return));
                }
            }
            else
            {
            	$return = array();
                $return['status'] = 'error';
                $return['message'] = 'Failed to move uploaded file.';
                $return['error_code'] = '103';
                die(json_encode($return));
            }
        }
        else
        {
            // Open temp file
            $out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
            if ($out)
            {
                // Read binary input stream and append it to temp file
                $in = fopen("php://input", "rb");

                if ($in)
                {
                    while ($buff = fread($in, 4096))
                    {
                        fwrite($out, $buff);
                    }
                }
                else
                {
                	$return = array();
	                $return['status'] = 'error';
	                $return['message'] = 'Failed to open input stream.';
	                $return['error_code'] = '103';
	                die(json_encode($return));
                }
                fclose($in);
                fclose($out);
            }
            else
            {
            	$return = array();
                $return['status'] = 'error';
                $return['message'] = 'Failed to open output stream.';
                $return['error_code'] = '103';
                die(json_encode($return));
            }
        }
    }

    /**
     * Displays the html to add accommodation.
     */
    public function actionGetAccomAddEdit()
    {
        $accommodationModel = new Accommodation;
        
        $images = $assigned_features_arr = array(); 
		
		$op = 'add';

        // get accommodation
        if ( isset($_GET['accommodation_id']) && empty($_GET['accommodation_id']) === false )
        {
            $accommodation_id = (int)$_GET['accommodation_id'];
            $accommodationModel = $accommodationModel->findByPk($accommodation_id);
            
            $op = 'edit';
            
            // get assigned images
            $connection = Yii::app()->db;
            $sql = $connection->createCommand();
            $sql->select('ai_id, ai_thumb_name, ai_thumb_width, ai_thumb_height');
            $sql->from('accommodation_images');
            $sql->where('ai_accommodation_id = ' . $accommodation_id);
            $images = $sql->query();
            
            // get assigned features
            $connection = Yii::app()->db;
            $sql = $connection->createCommand();
            $sql->select('afm_feature_id');
            $sql->from('accommodation_features_mappings');
            $sql->where('afm_accommodation_id = ' . $accommodation_id);
            $assigned_features = $sql->queryAll();
            
            $assigned_features_arr = array();
            foreach ( $assigned_features as $assigned_feature )
            {
            	$id = (int)current($assigned_feature);
            	$assigned_features_arr[$id] = $id;
            }
            
        }

    	// get available features
    	$connection = Yii::app()->db;
        $sql = $connection->createCommand();
        $sql->select('feature_id, feature_name')->from('accommodation_features');
        $sql->order('feature_name');
        $features = $sql->query();

    	$this->renderPartial(
            'get_accom_add_edit',
                array(
                    'accommodationModel' => $accommodationModel,
                    'features' => $features,
                	'assigned_features' => $assigned_features_arr,
                    'images' => $images,
                    'op' => $op,
                )
        );
    }
	
	/**
	 * Installs DB
	 * TODO: remove this on live
	 */
	public function actionInstall()
	{
		if ( YII_DEBUG === false ) 
		{
			return;
		}
		$file = realpath(dirname(__FILE__)) . '/install.sql';
		$sql = file_get_contents($file);
		$connection = Yii::app()->db;
		$command=$connection->createCommand($sql);
		$command->execute();
		die('DB installed');
	}

}
