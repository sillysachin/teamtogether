if (JSNFramework === undefined) {
	var JSNFramework = {
		libs: {},
		paths: {}
	};
}

var frameworkUrl = JSNFramework.rootUrl + '/plugins/system/jsnframework/assets';
var shim = JSNFramework.depends || {};
	shim['jquery.ui']				= { deps: ['jquery'] };
	shim['jquery.cookie']			= { deps: ['jquery'] };
	shim['jquery.hotkeys']			= { deps: ['jquery'] };
	shim['jquery.jstorage']			= { deps: ['jquery'] };
	shim['jquery.jstree']			= { deps: ['jquery'] };
	shim['jquery.layout']			= { deps: ['jquery'] };
	shim['jquery.tinyscrollbar']	= { deps: ['jquery'] };
	shim['jquery.topzindex']		= { deps: ['jquery'] };

var paths = JSNFramework.paths || {};
	paths['jquery.ui']				= frameworkUrl + '/3rd-party/jquery-ui/js/jquery-ui-1.8.16.custom.min';
	paths['jquery.cookie']			= frameworkUrl + '/3rd-party/jquery.cookie/jquery.cookie';
	paths['jquery.hotkeys']			= frameworkUrl + '/3rd-party/jquery.hotkeys/jquery.hotkeys';
	paths['jquery.jstorage']		= frameworkUrl + '/3rd-party/jquery.jstorage/jquery.jstorage';
	paths['jquery.jstree']			= frameworkUrl + '/3rd-party/jquery.jstree/jquery.jstree';
	paths['jquery.layout']			= frameworkUrl + '/3rd-party/jquery.layout/js/jquery.layout-latest';
	paths['jquery.tinyscrollbar']	= frameworkUrl + '/3rd-party/jquery.tinyscrollbar/jquery.tinyscrollbar';
	paths['jquery.topzindex']		= frameworkUrl + '/3rd-party/jquery.topzindex/jquery.topzindex';
	paths['framework']				= frameworkUrl + '/joomlashine/js';
	paths['framework/3rd']			= frameworkUrl + '/3rd-party';

/**
 * Update configuration values for requirejs
 */
require.config({
	/**
	 * Auto append version number in the query string to prevent cache
	 * @type string
	 */
	urlArgs: 'v=' + JSNFramework.version,

	/**
	 * Configuration of the path for all jQuery plugins and other libraries
	 * that contains in assets folder of framework
	 * @type object
	 */
	paths: paths,

	/**
	 * Declaration of jQuery plugins
	 * @type object
	 */
	shim: shim
});

/**
 * Define jQuery module for RequireJS and call noConflict to ensure it will working with 
 * Mootools instance
 * @return jQuery
 */
define('jquery', [frameworkUrl + '/3rd-party/jquery/jquery-1.7.1.min.js'], function () {
	return jQuery.noConflict();
});

/**
 * Execute modules that registered in execute list
 */
if (JSNFramework.exec !== undefined)
{
	for (var index = 0; index < JSNFramework.exec.length; index++) {
		var module = JSNFramework.exec[index].module,
			params = JSNFramework.exec[index].params;

		// Load the module
		require([module], function (ModuleObject) {
			// Create instance for the module object
			new ModuleObject(params);
		});
	}
}