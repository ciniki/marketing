<?php
//
// Description
// -----------
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:			The ID of the business to add the feature image to.
//
// Returns
// -------
// <rsp stat='ok' />
//
function ciniki_marketing_categoryList(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'type'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Category Type'), 
        )); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   
    $args = $rc['args'];

    //  
    // Make sure this module is activated, and
    // check permission to run this function for this business
    //  
	ciniki_core_loadMethod($ciniki, 'ciniki', 'marketing', 'private', 'checkAccess');
    $rc = ciniki_marketing_checkAccess($ciniki, $args['business_id'], 'ciniki.marketing.categoryList'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }

	//
	// Get the existing details
	//
	$strsql = "SELECT id, title "
		. "FROM ciniki_marketing_categories "
		. "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
		. "AND ctype = '" . ciniki_core_dbQuote($ciniki, $args['type']) . "' "
		. "ORDER BY sequence, title "
		. "";
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
	$rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.marketing', array(
		array('container'=>'categories', 'fname'=>'id', 'name'=>'category',
			'fields'=>array('id', 'title')),
		));
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	if( !isset($rc['categories']) ) {
		return array('stat'=>'ok', 'categories'=>array());
	}
	$categories = $rc['categories'];

	return array('stat'=>'ok', 'categories'=>$categories);
}
?>
