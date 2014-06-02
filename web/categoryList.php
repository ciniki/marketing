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
function ciniki_marketing_web_categoryList($ciniki, $settings, $business_id, $args) {

	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'makePermalink');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryIDTree');

	$strsql = "SELECT id, title, permalink, "
		. "full_description, "
		. "base_notes, "
		. "addon_description, "
		. "addon_notes, "
		. "future_description, "
		. "future_notes, "
		. "signup_text, "
		. "signup_url "
		. "FROM ciniki_marketing_categories "
		. "WHERE ciniki_marketing_categories.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
		. "AND ciniki_marketing_categories.ctype = 10 "
		. "AND (ciniki_marketing_categories.webflags&0x01) = 1 "
		. "ORDER BY sequence, title "
		. "";
	$rc = ciniki_core_dbHashQueryIDTree($ciniki, $strsql, 'ciniki.marketing', array(
		array('container'=>'categories', 'fname'=>'id',
			'fields'=>array('id', 'name'=>'title', 'permalink', 'signup_text', 'signup_url', 
				'full_description', 'base_notes',
				'addon_description', 'addon_notes', 'future_description', 'future_notes')),
		));
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	if( !isset($rc['categories']) ) {
		return array('stat'=>'ok', 'categories'=>array());
	}
	
	$categories = $rc['categories'];

	return array('stat'=>'ok', 'categories'=>$categories);
}
?>
