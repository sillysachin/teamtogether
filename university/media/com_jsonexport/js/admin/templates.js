this["Ember"] = this["Ember"] || {};
this["Ember"]["TEMPLATES"] = this["Ember"]["TEMPLATES"] || {};

this["Ember"]["TEMPLATES"]["application"] = Ember.Handlebars.template(function anonymous(Handlebars,depth0,helpers,partials,data) {
this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Ember.Handlebars.helpers); data = data || {};
  var buffer = '', stack1;


  stack1 = helpers._triageMustache.call(depth0, "outlet", {hash:{},hashTypes:{},hashContexts:{},contexts:[depth0],types:["ID"],data:data});
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n");
  return buffer;
  
});

this["Ember"]["TEMPLATES"]["settings"] = Ember.Handlebars.template(function anonymous(Handlebars,depth0,helpers,partials,data) {
this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Ember.Handlebars.helpers); data = data || {};
  var buffer = '', stack1, helper, options, escapeExpression=this.escapeExpression, self=this, helperMissing=helpers.helperMissing;

function program1(depth0,data) {
  
  
  data.buffer.push("Tables");
  }

function program3(depth0,data) {
  
  
  data.buffer.push("Settings");
  }

  data.buffer.push("<div class=\"subhead-collapse\" id=\"ember-toolbar\">\n  <div class=\"subhead\">\n    <div class=\"container-fluid\">\n      <div id=\"container-collapse\" class=\"container-collapse\"></div>\n      <div class=\"row-fluid\">\n        <div class=\"span12\">\n          <div class=\"btn-toolbar\" id=\"toolbar\">\n            <div class=\"btn-group\" id=\"toolbar-apply\">\n              <button ");
  data.buffer.push(escapeExpression(helpers.action.call(depth0, "save", {hash:{
    'target': ("view")
  },hashTypes:{'target': "STRING"},hashContexts:{'target': depth0},contexts:[depth0],types:["STRING"],data:data})));
  data.buffer.push(" class=\"btn btn-small btn-success\">\n              <i class=\"icon-apply icon-white\">\n              </i>\n              Save\n              </button>\n            </div>\n\n            <div class=\"btn-group\" id=\"toolbar-cancel\">\n              <button ");
  data.buffer.push(escapeExpression(helpers.action.call(depth0, "rollbackChanges", {hash:{
    'target': ("view")
  },hashTypes:{'target': "STRING"},hashContexts:{'target': depth0},contexts:[depth0],types:["STRING"],data:data})));
  data.buffer.push(" class=\"btn btn-small\">\n              <i class=\"icon-arrow-left \">\n              </i>\n              Discard unsaved settings\n              </button>\n            </div>\n\n            <div class=\"btn-group divider\">\n            </div>\n\n            <div class=\"btn-group\" id=\"toolbar-help\">\n              <button class=\"btn btn-small\">\n              <i class=\"icon-question-sign\">\n              </i>\n              Help\n              </button>\n            </div>\n          </div>\n        </div>\n      </div>\n    </div>\n  </div>\n</div>\n\n<div class=\"container-main\">\n  <section id=\"content\">\n    <!-- Begin Content -->\n    <div class=\"row-fluid\">\n      <div class=\"span12\">\n        <form action=\"/joomla3/administrator/index.php?option=com_jsonexport&amp;view=products\" method=\"post\" name=\"adminForm\" id=\"adminForm\">\n          <div id=\"j-sidebar-container\" class=\"span2\">\n            <div id=\"sidebar\">\n              <div class=\"sidebar-nav\">\n                <ul id=\"submenu\" class=\"nav nav-list\">\n                  <li>\n                    ");
  stack1 = (helper = helpers.linkTo || (depth0 && depth0.linkTo),options={hash:{},hashTypes:{},hashContexts:{},inverse:self.noop,fn:self.program(1, program1, data),contexts:[depth0],types:["STRING"],data:data},helper ? helper.call(depth0, "tables", options) : helperMissing.call(depth0, "linkTo", "tables", options));
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n                  </li>\n                  <li class=\"active\">\n                    ");
  stack1 = (helper = helpers.linkTo || (depth0 && depth0.linkTo),options={hash:{},hashTypes:{},hashContexts:{},inverse:self.noop,fn:self.program(3, program3, data),contexts:[depth0],types:["STRING"],data:data},helper ? helper.call(depth0, "settings", options) : helperMissing.call(depth0, "linkTo", "settings", options));
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n                  </li>\n                </ul>\n                <hr>\n              </div>\n            </div>\n          </div>\n          <div id=\"j-main-container\" class=\"span10\">\n            <div class=\"span10\">\n              <ul class=\"nav nav-tabs\" id=\"configTabs\">\n                <li class=\"active\"><a href=\"#general\" data-toggle=\"tab\">General</a></li>\n              </ul>\n              <div class=\"tab-content\">\n                <div class=\"tab-pane well active\" id=\"general\">\n                  ");
  data.buffer.push(escapeExpression(helpers.view.call(depth0, "App.SettingsView__GeneralView", {hash:{},hashTypes:{},hashContexts:{},contexts:[depth0],types:["ID"],data:data})));
  data.buffer.push("\n                </div>\n              </div>\n            </div>\n\n          </div>\n        </form>\n      </div>\n    </div>\n  </section>\n</div>\n");
  return buffer;
  
});

