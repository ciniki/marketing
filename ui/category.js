//
// The marketing module app
//
function ciniki_marketing_category() {
    //
    // Panels
    //
    this.categoryWebflags = {
        '1':{'name':'Visible'},
        };
    this.init = function() {
        //
        // The panel for editing a category
        //
        this.edit = new M.panel('Category',
            'ciniki_marketing_category', 'edit',
            'mc', 'medium mediumaside', 'sectioned', 'ciniki.marketing.category.edit');
        this.edit.data = null;
        this.edit.category_id = 0;
        this.edit.sections = { 
            'general':{'label':'General', 'fields':{
                'title':{'label':'Title', 'type':'text'},
                'sequence':{'label':'Sequence', 'type':'text', 'size':'small'},
                'webflags':{'label':'Options', 'hint':'', 'type':'flags', 'flags':this.categoryWebflags},
                }}, 
            'signup':{'label':'Signup', 'fields':{
                'signup_text':{'label':'Text', 'type':'text'},
                'signup_url':{'label':'URL', 'type':'text'},
                }},
            '_short_description':{'label':'Short Description', 'fields':{
                'short_description':{'label':'', 'hidelabel':'yes', 'type':'textarea', 'size':'small'},
                }},
            '_full_description':{'label':'Full Description', 'fields':{
                'full_description':{'label':'', 'hidelabel':'yes', 'type':'textarea', 'size':'small'},
                }},
            '_base_notes':{'label':'Base Notes', 'fields':{
                'base_notes':{'label':'', 'hidelabel':'yes', 'type':'textarea', 'size':'small'},
                }},
            '_addon_description':{'label':'Addon Description', 'fields':{
                'addon_description':{'label':'', 'hidelabel':'yes', 'type':'textarea', 'size':'small'},
                }},
            '_addon_notes':{'label':'Addon Notes', 'fields':{
                'addon_notes':{'label':'', 'hidelabel':'yes', 'type':'textarea', 'size':'small'},
                }},
            '_future_description':{'label':'Future Description', 'fields':{
                'future_description':{'label':'', 'hidelabel':'yes', 'type':'textarea', 'size':'small'},
                }},
            '_future_notes':{'label':'Future Notes', 'fields':{
                'future_notes':{'label':'', 'hidelabel':'yes', 'type':'textarea', 'size':'small'},
                }},
            '_save':{'label':'', 'buttons':{
                'save':{'label':'Save', 'fn':'M.ciniki_marketing_category.saveCategory();'},
                'delete':{'label':'Delete', 'fn':'M.ciniki_marketing_category.removeCategory();'},
                }},
            };  
        this.edit.sectionData = function(s) { return this.data[s]; }
        this.edit.fieldValue = function(s, i, d) { return this.data[i]; }
        this.edit.fieldHistoryArgs = function(s, i) {
            return {'method':'ciniki.marketing.categoryHistory', 'args':{'tnid':M.curTenantID, 
                'category_id':this.category_id, 'field':i}};
        }
        this.edit.addButton('save', 'Save', 'M.ciniki_marketing_category.saveCategory();');
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
        var appContainer = M.createContainer(appPrefix, 'ciniki_marketing_category', 'yes');
        if( appContainer == null ) {
            M.alert('App Error');
            return false;
        } 

        this.editCategory(cb, args.category_id);
    }

    this.editCategory = function(cb, fid) {
        this.edit.reset();
        if( fid != null ) { this.edit.category_id = fid; }
        if( this.edit.category_id > 0 ) {
            M.api.getJSONCb('ciniki.marketing.categoryGet', {'tnid':M.curTenantID, 
                'category_id':this.edit.category_id, 'images':'yes'}, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    var p = M.ciniki_marketing_category.edit;
                    p.data = rsp.category;
                    p.refresh();
                    p.show(cb);
                });
        } else {
            this.edit.data = {'section':10};
            this.edit.additional_images = [];
            this.edit.refresh();
            this.edit.show(cb);
        }
    };

    this.saveCategory = function() {
        if( this.edit.category_id > 0 ) {
            var c = this.edit.serializeForm('no');
            if( c != '' ) {
                M.api.postJSONCb('ciniki.marketing.categoryUpdate', 
                    {'tnid':M.curTenantID, 'category_id':M.ciniki_marketing_category.edit.category_id}, c,
                    function(rsp) {
                        if( rsp.stat != 'ok' ) {
                            M.api.err(rsp);
                            return false;
                        } 
                    M.ciniki_marketing_category.edit.close();
                    });
            } else {
                this.edit.close();
            }
        } else {
            var c = this.edit.serializeForm('yes');
            c += '&ctype=10';
            M.api.postJSONCb('ciniki.marketing.categoryAdd', 
                {'tnid':M.curTenantID}, c, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    } 
                    M.ciniki_marketing_category.edit.close();
                });
        }
    };

    this.removeCategory = function() {
        M.confirm("Are you sure you want to remove '" + this.edit.data.title + "' as a category ?",null,function() {
            var rsp = M.api.getJSONCb('ciniki.marketing.categoryDelete', 
                {'tnid':M.curTenantID, 'category_id':M.ciniki_marketing_category.edit.category_id}, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    M.ciniki_marketing_category.edit.close();
                });
        });
    }
};
