<?php
//
// Description
// -----------
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:			The ID of the business to add the plan to.
// plan_id:			The ID of the plan to get.
//
// Returns
// -------
//
function ciniki_marketing_planGet($ciniki) {
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
    $rc = ciniki_marketing_checkAccess($ciniki, $args['business_id'], 'ciniki.marketing.planGet'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');
	$date_format = ciniki_users_dateFormat($ciniki);

	//
	// Get the main information
	//
	$strsql = "SELECT ciniki_marketing_plans.id, "
		. "ciniki_marketing_plans.title, "
		. "ciniki_marketing_plans.permalink, "
		. "ciniki_marketing_plans.primary_image_id, "
		. "ciniki_marketing_plans.webflags, "
		. "ciniki_marketing_plans.oneline_description, "
		. "ciniki_marketing_plans.short_description, "
		. "ciniki_marketing_plans.full_description "
		. "FROM ciniki_marketing_plans "
		. "WHERE ciniki_marketing_plans.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
		. "AND ciniki_marketing_plans.id = '" . ciniki_core_dbQuote($ciniki, $args['plan_id']) . "' "
		. "";
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
	$rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.marketing', array(
		array('container'=>'plans', 'fname'=>'id', 'name'=>'plan',
			'fields'=>array('id', 'title', 'permalink', 'primary_image_id', 
				'webflags', 'oneline_description', 'short_description', 'full_description')),
		));
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	if( !isset($rc['plans']) ) {
		return array('stat'=>'ok', 'err'=>array('pkg'=>'ciniki', 'code'=>'1745', 'msg'=>'Unable to find plan'));
	}
	$plan = $rc['plans'][0]['plan'];
	
	return array('stat'=>'ok', 'plan'=>$plan);
}
?>