this["Ember"]["TEMPLATES"]["settings/general"] = Ember.Handlebars.template(function anonymous(Handlebars,depth0,helpers,partials,data) {
this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Ember.Handlebars.helpers); data = data || {};
  var buffer = '', escapeExpression=this.escapeExpression;


  data.buffer.push("<div class=\"well row\">\n  <div class=\"span6\">\n\n    <div class=\"control-group\">\n      <div class=\"control-label\">\n        <label\n          id=\"jform_info_block_position-lbl\"\n          for=\"jform_info_block_position\"\n          class=\"hasTooltip\"\n          title=\"If yes, the following username and password must be set as query parameters, otherwise no results will be shown\">\n          Require authentication <span class=\"icon-question-sign\"></span>\n        </label>\n      </div>\n      <div class=\"controls\">\n        ");
  data.buffer.push(escapeExpression(helpers.view.call(depth0, "Ember.Select", {hash:{
    'contentBinding': ("view.selectValues.yesOrNo"),
    'optionValuePath': ("content.value"),
    'optionLabelPath': ("content.label"),
    'valueBinding': ("App.settings.general.requireAuthentication")
  },hashTypes:{'contentBinding': "STRING",'optionValuePath': "STRING",'optionLabelPath': "STRING",'valueBinding': "STRING"},hashContexts:{'contentBinding': depth0,'optionValuePath': depth0,'optionLabelPath': depth0,'valueBinding': depth0},contexts:[depth0],types:["ID"],data:data})));
  data.buffer.push("\n      </div>\n    </div>\n\n    <div class=\"control-group\">\n      <div class=\"control-label\">\n        <label>Username</label>\n      </div>\n      <div class=\"controls\">\n        ");
  data.buffer.push(escapeExpression(helpers.view.call(depth0, "Ember.TextField", {hash:{
    'valueBinding': ("App.settings.general.requiredUsername")
  },hashTypes:{'valueBinding': "STRING"},hashContexts:{'valueBinding': depth0},contexts:[depth0],types:["ID"],data:data})));
  data.buffer.push("\n      </div>\n    </div>\n    <div class=\"control-group\">\n      <div class=\"control-label\">\n        <label>Password</label>\n      </div>\n      <div class=\"controls\">\n        ");
  data.buffer.push(escapeExpression(helpers.view.call(depth0, "Ember.TextField", {hash:{
    'valueBinding': ("App.settings.general.requiredPassword")
  },hashTypes:{'valueBinding': "STRING"},hashContexts:{'valueBinding': depth0},contexts:[depth0],types:["ID"],data:data})));
  data.buffer.push("\n      </div>\n    </div>\n\n  </div>\n</div>\n");
  return buffer;
  
});

