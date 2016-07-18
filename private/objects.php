<?php
//
// Description
// -----------
//
// Arguments
// ---------
//
// Returns
// -------
//
function ciniki_marketing_objects($ciniki) {
    
    $objects = array();
    $objects['category'] = array(
        'name'=>'Category',
        'sync'=>'yes',
        'table'=>'ciniki_marketing_categories',
        'fields'=>array(
            'title'=>array(),
            'permalink'=>array(),
            'sequence'=>array(),
            'ctype'=>array(),
            'webflags'=>array(),
            'short_description'=>array(),
            'full_description'=>array(),
            'base_notes'=>array(),
            'addon_description'=>array(),
            'addon_notes'=>array(),
            'future_description'=>array(),
            'future_notes'=>array(),
            'signup_text'=>array(),
            'signup_url'=>array(),
            ),
        'history_table'=>'ciniki_marketing_history',
        );
    $objects['feature'] = array(
        'name'=>'Features',
        'sync'=>'yes',
        'table'=>'ciniki_marketing_features',
        'fields'=>array(
            'category_id'=>array('ref'=>'ciniki.marketing.category'),
            'section'=>array(),
            'sequence'=>array(),
            'title'=>array(),
            'permalink'=>array(),
            'primary_image_id'=>array('ref'=>'ciniki.images.image'),
            'webflags'=>array(),
            'price'=>array(),
            'short_description'=>array(),
            'full_description'=>array(),
            ),
        'history_table'=>'ciniki_marketing_history',
        );
    $objects['feature_image'] = array(
        'name'=>'Feature Image',
        'sync'=>'yes',
        'table'=>'ciniki_marketing_feature_images',
        'fields'=>array(
            'feature_id'=>array('ref'=>'ciniki.marketing.feature'),
            'name'=>array(),
            'permalink'=>array(),
            'webflags'=>array(),
            'image_id'=>array('ref'=>'ciniki.images.image'),
            'description'=>array(),
            ),
        'history_table'=>'ciniki_marketing_history',
        );
    
    return array('stat'=>'ok', 'objects'=>$objects);
}
?>
