<?php
//
// Description
// -----------
// This method will add a new feature for the business.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:		The ID of the business to add the feature to.
// name:			The name of the feature.
// url:				(optional) The URL for the feature website.
// description:		(optional) The description for the feature.
// start_date:		(optional) The date the feature starts.  
// end_date:		(optional) The date the feature ends, if it's longer than one day.
//
// Returns
// -------
// <rsp stat="ok" id="42">
//
function ciniki_marketing_featureAdd(&$ciniki) {
	//
	// Find all the required and optional arguments
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
	$rc = ciniki_core_prepareArgs($ciniki, 'no', array(
		'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
		'category_id'=>array('required'=>'no', 'blank'=>'no', 'default'=>'', 'name'=>'Category'), 
		'section'=>array('required'=>'no', 'blank'=>'no', 'default'=>'10', 'name'=>'Section'), 
		'sequence'=>array('required'=>'no', 'blank'=>'no', 'default'=>'10', 'name'=>'Section'), 
		'title'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Title'), 
		'permalink'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Permalink'), 
		'primary_image_id'=>array('required'=>'no', 'default'=>'0', 'blank'=>'yes', 'name'=>'Image'), 
		'webflags'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Options'), 
		'price'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'currency', 'default'=>'0', 'name'=>'Price'), 
		'short_description'=>array('required'=>'no', 'default'=>'', 'blank'=>'yes', 'name'=>'Short Description'),
		'full_description'=>array('required'=>'no', 'default'=>'', 'blank'=>'yes', 'name'=>'Full Description'),
		'images'=>array('required'=>'no', 'default'=>'', 'blank'=>'yes', 'type'=>'idlist', 'name'=>'Images'),
		));
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	$args = $rc['args'];
	
	if( !isset($args['permalink']) || $args['permalink'] == '' ) {	
		ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'makePermalink');
		$args['permalink'] = ciniki_core_makePermalink($ciniki, $args['title']);
	}

	//
	// Check access to business_id as owner
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'marketing', 'private', 'checkAccess');
	$ac = ciniki_marketing_checkAccess($ciniki, $args['business_id'], 'ciniki.marketing.featureAdd');
	if( $ac['stat'] != 'ok' ) {
		return $ac;
	}

	//
	// Check the permalink doesn't already exist
	//
	$strsql = "SELECT id FROM ciniki_marketing_features "
		. "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' " 
		. "AND permalink = '" . ciniki_core_dbQuote($ciniki, $args['permalink']) . "' "
		. "AND category_id = '" . ciniki_core_dbQuote($ciniki, $args['category_id']) . "' "
		. "";
	$rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.marketing', 'feature');
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	if( $rc['num_rows'] > 0 ) {
		return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'1764', 'msg'=>'You already have an feature with this name, please choose another name'));
	}

	//
	// Add the feature to the database
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectAdd');
	return ciniki_core_objectAdd($ciniki, $args['business_id'], 'ciniki.marketing.feature', $args);
}
?>
