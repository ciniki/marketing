//
// The app to add/edit marketing feature images
//
function ciniki_marketing_featureimages() {
    this.webFlags = {
        '1':{'name':'Hidden'},
        };
    this.init = function() {
        //
        // The panel to display the edit form
        //
        this.edit = new M.panel('Edit Image',
            'ciniki_marketing_featureimages', 'edit',
            'mc', 'medium', 'sectioned', 'ciniki.marketing.images.edit');
        this.edit.default_data = {};
        this.edit.data = {};
        this.edit.feature_id = 0;
        this.edit.sections = {
            '_image':{'label':'Photo', 'type':'imageform', 'fields':{
                'image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes', 'controls':'all', 'history':'no'},
            }},
            'info':{'label':'Information', 'type':'simpleform', 'fields':{
                'name':{'label':'Title', 'type':'text'},
                'webflags':{'label':'Website', 'type':'flags', 'join':'yes', 'flags':this.webFlags},
            }},
            '_description':{'label':'Description', 'type':'simpleform', 'fields':{
                'description':{'label':'', 'type':'textarea', 'size':'small', 'hidelabel':'yes'},
            }},
            '_save':{'label':'', 'buttons':{
                'save':{'label':'Save', 'fn':'M.ciniki_marketing_featureimages.saveImage();'},
                'delete':{'label':'Delete', 'fn':'M.ciniki_marketing_featureimages.deleteImage();'},
            }},
        };
        this.edit.fieldValue = function(s, i, d) { 
            if( this.data[i] != null ) {
                return this.data[i]; 
            } 
            return ''; 
        };
        this.edit.fieldHistoryArgs = function(s, i) {
            return {'method':'ciniki.marketing.featureImageHistory', 'args':{'business_id':M.curBusinessID, 
                'feature_image_id':this.feature_image_id, 'field':i}};
        };
        this.edit.addDropImage = function(iid) {
            M.ciniki_marketing_featureimages.edit.setFieldValue('image_id', iid, null, null);
            return true;
        };
        this.edit.addButton('save', 'Save', 'M.ciniki_marketing_featureimages.saveImage();');
        this.edit.addClose('Cancel');
    };

    this.start = function(cb, appPrefix, aG) {
        args = {};
        if( aG != null ) {
            args = eval(aG);
        }

        //
        // Create container
        //
        var appContainer = M.createContainer(appPrefix, 'ciniki_marketing_featureimages', 'yes');
        if( appContainer == null ) {
            alert('App Error');
            return false;
        }

        if( args.add != null && args.add == 'yes' ) {
            this.showEdit(cb, 0, args.feature_id);
        } else if( args.feature_image_id != null && args.feature_image_id > 0 ) {
            this.showEdit(cb, args.feature_image_id);
        }
        return false;
    }

    this.showEdit = function(cb, iid, eid) {
        if( iid != null ) {
            this.edit.feature_image_id = iid;
        }
        if( eid != null ) {
            this.edit.feature_id = eid;
        }
        if( this.edit.feature_image_id > 0 ) {
            var rsp = M.api.getJSONCb('ciniki.marketing.featureImageGet', 
                {'business_id':M.curBusinessID, 'feature_image_id':this.edit.feature_image_id}, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    M.ciniki_marketing_featureimages.edit.data = rsp.image;
                    M.ciniki_marketing_featureimages.edit.refresh();
                    M.ciniki_marketing_featureimages.edit.show(cb);
                });
        } else {
            this.edit.reset();
            this.edit.data = {};
            this.edit.refresh();
            this.edit.show(cb);
        }
    };

    this.saveImage = function() {
        if( this.edit.feature_image_id > 0 ) {
            var c = this.edit.serializeFormData('no');
            if( c != '' ) {
                var rsp = M.api.postJSONFormData('ciniki.marketing.featureImageUpdate', 
                    {'business_id':M.curBusinessID, 
                    'feature_image_id':this.edit.feature_image_id}, c,
                        function(rsp) {
                            if( rsp.stat != 'ok' ) {
                                M.api.err(rsp);
                                return false;
                            } else {
                                M.ciniki_marketing_featureimages.edit.close();
                            }
                        });
            } else {
                this.edit.close();
            }
        } else {
            var c = this.edit.serializeFormData('yes');
            var rsp = M.api.postJSONFormData('ciniki.marketing.featureImageAdd', 
                {'business_id':M.curBusinessID, 'feature_id':this.edit.feature_id}, c,
                    function(rsp) {
                        if( rsp.stat != 'ok' ) {
                            M.api.err(rsp);
                            return false;
                        } else {
                            M.ciniki_marketing_featureimages.edit.close();
                        }
                    });
        }
    };

    this.deleteImage = function() {
        if( confirm('Are you sure you want to delete this image?') ) {
            var rsp = M.api.getJSONCb('ciniki.marketing.featureImageDelete', {'business_id':M.curBusinessID, 
                'feature_image_id':this.edit.feature_image_id}, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    M.ciniki_marketing_featureimages.edit.close();
                });
        }
    };
}
