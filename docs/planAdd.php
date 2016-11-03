<?php
//
// Description
// -----------
// This method will add a new plan for the business.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:     The ID of the business to add the plan to.
// name:            The name of the plan.
// url:             (optional) The URL for the plan website.
// description:     (optional) The description for the plan.
// start_date:      (optional) The date the plan starts.  
// end_date:        (optional) The date the plan ends, if it's longer than one day.
//
// Returns
// -------
// <rsp stat="ok" id="42">
//
function ciniki_marketing_planAdd(&$ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'group_name'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Group'), 
        'name'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'name'), 
        'permalink'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Permalink'), 
        'price'=>array('required'=>'no', 'blank'=>'no', 'default'=>'0', 'type'=>'currency', 'name'=>'Price'), 
        'primary_image_id'=>array('required'=>'no', 'default'=>'0', 'blank'=>'yes', 'name'=>'Image'), 
        'webflags'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Options'), 
        'short_description'=>array('required'=>'no', 'default'=>'', 'blank'=>'yes', 'name'=>'Short Description'),
        'full_description'=>array('required'=>'no', 'default'=>'', 'blank'=>'yes', 'name'=>'Full Description'),
        'signup_url'=>array('required'=>'no', 'default'=>'', 'blank'=>'yes', 'name'=>'Signup URL'),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $args = $rc['args'];
    
    if( !isset($args['permalink']) || $args['permalink'] == '' ) {  
        ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'makePermalink');
        $args['permalink'] = ciniki_core_makePermalink($ciniki, $args['group_name'] . '-' . $args['name']);
    }

    //
    // Check access to business_id as owner
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'marketing', 'private', 'checkAccess');
    $ac = ciniki_marketing_checkAccess($ciniki, $args['business_id'], 'ciniki.marketing.planAdd');
    if( $ac['stat'] != 'ok' ) {
        return $ac;
    }

    //
    // Check the permalink doesn't already exist
    //
    $strsql = "SELECT id FROM ciniki_marketing_plans "
        . "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' " 
        . "AND permalink = '" . ciniki_core_dbQuote($ciniki, $args['permalink']) . "' "
        . "";
    $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.marketing', 'plan');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( $rc['num_rows'] > 0 ) {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.marketing.1', 'msg'=>'You already have an plan with this name, please choose another name'));
    }

    //
    // Add the plan to the database
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectAdd');
    return ciniki_core_objectAdd($ciniki, $args['business_id'], 'ciniki.marketing.plan', $args);
}
?>
