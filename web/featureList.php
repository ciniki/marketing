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
function ciniki_marketing_web_featureList($ciniki, $settings, $tnid, $args) {


    $strsql = "SELECT ciniki_marketing_features.id, "
        . "ciniki_marketing_features.section, "
        . "ciniki_marketing_features.title, "
        . "ciniki_marketing_features.permalink, "
        . "ciniki_marketing_features.primary_image_id, "
        . "ciniki_marketing_features.short_description, "
        . "IF(ciniki_marketing_features.full_description<>'', 'yes', 'no') AS is_details "
        . "FROM ciniki_marketing_categories "
        . "LEFT JOIN ciniki_marketing_features ON ("
            . "ciniki_marketing_categories.id = ciniki_marketing_features.category_id "
            . "AND ciniki_marketing_features.tnid = '" . ciniki_core_dbQuote($ciniki, $tnid) . "' "
            . "AND (ciniki_marketing_features.webflags&0x01) = 1 "
            . ") "
        . "WHERE ciniki_marketing_categories.id = '" . ciniki_core_dbQuote($ciniki, $args['category_id']) . "' "
        . "AND (ciniki_marketing_categories.webflags&0x01) = 1 "
        . "ORDER BY ciniki_marketing_features.section, "
            . "ciniki_marketing_features.sequence, ciniki_marketing_features.title "
        . "";
    $rc = ciniki_core_dbHashQueryIDTree($ciniki, $strsql, 'ciniki.customers', array(
        array('container'=>'sections', 'fname'=>'section',
            'fields'=>array('number'=>'section')),
        array('container'=>'features', 'fname'=>'id',
            'fields'=>array('id', 'name'=>'title')),
        array('container'=>'list', 'fname'=>'id', 
            'fields'=>array('id', 'title', 'permalink', 'image_id'=>'primary_image_id',
                'description'=>'short_description', 'is_details')),
        ));

    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['sections']) ) {
        return array('stat'=>'ok', 'sections'=>array());
    }

    return array('stat'=>'ok', 'sections'=>$rc['sections']);
}
?>
