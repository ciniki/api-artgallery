<?php
//
// Description
// -----------
//
// Arguments
// ---------
//
// Returns
// -------
//
function ciniki_artgallery_objects($ciniki) {
	
	$objects = array();
	$objects['exhibition'] = array(
		'name'=>'Exhibition',
		'sync'=>'yes',
		'table'=>'ciniki_artgallery_exhibitions',
		'fields'=>array(
			'name'=>array(),
			'permalink'=>array(),
			'webflags'=>array(),
			'start_date'=>array(),
			'end_date'=>array(),
			'primary_image_id'=>array('ref'=>'ciniki.images.image'),
			'location'=>array(),
			'short_description'=>array(),
			'long_description'=>array(),
			),
		'history_table'=>'ciniki_artgallery_history',
		);
	$objects['exhibition_image'] = array(
		'name'=>'Exhibition Image',
		'sync'=>'yes',
		'table'=>'ciniki_artgallery_exhibition_images',
		'fields'=>array(
			'exhibition_id'=>array('ref'=>'ciniki.artgallery.exhibition'),
			'name'=>array(),
			'permalink'=>array(),
			'webflags'=>array(),
			'sequence'=>array(),
			'image_id'=>array('ref'=>'ciniki.images.image'),
			'description'=>array(),
			'url'=>array(),
			),
		'history_table'=>'ciniki_artgallery_history',
		);
	$objects['exhibition_link'] = array(
		'name'=>'Exhibition Link',
		'sync'=>'yes',
		'table'=>'ciniki_artgallery_exhibition_links',
		'fields'=>array(
			'exhibition_id'=>array('ref'=>'ciniki.artgallery.exhibition'),
			'name'=>array(),
			'url'=>array(),
			),
		'history_table'=>'ciniki_artgallery_history',
		);
	$objects['file'] = array(
		'name'=>'File',
		'sync'=>'yes',
		'table'=>'ciniki_artgallery_files',
		'fields'=>array(
			'type'=>array(),
			'extension'=>array(),
			'status'=>array(),
			'name'=>array(),
			'permalink'=>array(),
			'webflags'=>array(),
			'description'=>array(),
			'org_filename'=>array(),
			'publish_date'=>array(),
			'binary_content'=>array('history'=>'no'),
			),
		'history_table'=>'ciniki_artgallery_history',
		);
	$objects['setting'] = array(
		'type'=>'settings',
		'name'=>'Art Gallery Settings',
		'table'=>'ciniki_artgallery_settings',
		'history_table'=>'ciniki_artgallery_history',
		);
	
	return array('stat'=>'ok', 'objects'=>$objects);
}
?>