<?php
//
// Description
// -----------
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:         The ID of the tenant to add the feature to.
// feature_id:          The ID of the feature to get.
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
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'feature_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Feature'),
        'images'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Images'),
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
    $rc = ciniki_marketing_checkAccess($ciniki, $args['tnid'], 'ciniki.marketing.featureGet'); 
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
        . "ciniki_marketing_features.category_id, "
        . "ciniki_marketing_features.section, "
        . "ciniki_marketing_features.sequence, "
        . "ciniki_marketing_features.title, "
        . "ciniki_marketing_features.permalink, "
        . "ciniki_marketing_features.primary_image_id, "
        . "ciniki_marketing_features.webflags, "
        . "ciniki_marketing_features.price, "
        . "ciniki_marketing_features.short_description, "
        . "ciniki_marketing_features.full_description "
        . "FROM ciniki_marketing_features "
        . "WHERE ciniki_marketing_features.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "AND ciniki_marketing_features.id = '" . ciniki_core_dbQuote($ciniki, $args['feature_id']) . "' "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
    $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.marketing', array(
        array('container'=>'features', 'fname'=>'id', 'name'=>'feature',
            'fields'=>array('id', 'category_id', 'section', 'sequence', 'title', 'permalink', 'primary_image_id', 
                'webflags', 'price', 'short_description', 'full_description')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['features']) ) {
        return array('stat'=>'ok', 'err'=>array('code'=>'ciniki.marketing.17', 'msg'=>'Unable to find feature'));
    }
    $feature = $rc['features'][0]['feature'];

    //
    // Get the images
    //
    if( isset($args['images']) && $args['images'] == 'yes' ) {
        $strsql = "SELECT id, name, image_id, webflags "
            . "FROM ciniki_marketing_feature_images "
            . "WHERE feature_id = '" . ciniki_core_dbQuote($ciniki, $args['feature_id']) . "' "
            . "AND tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "";
        $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.info', array(
            array('container'=>'images', 'fname'=>'id', 'name'=>'image',
                'fields'=>array('id', 'name', 'image_id', 'webflags')),
            ));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( isset($rc['images']) ) {
            $feature['images'] = $rc['images'];
            ciniki_core_loadMethod($ciniki, 'ciniki', 'images', 'private', 'loadCacheThumbnail');
            foreach($feature['images'] as $inum => $img) {
                if( isset($img['image']['image_id']) && $img['image']['image_id'] > 0 ) {
                    $rc = ciniki_images_loadCacheThumbnail($ciniki, $args['tnid'], 
                        $img['image']['image_id'], 75);
                    if( $rc['stat'] != 'ok' ) {
                        return $rc;
                    }
                    $feature['images'][$inum]['image']['image_data'] = 'data:image/jpg;base64,' . base64_encode($rc['image']);
                }
            }
        }
    }
    
    return array('stat'=>'ok', 'feature'=>$feature);
}
?>
