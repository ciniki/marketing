//
// The marketing module app
//
function ciniki_marketing_main() {
	//
	// Panels
	//
	this.featureWebFlags = {
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
			'plans':{'label':'Plans', 'type':'simplegrid', 'num_cols':1,
				'headerValues':null,
				'cellClasses':['multiline center nobreak', 'multiline'],
				'noData':'No plans',
				'addTxt':'Add Plan',
				'addFn':'M.ciniki_marketing_main.editPlan(\'M.ciniki_marketing_main.showMenu();\',0);',
				},
			'features':{'label':'Features', 'type':'simplegrid', 'num_cols':1,
				'headerValues':null,
				'cellClasses':['multiline center nobreak', 'multiline'],
				'noData':'No features',
				'addTxt':'Add Feature',
				'addFn':'M.ciniki_marketing_main.editFeature(\'M.ciniki_marketing_main.showMenu();\',0);',
				},
			};
		this.menu.sectionData = function(s) { return this.data[s]; }
		this.menu.noData = function(s) { return this.sections[s].noData; }
		this.menu.cellValue = function(s, i, j, d) {
			if( s == 'plans' ) {
				if( j == 0 ) { return d.plan.group + ' - ' + d.plan.name; } 
			}
			if( s == 'features' ) {
				if( j == 0 ) { return d.feature.title; }
			}
		};
		this.menu.rowFn = function(s, i, d) {
			if( s == 'plans' ) {
				return 'M.ciniki_marketing_main.editPlan(\'M.ciniki_marketing_main.showMenu();\',\'' + d.plan.id + '\');';
			} else if( s == 'features' ) {
				return 'M.ciniki_marketing_main.editFeature(\'M.ciniki_marketing_main.showMenu();\',\'' + d.feature.id + '\');';
			}
		};
		this.menu.addButton('add_p', 'Add', 'M.ciniki_marketing_main.editPlan(\'M.ciniki_marketing_main.showMenu();\',0);');
		this.menu.addButton('add_f', 'Add', 'M.ciniki_marketing_main.editFeature(\'M.ciniki_marketing_main.showMenu();\',0);');
		this.menu.addClose('Back');

		//
		// The panel for editing a plan
		//
		this.editplan = new M.panel('Plan',
			'ciniki_marketing_main', 'editplan',
			'mc', 'medium mediumaside', 'sectioned', 'ciniki.marketing.main.edit');
		this.editplan.data = null;
		this.editplan.plan_id = 0;
        this.editplan.sections = { 
			'_image':{'label':'', 'aside':'yes', 'fields':{
				'primary_image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes', 'controls':'all', 'history':'no'},
			}},
            'general':{'label':'General', 'fields':{
                'group':{'label':'Group', 'type':'text'},
                'name':{'label':'Name', 'type':'text'},
                'webflags':{'label':'Options', 'hint':'', 'type':'flags', 'flags':this.planWebflags},
                }}, 
			'_short_description':{'label':'Short Description', 'fields':{
				'short_description':{'label':'', 'hidelabel':'yes', 'hint':'', 'type':'textarea'},
				}},
			'_full_description':{'label':'Full Description', 'fields':{
				'full_description':{'label':'', 'hidelabel':'yes', 'hint':'', 'type':'textarea'},
				}},
			'_save':{'label':'', 'buttons':{
				'save':{'label':'Save', 'fn':'M.ciniki_marketing_main.savePlan();'},
				'delete':{'label':'Delete', 'fn':'M.ciniki_marketing_main.removePlan();'},
				}},
            };  
		this.editplan.fieldValue = function(s, i, d) { return this.data[i]; }
		this.editplan.fieldHistoryArgs = function(s, i) {
			return {'method':'ciniki.marketing.planHistory', 'args':{'business_id':M.curBusinessID, 
				'plan_id':this.plan_id, 'field':i}};
		}
		this.editplan.addDropImage = function(iid) {
			M.ciniki_marketing_main.editplan.setFieldValue('primary_image_id', iid, null, null);
			return true;
		};
		this.editplan.deleteImage = function(fid) {
			this.setFieldValue(fid, 0, null, null);
			return true;
		};
		this.editplan.addButton('save', 'Save', 'M.ciniki_marketing_main.savePlan();');
		this.editplan.addClose('Cancel');

		//
		// The panel for editing a feature
		//
		this.editfeature = new M.panel('Feature',
			'ciniki_marketing_main', 'editfeature',
			'mc', 'medium mediumaside', 'sectioned', 'ciniki.marketing.main.edit');
		this.editfeature.data = null;
		this.editfeature.feature_id = 0;
        this.editfeature.sections = { 
			'_image':{'label':'', 'aside':'yes', 'fields':{
				'primary_image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes', 'controls':'all', 'history':'no'},
			}},
            'general':{'label':'General', 'fields':{
                'title':{'label':'Title', 'hint':'Title', 'type':'text'},
                'webflags':{'label':'Options', 'hint':'', 'type':'flags', 'flags':this.featureWebflags},
                }}, 
			'_oneline_description':{'label':'One Line Description', 'fields':{
				'oneline_description':{'label':'', 'hidelabel':'yes', 'hint':'', 'type':'textarea'},
				}},
			'_short_description':{'label':'Short Description', 'fields':{
				'short_description':{'label':'', 'hidelabel':'yes', 'hint':'', 'type':'textarea'},
				}},
			'_full_description':{'label':'Full Description', 'fields':{
				'full_description':{'label':'', 'hidelabel':'yes', 'hint':'', 'type':'textarea'},
				}},
			'_save':{'label':'', 'buttons':{
				'save':{'label':'Save', 'fn':'M.ciniki_marketing_main.saveFeature();'},
				'delete':{'label':'Delete', 'fn':'M.ciniki_marketing_main.removeFeature();'},
				}},
            };  
		this.editfeature.fieldValue = function(s, i, d) { return this.data[i]; }
		this.editfeature.fieldHistoryArgs = function(s, i) {
			return {'method':'ciniki.marketing.featureHistory', 'args':{'business_id':M.curBusinessID, 
				'feature_id':this.feature_id, 'field':i}};
		}
		this.editfeature.addDropImage = function(iid) {
			M.ciniki_marketing_main.editfeature.setFieldValue('primary_image_id', iid, null, null);
			return true;
		};
		this.editfeature.deleteImage = function(fid) {
			this.setFieldValue(fid, 0, null, null);
			return true;
		};
		this.editfeature.addButton('save', 'Save', 'M.ciniki_marketing_main.saveFeature();');
		this.editfeature.addClose('Cancel');
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

		this.showMenu(cb);
	}

	this.showMenu = function(cb) {
		this.menu.data = {};
		var rsp = M.api.getJSONCb('ciniki.marketing.plans', 
			{'business_id':M.curBusinessID}, function(rsp) {
				if( rsp.stat != 'ok' ) {
					M.api.err(rsp);
					return false;
				}
				var p = M.ciniki_marketing_main.menu;
				p.data = {'plans':rsp.plans, 'features':rsp.features};
				p.refresh();
				p.show(cb);
			});
	};

	this.editPlan = function(cb, pid) {
		this.editplan.reset();
		if( pid != null ) { this.editplan.plan_id = pid; }

		if( this.editplan.plan_id > 0 ) {
			M.api.getJSONCb('ciniki.marketing.planGet', {'business_id':M.curBusinessID, 
				'plan_id':this.edit.plan_id}, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					}
					var p = M.ciniki_marketing_main.editplan;
					p.data = rsp.plan;
					p.refresh();
					p.show(cb);
				});
		} else {
			this.editplan.data = {};
			this.editplan.refresh();
			this.editplan.show(cb);
		}
	};

	this.savePlan = function() {
		if( this.editplan.plan_id > 0 ) {
			var c = this.editplan.serializeForm('no');
			if( c != '' ) {
				M.api.postJSONCb('ciniki.marketing.planUpdate', 
					{'business_id':M.curBusinessID, 'plan_id':M.ciniki_marketing_main.editplan.plan_id}, c,
					function(rsp) {
						if( rsp.stat != 'ok' ) {
							M.api.err(rsp);
							return false;
						} 
					M.ciniki_marketing_main.editpan.close();
					});
			} else {
				this.editplan.close();
			}
		} else {
			var c = this.editplan.serializeForm('yes');
			M.api.postJSONCb('ciniki.marketing.planAdd', 
				{'business_id':M.curBusinessID}, c, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					} 
					M.ciniki_marketing_main.editplan.close();
				});
		}
	};

	this.removePlan = function() {
		if( confirm("Are you sure you want to remove '" + this.plan.data.name + "' as an plan ?") ) {
			var rsp = M.api.getJSONCb('ciniki.marketing.planDelete', 
				{'business_id':M.curBusinessID, 'plan_id':M.ciniki_marketing_main.editplan.plan_id}, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					}
					M.ciniki_marketing_main.editplan.close();
				});
		}
	}

	this.editFeature = function(cb, fid) {
		this.editfeature.reset();
		if( fid != null ) { this.editfeature.feature_id = fid; }

		if( this.editfeature.feature_id > 0 ) {
			M.api.getJSONCb('ciniki.marketing.featureGet', {'business_id':M.curBusinessID, 
				'feature_id':this.edit.feature_id}, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					}
					var p = M.ciniki_marketing_main.editfeature;
					p.data = rsp.feature;
					p.refresh();
					p.show(cb);
				});
		} else {
			this.editfeature.data = {};
			this.editfeature.refresh();
			this.editfeature.show(cb);
		}
	};

	this.saveFeature = function() {
		if( this.editfeature.feature_id > 0 ) {
			var c = this.editfeature.serializeForm('no');
			if( c != '' ) {
				M.api.postJSONCb('ciniki.marketing.featureUpdate', 
					{'business_id':M.curBusinessID, 'feature_id':M.ciniki_marketing_main.editfeature.feature_id}, c,
					function(rsp) {
						if( rsp.stat != 'ok' ) {
							M.api.err(rsp);
							return false;
						} 
					M.ciniki_marketing_main.editpan.close();
					});
			} else {
				this.editfeature.close();
			}
		} else {
			var c = this.editfeature.serializeForm('yes');
			M.api.postJSONCb('ciniki.marketing.featureAdd', 
				{'business_id':M.curBusinessID}, c, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					} 
					M.ciniki_marketing_main.editfeature.close();
				});
		}
	};

	this.removeFeature = function() {
		if( confirm("Are you sure you want to remove '" + this.feature.data.name + "' as an feature ?") ) {
			var rsp = M.api.getJSONCb('ciniki.marketing.featureDelete', 
				{'business_id':M.curBusinessID, 'feature_id':M.ciniki_marketing_main.editfeature.feature_id}, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					}
					M.ciniki_marketing_main.editfeature.close();
				});
		}
	}
};
