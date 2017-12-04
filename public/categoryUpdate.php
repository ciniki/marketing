<?php
//
// Description
// -----------
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:         The ID of the tenant to add the category image to.
// name:                The name of the image.  
//
// Returns
// -------
// <rsp stat='ok' />
//
function ciniki_marketing_categoryUpdate(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'category_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Category'), 
        'title'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Title'), 
        'permalink'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Permalink'), 
        'sequence'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Section'), 
        'ctype'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Type'), 
        'webflags'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Options'), 
        'short_description'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Short Description'),
        'full_description'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Full Description'),
        'base_notes'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Base Notes'),
        'addon_description'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Addon Description'),
        'addon_notes'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Addon Notes'),
        'future_description'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Future Description'),
        'future_notes'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Future Notes'),
        'signup_text'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Signup Text'),
        'signup_url'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Signup URL'),
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
    $rc = ciniki_marketing_checkAccess($ciniki, $args['tnid'], 'ciniki.marketing.categoryUpdate'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }

    //
    // Get the existing details
    //
    $strsql = "SELECT id, uuid, title "
        . "FROM ciniki_marketing_categories "
        . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "AND id = '" . ciniki_core_dbQuote($ciniki, $args['category_id']) . "' "
        . "";
    $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.marketing', 'item');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['item']) ) {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.marketing.13', 'msg'=>'Feature not found'));
    }
    $item = $rc['item'];

    if( isset($args['category']) || isset($args['title']) ) {
        ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'makePermalink');
        if( isset($args['title']) ) {
            $args['permalink'] = ciniki_core_makePermalink($ciniki, $args['title']);
        } 

        //
        // Make sure the permalink is unique
        //
        $strsql = "SELECT id, title, permalink "
            . "FROM ciniki_marketing_categories "
            . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "AND permalink = '" . ciniki_core_dbQuote($ciniki, $args['permalink']) . "' "
            . "AND id <> '" . ciniki_core_dbQuote($ciniki, $args['category_id']) . "' "
            . "";
        $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.marketing', 'category');
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( $rc['num_rows'] > 0 ) {
            return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.marketing.14', 'msg'=>'You already have an category with this name, please choose another name'));
        }
    }

    //
    // Update the category in the database
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectUpdate');
    return ciniki_core_objectUpdate($ciniki, $args['tnid'], 'ciniki.marketing.category', $args['category_id'], $args);
}
?>
