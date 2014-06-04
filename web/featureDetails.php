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
function ciniki_marketing_web_featureDetails($ciniki, $settings, $business_id, $category_id, $feature_permalink) {

	//
	// Load INTL settings
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'businesses', 'private', 'intlSettings');
	$rc = ciniki_businesses_intlSettings($ciniki, $business_id);
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	$intl_timezone = $rc['settings']['intl-default-timezone'];
	$intl_currency_fmt = numfmt_create($rc['settings']['intl-default-locale'], NumberFormatter::CURRENCY);
	$intl_currency = $rc['settings']['intl-default-currency'];

	$strsql = "SELECT ciniki_marketing_features.id, "
		. "ciniki_marketing_features.section, "
		. "ciniki_marketing_features.sequence, "
		. "ciniki_marketing_features.title, "
		. "ciniki_marketing_features.permalink, "
		. "ciniki_marketing_features.primary_image_id, "
		. "ciniki_marketing_features.full_description, "
		. "ciniki_marketing_feature_images.image_id, "
		. "ciniki_marketing_feature_images.name AS image_name, "
		. "ciniki_marketing_feature_images.permalink AS image_permalink, "
		. "ciniki_marketing_feature_images.description AS image_description, "
		. "UNIX_TIMESTAMP(ciniki_marketing_feature_images.last_updated) AS image_last_updated "
		. "FROM ciniki_marketing_features "
		. "LEFT JOIN ciniki_marketing_feature_images ON ("
			. "ciniki_marketing_features.id = ciniki_marketing_feature_images.feature_id "
			. "AND (ciniki_marketing_feature_images.webflags&0x01) = 0 "
			. ") "
		. "WHERE ciniki_marketing_features.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
		. "AND ciniki_marketing_features.category_id = '" . ciniki_core_dbQuote($ciniki, $category_id) . "' "
		. "AND ciniki_marketing_features.permalink = '" . ciniki_core_dbQuote($ciniki, $feature_permalink) . "' "
		. "";
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryIDTree');
	$rc = ciniki_core_dbHashQueryIDTree($ciniki, $strsql, 'ciniki.features', array(
		array('container'=>'features', 'fname'=>'id', 
			'fields'=>array('id', 'section', 'sequence', 'title', 'permalink', 
				'image_id'=>'primary_image_id', 'full_description')),
		array('container'=>'images', 'fname'=>'image_id', 
			'fields'=>array('image_id', 'title'=>'image_name', 'permalink'=>'image_permalink',
				'description'=>'image_description', 
				'last_updated'=>'image_last_updated')),
		));
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	if( !isset($rc['features']) || count($rc['features']) < 1 ) {
		return array('stat'=>'404', 'err'=>array('pkg'=>'ciniki', 'code'=>'1288', 'msg'=>"I'm sorry, but we can't find the feature you requested."));
	}
	$feature = array_pop($rc['features']);

	return array('stat'=>'ok', 'feature'=>$feature);
}
?>
