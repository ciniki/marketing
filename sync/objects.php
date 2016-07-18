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
function ciniki_marketing_sync_objects($ciniki, &$sync, $business_id, $args) {
    ciniki_core_loadMethod($ciniki, 'ciniki', 'marketing', 'private', 'objects');
    return ciniki_marketing_objects($ciniki);
}
?>
