<?php
//
// Description
// -----------
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:         The ID of the tenant to add the exhibition to.
// exhibition_image_id: The ID of the exhibition image to get.
//
// Returns
// -------
//
function ciniki_artgallery_exhibitionLinkGet($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'exhibition_link_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Exhibition Link'),
        )); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   
    $args = $rc['args'];

    //  
    // Make sure this module is activated, and
    // check permission to run this function for this tenant
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'artgallery', 'private', 'checkAccess');
    $rc = ciniki_artgallery_checkAccess($ciniki, $args['tnid'], 'ciniki.artgallery.exhibitionLinkGet'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');
    $date_format = ciniki_users_dateFormat($ciniki);

    //
    // Get the main information
    //
    $strsql = "SELECT ciniki_artgallery_exhibition_links.id, "
        . "ciniki_artgallery_exhibition_links.name, "
        . "ciniki_artgallery_exhibition_links.url "
        . "FROM ciniki_artgallery_exhibition_links "
        . "WHERE ciniki_artgallery_exhibition_links.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "AND ciniki_artgallery_exhibition_links.id = '" . ciniki_core_dbQuote($ciniki, $args['exhibition_link_id']) . "' "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
    $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.artgallery', array(
        array('container'=>'links', 'fname'=>'id', 'name'=>'link',
            'fields'=>array('id', 'name', 'url')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['links']) ) {
        return array('stat'=>'ok', 'err'=>array('code'=>'ciniki.artgallery.20', 'msg'=>'Unable to find link'));
    }
    $link = $rc['links'][0]['link'];
    
    return array('stat'=>'ok', 'link'=>$link);
}
?>
