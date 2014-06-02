//
// The marketing module app
//
function ciniki_marketing_plan() {
	//
	// Panels
	//
	this.planWebflags = {
		'1':{'name':'Visible'},
		};
	this.init = function() {
		//
		// The panel for editing a plan
		//
		this.edit = new M.panel('Plan',
			'ciniki_marketing_plan', 'edit',
			'mc', 'medium', 'sectioned', 'ciniki.marketing.plan.edit');
		this.edit.data = null;
		this.edit.plan_id = 0;
        this.edit.sections = { 
            'general':{'label':'General', 'fields':{
                'group_name':{'label':'Group', 'type':'text'},
                'name':{'label':'Name', 'type':'text'},
                'price':{'label':'Price', 'type':'text', 'size':'small'},
                'webflags':{'label':'Options', 'hint':'', 'type':'flags', 'flags':this.planWebflags},
                'signup_url':{'label':'Signup URL', 'type':'text'},
                }}, 
			'_short_description':{'label':'Short Description', 'fields':{
				'short_description':{'label':'', 'hidelabel':'yes', 'hint':'', 'type':'textarea'},
				}},
			'_full_description':{'label':'Full Description', 'fields':{
				'full_description':{'label':'', 'hidelabel':'yes', 'hint':'', 'type':'textarea'},
				}},
			'_save':{'label':'', 'buttons':{
				'save':{'label':'Save', 'fn':'M.ciniki_marketing_plan.savePlan();'},
				'delete':{'label':'Delete', 'fn':'M.ciniki_marketing_plan.removePlan();'},
				}},
            };  
		this.edit.fieldValue = function(s, i, d) { return this.data[i]; }
		this.edit.fieldHistoryArgs = function(s, i) {
			return {'method':'ciniki.marketing.planHistory', 'args':{'business_id':M.curBusinessID, 
				'plan_id':this.plan_id, 'field':i}};
		}
		this.edit.addDropImage = function(iid) {
			M.ciniki_marketing_plan.edit.setFieldValue('primary_image_id', iid, null, null);
			return true;
		};
		this.edit.deleteImage = function(fid) {
			this.setFieldValue(fid, 0, null, null);
			return true;
		};
		this.edit.addButton('save', 'Save', 'M.ciniki_marketing_plan.savePlan();');
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
		var appContainer = M.createContainer(appPrefix, 'ciniki_marketing_plan', 'yes');
		if( appContainer == null ) {
			alert('App Error');
			return false;
		} 

		this.editPlan(cb, args.plan_id);
	}

	this.editPlan = function(cb, pid) {
		this.edit.reset();
		if( pid != null ) { this.edit.plan_id = pid; }

		if( this.edit.plan_id > 0 ) {
			M.api.getJSONCb('ciniki.marketing.planGet', {'business_id':M.curBusinessID, 
				'plan_id':this.edit.plan_id}, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					}
					var p = M.ciniki_marketing_plan.edit;
					p.data = rsp.plan;
					p.refresh();
					p.show(cb);
				});
		} else {
			this.edit.data = {};
			this.edit.refresh();
			this.edit.show(cb);
		}
	};

	this.savePlan = function() {
		if( this.edit.plan_id > 0 ) {
			var c = this.edit.serializeForm('no');
			if( c != '' ) {
				M.api.postJSONCb('ciniki.marketing.planUpdate', 
					{'business_id':M.curBusinessID, 'plan_id':M.ciniki_marketing_plan.edit.plan_id}, c,
					function(rsp) {
						if( rsp.stat != 'ok' ) {
							M.api.err(rsp);
							return false;
						} 
					M.ciniki_marketing_plan.editpan.close();
					});
			} else {
				this.edit.close();
			}
		} else {
			var c = this.edit.serializeForm('yes');
			M.api.postJSONCb('ciniki.marketing.planAdd', 
				{'business_id':M.curBusinessID}, c, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					} 
					M.ciniki_marketing_plan.edit.close();
				});
		}
	};

	this.removePlan = function() {
		if( confirm("Are you sure you want to remove '" + this.edit.data.name + "' as a plan ?") ) {
			var rsp = M.api.getJSONCb('ciniki.marketing.planDelete', 
				{'business_id':M.curBusinessID, 'plan_id':M.ciniki_marketing_plan.edit.plan_id}, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					}
					M.ciniki_marketing_plan.edit.close();
				});
		}
	}
};
