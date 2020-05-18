//
// The marketing module app
//
function ciniki_marketing_feature() {
    //
    // Panels
    //
    this.featureWebflags = {
        '1':{'name':'Visible'},
        };
    this.featureSections = {
        '10':'Base',
        '30':'Addon',
        '50':'Future',
        };
    this.init = function() {
        //
        // The panel for editing a feature
        //
        this.edit = new M.panel('Feature',
            'ciniki_marketing_feature', 'edit',
            'mc', 'medium mediumaside', 'sectioned', 'ciniki.marketing.feature.edit');
        this.edit.data = null;
        this.edit.feature_id = 0;
        this.edit.additional_images = [];
        this.edit.sections = { 
            '_image':{'label':'', 'aside':'yes', 'type':'imageform', 'fields':{
                'primary_image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes', 
                    'controls':'all', 'history':'no',
                    'addDropImage':function(iid) {
                        M.ciniki_marketing_feature.edit.setFieldValue('primary_image_id',iid);
                        return true;
                        },
                    'addDropImageRefresh':'',
                    'deleteImage':'M.ciniki_marketing_feature.edit.deletePrimaryImage',
                    },
            }},
            'general':{'label':'General', 'fields':{
                'category_id':{'label':'Category', 'type':'select', 'options':[]},
                'section':{'label':'Section', 'type':'toggle', 'toggles':this.featureSections},
                'sequence':{'label':'Sequence', 'type':'text', 'size':'small'},
                'title':{'label':'Title', 'hint':'Title', 'type':'text'},
                'price':{'label':'Price', 'type':'text', 'size':'small'},
                'webflags':{'label':'Options', 'hint':'', 'type':'flags', 'flags':this.featureWebflags},
                }}, 
            '_short_description':{'label':'Short Description', 'fields':{
                'short_description':{'label':'', 'hidelabel':'yes', 'hint':'', 'type':'textarea'},
                }},
            '_full_description':{'label':'Full Description', 'fields':{
                'full_description':{'label':'', 'hidelabel':'yes', 'hint':'', 'type':'textarea'},
                }},
            'images':{'label':'Gallery', 'type':'simplethumbs'},
            '_images':{'label':'', 'type':'simplegrid', 'num_cols':1,
                'addTxt':'Add Additional Image',
                'addFn':'M.startApp(\'ciniki.marketing.featureimages\',null,\'M.ciniki_marketing_feature.edit.addDropImageRefresh();\',\'mc\',{\'feature_id\':M.ciniki_marketing_feature.edit.feature_id,\'add\':\'yes\'});',
                },
            '_save':{'label':'', 'buttons':{
                'save':{'label':'Save', 'fn':'M.ciniki_marketing_feature.saveFeature();'},
                'delete':{'label':'Delete', 'fn':'M.ciniki_marketing_feature.removeFeature();'},
                }},
            };  
        this.edit.sectionData = function(s) { return this.data[s]; }
        this.edit.fieldValue = function(s, i, d) { return this.data[i]; }
        this.edit.fieldHistoryArgs = function(s, i) {
            return {'method':'ciniki.marketing.featureHistory', 'args':{'tnid':M.curTenantID, 
                'feature_id':this.feature_id, 'field':i}};
        }
        this.edit.deletePrimaryImage = function(fid) {
            this.setFieldValue(fid, 0, null, null);
            return true;
        };
        this.edit.addDropImage = function(iid) {
            if( M.ciniki_marketing_feature.edit.feature_id > 0 ) {
                var rsp = M.api.getJSON('ciniki.marketing.featureImageAdd', 
                    {'tnid':M.curTenantID, 'image_id':iid, 
                    'feature_id':M.ciniki_marketing_feature.edit.feature_id});
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                return true;
            } else {
                M.ciniki_marketing_feature.edit.additional_images.push(iid);
            }
        };
        this.edit.addDropImageRefresh = function() {
            if( M.ciniki_marketing_feature.edit.feature_id > 0 ) {
                var rsp = M.api.getJSONCb('ciniki.marketing.featureGet', {'tnid':M.curTenantID, 
                    'feature_id':M.ciniki_marketing_feature.edit.feature_id, 'images':'yes'}, function(rsp) {
                        if( rsp.stat != 'ok' ) {
                            M.api.err(rsp);
                            return false;
                        }
                        var p = M.ciniki_marketing_feature.edit;
                        p.data.images = rsp.feature.images;
                        p.refreshSection('images');
                        p.show();
                    });
            }
            return true;
        };
        this.edit.thumbFn = function(s, i, d) {
            return 'M.startApp(\'ciniki.marketing.featureimages\',null,\'M.ciniki_marketing_feature.edit.addDropImageRefresh();\',\'mc\',{\'feature_id\':M.ciniki_marketing_feature.edit.feature_id,\'feature_image_id\':\'' + d.image.id + '\'});';
        };
        this.edit.addButton('save', 'Save', 'M.ciniki_marketing_feature.saveFeature();');
        this.edit.addClose('Cancel');
    }

    //
    // Arguments:
    // aG - The arguments to be parsed into args
    //
    this.start = function(cb, appPrefix, aG) {
        args = {};
        if( aG != null ) { args = eval(aG); }

        //
        // Create the app container if it doesn't exist, and clear it out
        // if it does exist.
        //
        var appContainer = M.createContainer(appPrefix, 'ciniki_marketing_feature', 'yes');
        if( appContainer == null ) {
            M.alert('App Error');
            return false;
        } 

        this.editFeature(cb, args.feature_id, args.category_id);
    }

    this.editFeature = function(cb, fid, cid) {
        this.edit.reset();
        if( fid != null ) { this.edit.feature_id = fid; }
        if( this.edit.feature_id > 0 ) {
            M.api.getJSONCb('ciniki.marketing.featureGet', {'tnid':M.curTenantID, 
                'feature_id':this.edit.feature_id, 'images':'yes'}, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    var p = M.ciniki_marketing_feature.edit;
                    p.data = rsp.feature;
                    M.ciniki_marketing_feature.setupCategories(cb);
                });
        } else {
            this.edit.data = {'section':10};
            if( cid != null && cid > 0 ) {
                this.edit.data['category_id'] = cid;
            }
            this.edit.additional_images = [];
            this.setupCategories(cb);
        }
    };

    this.setupCategories = function(cb) {
        M.api.getJSONCb('ciniki.marketing.categoryList', {'tnid':M.curTenantID,
            'type':'10'}, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    var p = M.ciniki_marketing_feature.edit;
                    for(i in rsp.categories) {
                        p.sections.general.fields.category_id.options[rsp.categories[i].category.id] = rsp.categories[i].category.title;
                    }
                    p.refresh();
                    p.show(cb);
            });
    };

    this.saveFeature = function() {
        if( this.edit.feature_id > 0 ) {
            var c = this.edit.serializeForm('no');
            if( c != '' ) {
                M.api.postJSONCb('ciniki.marketing.featureUpdate', 
                    {'tnid':M.curTenantID, 'feature_id':M.ciniki_marketing_feature.edit.feature_id}, c,
                    function(rsp) {
                        if( rsp.stat != 'ok' ) {
                            M.api.err(rsp);
                            return false;
                        } 
                    M.ciniki_marketing_feature.edit.close();
                    });
            } else {
                this.edit.close();
            }
        } else {
            var c = this.edit.serializeForm('yes');
            if( this.edit.additional_images.length > 0 ) {
                c += '&images=' . this.edit.additional_images.join(',');
            }
            M.api.postJSONCb('ciniki.marketing.featureAdd', 
                {'tnid':M.curTenantID}, c, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    } 
                    M.ciniki_marketing_feature.edit.close();
                });
        }
    };

    this.removeFeature = function() {
        M.confirm("Are you sure you want to remove '" + this.edit.data.title + "' as a feature ?",null,function() {
            var rsp = M.api.getJSONCb('ciniki.marketing.featureDelete', 
                {'tnid':M.curTenantID, 'feature_id':M.ciniki_marketing_feature.edit.feature_id}, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    M.ciniki_marketing_feature.edit.close();
                });
        });
    }
};
