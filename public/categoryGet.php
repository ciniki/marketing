<?php
//
// Description
// -----------
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:         The ID of the business to add the category to.
// category_id:         The ID of the category to get.
//
// Returns
// -------
//
function ciniki_marketing_categoryGet($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'category_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Category'),
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
    $rc = ciniki_marketing_checkAccess($ciniki, $args['business_id'], 'ciniki.marketing.categoryGet'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');
    $date_format = ciniki_users_dateFormat($ciniki);

    //
    // Get the main information
    //
    $strsql = "SELECT ciniki_marketing_categories.id, "
        . "ciniki_marketing_categories.title, "
        . "ciniki_marketing_categories.permalink, "
        . "ciniki_marketing_categories.sequence, "
        . "ciniki_marketing_categories.ctype, "
        . "ciniki_marketing_categories.webflags, "
        . "ciniki_marketing_categories.short_description, "
        . "ciniki_marketing_categories.full_description, "
        . "ciniki_marketing_categories.base_notes, "
        . "ciniki_marketing_categories.addon_description, "
        . "ciniki_marketing_categories.addon_notes, "
        . "ciniki_marketing_categories.future_description, "
        . "ciniki_marketing_categories.future_notes, "
        . "ciniki_marketing_categories.signup_text, "
        . "ciniki_marketing_categories.signup_url "
        . "FROM ciniki_marketing_categories "
        . "WHERE ciniki_marketing_categories.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
        . "AND ciniki_marketing_categories.id = '" . ciniki_core_dbQuote($ciniki, $args['category_id']) . "' "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
    $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.marketing', array(
        array('container'=>'categories', 'fname'=>'id', 'name'=>'category',
            'fields'=>array('id', 'title', 'permalink', 'sequence', 'ctype', 
                'webflags', 'short_description', 'full_description', 'signup_text', 'signup_url',
                'base_notes', 'addon_description', 'addon_notes', 'future_description', 'future_notes')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['categories']) ) {
        return array('stat'=>'ok', 'err'=>array('pkg'=>'ciniki', 'code'=>'1745', 'msg'=>'Unable to find category'));
    }
    $category = $rc['categories'][0]['category'];

    return array('stat'=>'ok', 'category'=>$category);
}
?>
