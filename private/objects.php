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
function ciniki_events_objects($ciniki) {
	
	$objects = array();
	$objects['feature'] = array(
		'name'=>'Features',
		'sync'=>'yes',
		'table'=>'ciniki_marketing_features',
		'fields'=>array(
			'title'=>array(),
			'permalink'=>array(),
			'primary_image_id'=>array('ref'=>'ciniki.images.image'),
			'webflags'=>array(),
			'oneline_description'=>array(),
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
	$objects['plan_feature'] = array(
		'name'=>'Plan Feature',
		'sync'=>'yes',
		'table'=>'ciniki_marketing_plan_features',
		'fields'=>array(
			'plan_id'=>array('ref'=>'ciniki.marketing.plan'),
			'feature_id'=>array('ref'=>'ciniki.marketing.feature'),
			'flags'=>array(),
			'notes'=>array(),
			),
		'history_table'=>'ciniki_marketing_history',
		);
	$objects['plan'] = array(
		'name'=>'Plan',
		'sync'=>'yes',
		'table'=>'ciniki_marketing_plans',
		'fields'=>array(
			'group'=>array(),
			'name'=>array(),
			'permalink'=>array(),
			'webflags'=>array(),
			'short_description'=>array(),
			'full_description'=>array(),
			'signup_url'=>array(),
			),
		'history_table'=>'ciniki_marketing_history',
		);
	
	return array('stat'=>'ok', 'objects'=>$objects);
}
?>
