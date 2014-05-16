<?php
//
// Description
// -----------
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:			The ID of the business to add the feature to.
// feature_id:			The ID of the feature to get.
//
// Returns
// -------
//
function ciniki_marketing_featureGet($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
		'feature_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Feature'),
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
    $rc = ciniki_marketing_checkAccess($ciniki, $args['business_id'], 'ciniki.marketing.featureGet'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');
	$date_format = ciniki_users_dateFormat($ciniki);

	//
	// Get the main information
	//
	$strsql = "SELECT ciniki_marketing_features.id, "
		. "ciniki_marketing_features.title, "
		. "ciniki_marketing_features.permalink, "
		. "ciniki_marketing_features.primary_image_id, "
		. "ciniki_marketing_features.webflags, "
		. "ciniki_marketing_features.oneline_description, "
		. "ciniki_marketing_features.short_description, "
		. "ciniki_marketing_features.full_description "
		. "FROM ciniki_marketing_features "
		. "WHERE ciniki_marketing_features.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
		. "AND ciniki_marketing_features.id = '" . ciniki_core_dbQuote($ciniki, $args['feature_id']) . "' "
		. "";
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
	$rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.marketing', array(
		array('container'=>'features', 'fname'=>'id', 'name'=>'feature',
			'fields'=>array('id', 'title', 'permalink', 'primary_image_id', 
				'webflags', 'oneline_description', 'short_description', 'full_description')),
		));
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	if( !isset($rc['features']) ) {
		return array('stat'=>'ok', 'err'=>array('pkg'=>'ciniki', 'code'=>'1745', 'msg'=>'Unable to find feature'));
	}
	$feature = $rc['features'][0]['feature'];
	
	return array('stat'=>'ok', 'feature'=>$feature);
}
?>
