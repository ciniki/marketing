//
// The marketing module app
//
function ciniki_marketing_main() {
    //
    // Panels
    //
    this.featureWebflags = {
        '1':{'name':'Visible'},
        };
    this.planWebflags = {
        '1':{'name':'Visible'},
        };
    this.init = function() {
        //
        // marketing menu panel
        //
        this.menu = new M.panel('Marketing',
            'ciniki_marketing_main', 'menu',
            'mc', 'medium', 'sectioned', 'ciniki.marketing.main.menu');
        this.menu.sections = {
            'feature_categories':{'label':'Categories', 'visible':'no', 'type':'simplegrid', 'num_cols':1,
                'cellClasses':['', ''],
                'noData':'No categories',
                'addTxt':'Add Category',
                'addFn':'M.startApp(\'ciniki.marketing.category\',null,\'M.ciniki_marketing_main.showMenu();\',\'mc\',{\'category_id\':0});',
                },
            };
        this.menu.sectionData = function(s) { return this.data[s]; }
        this.menu.noData = function(s) { return this.sections[s].noData; }
        this.menu.cellValue = function(s, i, j, d) {
            if( s == 'feature_categories' ) {
                if( j == 0 ) { return d.category.title; }
            }
        };
        this.menu.rowFn = function(s, i, d) {
            if( s == 'feature_categories' ) {
                return 'M.ciniki_marketing_main.showFeatures(\'M.ciniki_marketing_main.showMenu();\',\'' + d.category.id + '\',\'' + escape(d.category.title) + '\');';
            }
        };
        this.menu.addClose('Back');

        //
        // features menu panel
        //
        this.features = new M.panel('Features',
            'ciniki_marketing_main', 'features',
            'mc', 'medium', 'sectioned', 'ciniki.marketing.main.features');
        this.features.category_id = 0;
        this.features.sections = {
            'features':{'label':'Features', 'type':'simplegrid', 'num_cols':2,
                'headerValues':['Section', 'Title'],
                'sortable':'yes',
                'sortTypes':['text', 'text'],
                'cellClasses':['', ''],
                'noData':'No features',
                'addTxt':'Add Feature',
                'addFn':'M.startApp(\'ciniki.marketing.feature\',null,\'M.ciniki_marketing_main.showFeatures();\',\'mc\',{\'feature_id\':0,\'category_id\':M.ciniki_marketing_main.features.category_id});',
                },
            };
        this.features.sectionData = function(s) { return this.data[s]; }
        this.features.noData = function(s) { return this.sections[s].noData; }
        this.features.cellValue = function(s, i, j, d) {
            if( s == 'features' ) {
                if( j == 0 ) { return d.feature.section_text; }
                if( j == 1 ) { return d.feature.title; }
            }
        };
        this.features.rowFn = function(s, i, d) {
            if( s == 'features' ) {
                return 'M.startApp(\'ciniki.marketing.feature\',null,\'M.ciniki_marketing_main.showFeatures();\',\'mc\',{\'feature_id\':\'' + d.feature.id + '\'});';
            }
        };
        this.features.addButton('edit', 'Edit', 'M.startApp(\'ciniki.marketing.category\',null,\'M.ciniki_marketing_main.showFeatures();\',\'mc\',{\'category_id\':M.ciniki_marketing_main.features.category_id});');
        this.features.addClose('Back');
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
        var appContainer = M.createContainer(appPrefix, 'ciniki_marketing_main', 'yes');
        if( appContainer == null ) {
            alert('App Error');
            return false;
        } 

        if( (M.curTenant.modules['ciniki.marketing'].flags&0x01) > 0 ) {
            this.menu.sections.feature_categories.visible = 'yes';
        } else {
            this.menu.sections.feature_categories.visible = 'no';
        }

        this.showMenu(cb);
    }

    this.showMenu = function(cb) {
        this.menu.data = {};
        var rsp = M.api.getJSONCb('ciniki.marketing.categoryList', 
            {'tnid':M.curTenantID, 'type':'10'}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                var p = M.ciniki_marketing_main.menu;
                p.data = {'feature_categories':rsp.categories};
                p.refresh();
                p.show(cb);
            });
    };

    this.showFeatures = function(cb, cid, cname) {
        this.features.data = {};
        if( cid != null ) { this.features.category_id = cid; }
        if( cname != null ) { this.features.sections.features.label = unescape(cname); }
        var rsp = M.api.getJSONCb('ciniki.marketing.featureList', 
            {'tnid':M.curTenantID, 'category_id':this.features.category_id}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                var p = M.ciniki_marketing_main.features;
                p.data = {'features':rsp.features};
                p.refresh();
                p.show(cb);
            });
    };
};
