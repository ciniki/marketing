<?php
//
// Description
// -----------
// The module flags
//
// Arguments
// ---------
//
// Returns
// -------
//
function ciniki_marketing_flags($ciniki, $modules) {
	$flags = array(
		array('flag'=>array('bit'=>'1', 'name'=>'Features')),
		array('flag'=>array('bit'=>'2', 'name'=>'Feature Categories')),
		);

	return array('stat'=>'ok', 'flags'=>$flags);
}
?>
