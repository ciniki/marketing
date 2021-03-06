<?php
//
// Description
// -----------
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:         The ID of the tenant to add the feature image to.
//
// Returns
// -------
// <rsp stat='ok' />
//
function ciniki_marketing_listings(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        )); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   
    $args = $rc['args'];

    //  
    // Make sure this module is activated, and
    // check permission to run this function for this tenant
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'marketing', 'private', 'checkAccess');
    $rc = ciniki_marketing_checkAccess($ciniki, $args['tnid'], 'ciniki.marketing.listings'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');

    //
    // Get the existing plans
    //
    $strsql = "SELECT id, "
        . "group_name, name "
        . "FROM ciniki_marketing_plans "
        . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "ORDER BY group_name, name "
        . "";
    $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.marketing', array(
        array('container'=>'plans', 'fname'=>'id', 'name'=>'plan',
            'fields'=>array('id', 'group_name', 'name')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['plans']) ) {
        $plans = array();
    } else {
        $plans = $rc['plans'];
    }

    //
    // Get the existing features
    //
    $strsql = "SELECT id, "
        . "title "
        . "FROM ciniki_marketing_features "
        . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "ORDER BY title "
        . "";
    $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.marketing', array(
        array('container'=>'features', 'fname'=>'id', 'name'=>'feature',
            'fields'=>array('id', 'title')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['features']) ) {
        $features = array();
    } else {
        $features = $rc['features'];
    }

    return array('stat'=>'ok', 'plans'=>$plans, 'features'=>$features);
}
?>
