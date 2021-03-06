<?php
//
// Description
// -----------
// This method will return the list of artgallery for a tenant.  It is restricted
// to tenant owners and sysadmins.
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:     The ID of the tenant to get artgallery for.
//
// Returns
// -------
//
function ciniki_artgallery_exhibitionSearch($ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'start_needle'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Search String'),
        'limit'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Limit'),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $args = $rc['args'];
    
    //  
    // Check access to tnid as owner, or sys admin. 
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'artgallery', 'private', 'checkAccess');
    $rc = ciniki_artgallery_checkAccess($ciniki, $args['tnid'], 'ciniki.artgallery.exhibitionSearch');
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

    //
    // Load the tenant intl settings
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'tenants', 'private', 'intlSettings');
    $rc = ciniki_tenants_intlSettings($ciniki, $args['tnid']);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $intl_timezone = $rc['settings']['intl-default-timezone'];
    $intl_currency_fmt = numfmt_create($rc['settings']['intl-default-locale'], NumberFormatter::CURRENCY);
    $intl_currency = $rc['settings']['intl-default-currency'];

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');
    $date_format = ciniki_users_dateFormat($ciniki);

    //
    // Search
    //

    $rsp = array('stat'=>'ok', 'exhibitions'=>array());

    $strsql = "SELECT ciniki_artgallery_exhibitions.id, "
        . "ciniki_artgallery_exhibitions.name, ";
    if( ($ciniki['tenant']['modules']['ciniki.artgallery']['flags']&0x04) > 0 ) {
        $strsql .= "IFNULL(ciniki_artgallery_locations.name, '') AS location, ";
    } else {
        $strsql .= "ciniki_artgallery_exhibitions.location, ";
    }
    $strsql .= "ciniki_artgallery_exhibitions.short_description, "
        . "IFNULL(DATE_FORMAT(ciniki_artgallery_exhibitions.start_date, '" . ciniki_core_dbQuote($ciniki, $date_format) . "'), '') AS start_date, "
        . "IFNULL(DATE_FORMAT(ciniki_artgallery_exhibitions.end_date, '" . ciniki_core_dbQuote($ciniki, $date_format) . "'), '') AS end_date, "
        . "ciniki_artgallery_exhibitions.permalink "
        . "FROM ciniki_artgallery_exhibitions ";
    if( ($ciniki['tenant']['modules']['ciniki.artgallery']['flags']&0x01) > 0 ) {
        $strsql .= "LEFT JOIN ciniki_artgallery_locations ON (" 
            . "ciniki_artgallery_exhibitions.location_id = ciniki_artgallery_locations.id "
            . "AND ciniki_artgallery_locations.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . ") ";
    }
    $strsql .= "WHERE ciniki_artgallery_exhibitions.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "AND (ciniki_artgallery_exhibitions.name like '" . ciniki_core_dbQuote($ciniki, $args['start_needle']) . "%' "
            . "OR ciniki_artgallery_exhibitions.name like '% " . ciniki_core_dbQuote($ciniki, $args['start_needle']) . "%' "
            . ") "
        . "";
    if( isset($args['location_id']) && $args['location_id'] != '' && $args['location_id'] > 0 ) {
        $strsql .= "AND location_id = '" . ciniki_core_dbQuote($ciniki, $args['location_id']) . "' "
            . "";
    }
    $strsql .= "ORDER BY ciniki_artgallery_exhibitions.start_date DESC, name"
        . "";
    $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.artgallery', array(
        array('container'=>'exhibitions', 'fname'=>'id', 'name'=>'exhibition',
            'fields'=>array('id', 'name', 'permalink', 'location', 'short_description', 
                'start_date', 'end_date')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( isset($rc['exhibitions']) ) {
        $rsp['exhibitions'] = $rc['exhibitions'];
    }

    return $rsp;
}
?>
