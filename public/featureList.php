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
function ciniki_marketing_featureList(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'category_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Category'), 
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
    $rc = ciniki_marketing_checkAccess($ciniki, $args['tnid'], 'ciniki.marketing.featureList'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }

    //
    // Get the existing details
    //
    $strsql = "SELECT id, "
        . "category_id, section, section AS section_text, sequence, title "
        . "FROM ciniki_marketing_features "
        . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "";
    if( isset($args['category_id']) && $args['category_id'] != '' ) {
        $strsql .= "AND category_id = '" . ciniki_core_dbQuote($ciniki, $args['category_id']) . "' ";
    }
    $strsql .= "ORDER BY section, sequence, title "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
    $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.marketing', array(
        array('container'=>'features', 'fname'=>'id', 'name'=>'feature',
            'fields'=>array('id', 'category_id', 'section', 'section_text', 'sequence', 'title'),
            'maps'=>array('section_text'=>array('10'=>'Base', '30'=>'Addon', '50'=>'Future'))),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['features']) ) {
        return array('stat'=>'ok', 'features'=>array());
    }
    $features = $rc['features'];

    return array('stat'=>'ok', 'features'=>$features);
}
?>
