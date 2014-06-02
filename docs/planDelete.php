<?php
//
// Description
// ===========
//
// Arguments
// ---------
// 
// Returns
// -------
// <rsp stat='ok' />
//
function ciniki_marketing_planDelete(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
		'plan_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Plan'),
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
    $rc = ciniki_marketing_checkAccess($ciniki, $args['business_id'], 'ciniki.marketing.planImageDelete'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

	//
	// Get the existing plan information
	//
	$strsql = "SELECT id, uuid FROM ciniki_marketing_plans "
		. "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
		. "AND id = '" . ciniki_core_dbQuote($ciniki, $args['plan_id']) . "' "
		. "";
	$rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.marketing', 'item');
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	if( !isset($rc['item']) ) {
		return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'1758', 'msg'=>'Plan does not exist'));
	}
	$item = $rc['item'];

	//
	// Get the links to features to be removed
	//
	$strsql = "SELECT id, uuid, feature_id "
		. "FROM ciniki_marketing_plan_features "
		. "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
		. "AND plan_id = '" . ciniki_core_dbQuote($ciniki, $args['plan_id']) . "' "
		. "";
	$rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.marketing', 'link');
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	$plan_features = $rc['rows'];
	foreach($plan_features as $plan) {
		$rc = ciniki_core_objectDelete($ciniki, $args['business_id'], 'ciniki.marketing.plan_feature', $plan['id'], $plan['uuid'], 0x07);
		if( $rc['stat'] != 'ok' ) {
			return $rc;
		}
	}

	//
	// Delete the object
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectDelete');
	return ciniki_core_objectDelete($ciniki, $args['business_id'], 'ciniki.marketing.plan', $args['plan_id'], $item['uuid'], 0x07);
}
?>
