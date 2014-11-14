KulerModule.factory('styleCompiler', function () {
	var cache = {};

	function compile(styleTemplate, styles) {
		var template;

		if (typeof cache[styleTemplate] == 'undefined') {
			template = Handlebars.compile(styleTemplate);
		} else {
			template = cache[styleTemplate];
		}

		return template(styles);
	}

	return {
		compile: compile
	};
});

KulerModule.factory('translator', function (_tMessages) {
	return {
		get: function (text, placeholder) {
			placeholder = placeholder || text;

			return _tMessages[text] || placeholder;
		}
	};
});

KulerModule.factory('_t', function () {
	var _messages = {};

	return {
		config: function (messages) {
			_messages = messages;
		},
		get: function (text, placeholder) {
			placeholder = placeholder || text;

			return _messages[text] || placeholder;
		}
	};
});

KulerModule.factory('shortCode', function () {
	return {
		generate: function (moduleName, moduleTitle) {
			var shortcode;

			moduleName = moduleName.toLowerCase();
			moduleTitle = moduleTitle.toLowerCase();

			moduleTitle = moduleTitle.replace(/\s+/g, '_');

			return '[' + moduleName + ' ' + moduleTitle + ']';
		}
	};
});