this["Ember"]["TEMPLATES"]["tables/index"] = Ember.Handlebars.template(function anonymous(Handlebars,depth0,helpers,partials,data) {
this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Ember.Handlebars.helpers); data = data || {};
  var buffer = '', stack1, helper, options, escapeExpression=this.escapeExpression, helperMissing=helpers.helperMissing, self=this;

function program1(depth0,data) {
  
  
  data.buffer.push("Tables");
  }

function program3(depth0,data) {
  
  
  data.buffer.push("Settings");
  }

function program5(depth0,data) {
  
  var buffer = '', stack1;
  data.buffer.push("\n                  ");
  stack1 = helpers.view.call(depth0, "App.TablesView__SingleItemView", {hash:{
    'itemBinding': ("item"),
    'tagName': ("tr"),
    'classNames': ("row0")
  },hashTypes:{'itemBinding': "STRING",'tagName': "STRING",'classNames': "STRING"},hashContexts:{'itemBinding': depth0,'tagName': depth0,'classNames': depth0},inverse:self.noop,fn:self.program(6, program6, data),contexts:[depth0],types:["ID"],data:data});
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n                ");
  return buffer;
  }
function program6(depth0,data) {
  
  var buffer = '', stack1, helper, options;
  data.buffer.push("\n                    <td class=\"order nowrap center hidden-phone\">\n                      ");
  stack1 = helpers._triageMustache.call(depth0, "item.id", {hash:{},hashTypes:{},hashContexts:{},contexts:[depth0],types:["ID"],data:data});
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n                    </td>\n                    <td>\n                      ");
  data.buffer.push(escapeExpression(helpers.view.call(depth0, "Ember.Checkbox", {hash:{
    'checkedBinding': ("item.isSelected")
  },hashTypes:{'checkedBinding': "STRING"},hashContexts:{'checkedBinding': depth0},contexts:[depth0],types:["ID"],data:data})));
  data.buffer.push("\n                    </td>\n                    <td class=\"center\">\n                      <div class=\"btn-group\">\n                        <a class=\"btn btn-micro active\">\n                          ");
  data.buffer.push(escapeExpression((helper = helpers.printStatusIcon || (depth0 && depth0.printStatusIcon),options={hash:{},hashTypes:{},hashContexts:{},contexts:[depth0],types:["ID"],data:data},helper ? helper.call(depth0, "item.status", options) : helperMissing.call(depth0, "printStatusIcon", "item.status", options))));
  data.buffer.push("\n                        </a>\n                      </div>\n                    </td>\n                    <td>\n                      ");
  stack1 = helpers._triageMustache.call(depth0, "item.tableName", {hash:{},hashTypes:{},hashContexts:{},contexts:[depth0],types:["ID"],data:data});
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n                    </td>\n                  ");
  return buffer;
  }

function program8(depth0,data) {
  
  var buffer = '', stack1;
  data.buffer.push("\n\n                  ");
  stack1 = helpers['if'].call(depth0, "view.controller.isLoading", {hash:{},hashTypes:{},hashContexts:{},inverse:self.program(11, program11, data),fn:self.program(9, program9, data),contexts:[depth0],types:["ID"],data:data});
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n\n                ");
  return buffer;
  }
function program9(depth0,data) {
  
  
  data.buffer.push("\n                    <tr><td></td><td></td><td colspan=\"3\"><br>Loading..<br><br></td><td></td><td></td></tr>\n                  ");
  }

function program11(depth0,data) {
  
  
  data.buffer.push("\n                    <tr><td></td><td></td><td colspan=\"3\"><br>No tables found<br><br></td><td></td><td></td></tr>\n                  ");
  }

  data.buffer.push("<div class=\"subhead-collapse\" id=\"ember-toolbar\">\n  <div class=\"subhead\">\n    <div class=\"container-fluid\">\n      <div id=\"container-collapse\" class=\"container-collapse\"></div>\n      <div class=\"row-fluid\">\n        <div class=\"span12\">\n          <div class=\"btn-toolbar\" id=\"toolbar\">\n\n            <div class=\"btn-group\" id=\"toolbar-publish\">\n              <button ");
  data.buffer.push(escapeExpression(helpers.action.call(depth0, "publishItems", {hash:{
    'target': ("view")
  },hashTypes:{'target': "STRING"},hashContexts:{'target': depth0},contexts:[depth0],types:["STRING"],data:data})));
  data.buffer.push(" class=\"btn btn-small\">\n                <i class=\"icon-publish\"></i> Publish\n              </button>\n            </div>\n\n            <div class=\"btn-group\" id=\"toolbar-unpublish\">\n              <button ");
  data.buffer.push(escapeExpression(helpers.action.call(depth0, "unpublishItems", {hash:{
    'target': ("view")
  },hashTypes:{'target': "STRING"},hashContexts:{'target': depth0},contexts:[depth0],types:["STRING"],data:data})));
  data.buffer.push(" class=\"btn btn-small\">\n                <i class=\"icon-unpublish\"></i> Unpublish\n              </button>\n            </div>\n\n            <div class=\"btn-group\" id=\"toolbar-help\">\n              <button class=\"btn btn-small\">\n                <i class=\"icon-question-sign\"></i> Help\n              </button>\n            </div>\n\n          </div>\n        </div>\n      </div>\n    </div>\n  </div>\n</div>\n\n<div class=\" container-main\">\n  <section id=\"content\">\n    <!-- Begin Content -->\n    <div class=\"row-fluid\">\n      <div class=\"span12\">\n        <form action=\"/joomla3/administrator/index.php?option=com_jsonexport&amp;view=products\" method=\"post\" name=\"adminForm\" id=\"adminForm\">\n          <div id=\"j-sidebar-container\" class=\"span2\">\n            <div id=\"sidebar\">\n              <div class=\"sidebar-nav\">\n                <ul id=\"submenu\" class=\"nav nav-list\">\n                  <li class=\"active\">\n                    ");
  stack1 = (helper = helpers.linkTo || (depth0 && depth0.linkTo),options={hash:{},hashTypes:{},hashContexts:{},inverse:self.noop,fn:self.program(1, program1, data),contexts:[depth0],types:["STRING"],data:data},helper ? helper.call(depth0, "tables", options) : helperMissing.call(depth0, "linkTo", "tables", options));
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n                  </li>\n                  <li>\n                    ");
  stack1 = (helper = helpers.linkTo || (depth0 && depth0.linkTo),options={hash:{},hashTypes:{},hashContexts:{},inverse:self.noop,fn:self.program(3, program3, data),contexts:[depth0],types:["STRING"],data:data},helper ? helper.call(depth0, "settings", options) : helperMissing.call(depth0, "linkTo", "settings", options));
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n                  </li>\n                </ul>\n              </div>\n            </div>\n          </div>\n          <div id=\"j-main-container\" class=\"span10\">\n            <div id=\"filter-bar\" class=\"btn-toolbar\">\n              <div class=\"filter-search btn-group pull-left\">\n                <input type=\"text\" id=\"js-filter_search\" placeholder=\"Search by title\">\n              </div>\n              <div class=\"btn-group pull-left\">\n                <button class=\"btn\" ");
  data.buffer.push(escapeExpression(helpers.action.call(depth0, "filterSearch", {hash:{
    'target': ("view")
  },hashTypes:{'target': "STRING"},hashContexts:{'target': depth0},contexts:[depth0],types:["STRING"],data:data})));
  data.buffer.push("><i class=\"icon-search\"></i></button>\n                <button class=\"btn\" ");
  data.buffer.push(escapeExpression(helpers.action.call(depth0, "clearSearch", {hash:{
    'target': ("view")
  },hashTypes:{'target': "STRING"},hashContexts:{'target': depth0},contexts:[depth0],types:["STRING"],data:data})));
  data.buffer.push("><i class=\"icon-remove\"></i></button>\n              </div>\n            </div>\n            <div class=\"clearfix\"> </div>\n            <table class=\"table table-striped\" id=\"weblinkList\">\n              <thead>\n              <tr>\n                <th width=\"1%\" class=\"nowrap center hidden-phone\">\n                  <a><i class=\"icon-menu-2\"></i></a>\n                </th>\n                <th width=\"1%\">\n                  <input type=\"checkbox\" ");
  data.buffer.push(escapeExpression(helpers.action.call(depth0, "checkAllItems", {hash:{
    'target': ("view")
  },hashTypes:{'target': "STRING"},hashContexts:{'target': depth0},contexts:[depth0],types:["STRING"],data:data})));
  data.buffer.push(">\n                </th>\n                <th width=\"1%\" class=\"center\">\n                  <a ");
  data.buffer.push(escapeExpression(helpers.action.call(depth0, "filterByStatus", {hash:{
    'target': ("view")
  },hashTypes:{'target': "STRING"},hashContexts:{'target': depth0},contexts:[depth0],types:["STRING"],data:data})));
  data.buffer.push(">Status</a>\n                </th>\n                <th>\n                  <a ");
  data.buffer.push(escapeExpression(helpers.action.call(depth0, "filterByTitle", {hash:{
    'target': ("view")
  },hashTypes:{'target': "STRING"},hashContexts:{'target': depth0},contexts:[depth0],types:["STRING"],data:data})));
  data.buffer.push(">\n                    Title\n                    <i class=\"icon-arrow-up-3\"></i>\n                  </a>\n                </th>\n              </tr>\n              </thead>\n              <tfoot>\n              <tr>\n                <td colspan=\"10\">\n                  <div class=\"pagination pagination-toolbar\">\n                    <input type=\"hidden\" name=\"limitstart\" value=\"0\">\n                  </div> </td>\n              </tr>\n              </tfoot>\n              <tbody>\n                ");
  stack1 = helpers.each.call(depth0, "item", "in", "view.controller", {hash:{},hashTypes:{},hashContexts:{},inverse:self.program(8, program8, data),fn:self.program(5, program5, data),contexts:[depth0,depth0,depth0],types:["ID","ID","ID"],data:data});
  if(stack1 || stack1 === 0) { data.buffer.push(stack1); }
  data.buffer.push("\n\n              </tbody>\n            </table>\n          </div>\n        </form>\n      </div>\n    </div>\n  </section>\n</div>\n");
  return buffer;
  
});