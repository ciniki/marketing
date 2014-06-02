<?php
//
// Description
// -----------
// This method will add a new category for the business.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:		The ID of the business to add the category to.
// name:			The name of the category.
// url:				(optional) The URL for the category website.
// description:		(optional) The description for the category.
// start_date:		(optional) The date the category starts.  
// end_date:		(optional) The date the category ends, if it's longer than one day.
//
// Returns
// -------
// <rsp stat="ok" id="42">
//
function ciniki_marketing_categoryAdd(&$ciniki) {
	//
	// Find all the required and optional arguments
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
	$rc = ciniki_core_prepareArgs($ciniki, 'no', array(
		'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
		'title'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Title'), 
		'permalink'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Permalink'), 
		'sequence'=>array('required'=>'no', 'blank'=>'no', 'default'=>'10', 'name'=>'Section'), 
		'ctype'=>array('required'=>'no', 'blank'=>'no', 'default'=>'10', 'name'=>'Type'), 
		'webflags'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'0', 'name'=>'Options'), 
		'short_description'=>array('required'=>'no', 'default'=>'', 'blank'=>'yes', 'name'=>'Short Description'),
		'full_description'=>array('required'=>'no', 'default'=>'', 'blank'=>'yes', 'name'=>'Full Description'),
		'base_notes'=>array('required'=>'no', 'default'=>'', 'blank'=>'yes', 'name'=>'Base Notes'),
		'addon_description'=>array('required'=>'no', 'default'=>'', 'blank'=>'yes', 'name'=>'Addon Description'),
		'addon_notes'=>array('required'=>'no', 'default'=>'', 'blank'=>'yes', 'name'=>'Addon Notes'),
		'future_description'=>array('required'=>'no', 'default'=>'', 'blank'=>'yes', 'name'=>'Future Description'),
		'future_notes'=>array('required'=>'no', 'default'=>'', 'blank'=>'yes', 'name'=>'Future Notes'),
		'signup_text'=>array('required'=>'no', 'default'=>'', 'blank'=>'yes', 'name'=>'Signup Text'),
		'signup_url'=>array('required'=>'no', 'default'=>'', 'blank'=>'yes', 'name'=>'Signup URL'),
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
	$ac = ciniki_marketing_checkAccess($ciniki, $args['business_id'], 'ciniki.marketing.categoryAdd');
	if( $ac['stat'] != 'ok' ) {
		return $ac;
	}

	//
	// Check the permalink doesn't already exist
	//
	$strsql = "SELECT id "
		. "FROM ciniki_marketing_categories "
		. "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' " 
		. "AND ctype = '" . ciniki_core_dbQuote($ciniki, $args['ctype']) . "' "
		. "AND permalink = '" . ciniki_core_dbQuote($ciniki, $args['permalink']) . "' "
		. "";
	$rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.marketing', 'category');
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	if( $rc['num_rows'] > 0 ) {
		return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'1744', 'msg'=>'You already have an category with this name, please choose another name'));
	}

	//
	// Add the category to the database
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectAdd');
	return ciniki_core_objectAdd($ciniki, $args['business_id'], 'ciniki.marketing.category', $args);
}
?>
