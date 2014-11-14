'use strict';

Handlebars.registerHelper('_fontFamily', function (font) {
	if (font.match(/\w\s+\w/)) {
		return new Handlebars.SafeString('"' + font + '"');
	} else {
		return font;
	}
});

(function (root, factory) {
	if (typeof exports === 'object') {
		module.exports = factory(require('handlebars'));
	} else if (typeof define === 'function' && define.amd) {
		define(['handlebars'], factory);
	} else {
		root.HandlebarsHelpersRegistry = factory(root.Handlebars);
	}
}(this, function (Handlebars) {

	var isArray = function(value) {
		return Object.prototype.toString.call(value) === '[object Array]';
	}

	var ExpressionRegistry = function() {
		this.expressions = [];
	};

	ExpressionRegistry.prototype.add = function (operator, method) {
		this.expressions[operator] = method;
	};

	ExpressionRegistry.prototype.call = function (operator, left, right) {
		if ( ! this.expressions.hasOwnProperty(operator)) {
			throw new Error('Unknown operator "'+operator+'"');
		}

		return this.expressions[operator](left, right);
	};

	var eR = new ExpressionRegistry;
	eR.add('not', function(left, right) {
		return left != right;
	});
	eR.add('>', function(left, right) {
		return left > right;
	});
	eR.add('<', function(left, right) {
		return left < right;
	});
	eR.add('>=', function(left, right) {
		return left >= right;
	});
	eR.add('<=', function(left, right) {
		return left <= right;
	});
	eR.add('===', function(left, right) {
		return left === right;
	});
	eR.add('!==', function(left, right) {
		return left !== right;
	});
	eR.add('in', function(left, right) {
		if ( ! isArray(right)) {
			right = right.split(',');
		}
		return right.indexOf(left) !== -1;
	});

	var isHelper = function() {
		var args = arguments
			,   left = args[0]
			,   operator = args[1]
			,   right = args[2]
			,   options = args[3]
			;

		if (args.length == 2) {
			options = args[1];
			if (left) return options.fn(this);
			return options.inverse(this);
		}

		if (args.length == 3) {
			right = args[1];
			options = args[2];
			if (left == right) return options.fn(this);
			return options.inverse(this);
		}

		if (eR.call(operator, left, right)) {
			return options.fn(this);
		}
		return options.inverse(this);
	};

	Handlebars.registerHelper('is', isHelper);

	Handlebars.registerHelper('nl2br', function(text) {
		var nl2br = (text + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + '<br>' + '$2');
		return new Handlebars.SafeString(nl2br);
	});

	Handlebars.registerHelper('log', function() {
		console.log(['Values:'].concat(
			Array.prototype.slice.call(arguments, 0, -1)
		));
	});

	Handlebars.registerHelper('debug', function() {
		console.log('Context:', this);
		console.log(['Values:'].concat(
			Array.prototype.slice.call(arguments, 0, -1)
		));
	});

	return eR;

}));

KulerModule.value('_tMessages', _tMessages);

KulerModule.controller('ControlPanelCtrl', ['$scope', '$http', '$window', '$upload', 'styleCompiler', '$rootScope', '$sce', '$location', function ($scope, $http, $window, $upload, styleCompiler, $rootScope, $sce, $location) {
	// Hack for post request: http://victorblog.com/2012/12/20/make-angularjs-http-service-behave-like-jquery-ajax/
	$http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
	$http.defaults.transformRequest = [function(data) {
		return angular.isObject(data) && String(data) !== '[object File]' ? jQuery.param(data) : data;
	}];

	$scope.store_id = $location.search().store_id || Kuler.store_id;

	$scope.store = {
		skin_id: ''
	};

	$scope.options = {};
	$scope.loading = false;
	$scope.style = {
		fontFamily: '',
		css: '',
		reloadCss: false
	};

	$scope.fonts = Kuler.fonts;
	$scope.languages = Kuler.languages;
	$scope.defaultLanguage = Kuler.defaultLanguage;

	$scope.loadTheme = function () {
		$scope.removeMessage();
		$scope.loading = true;

		return $http.get(Kuler.store_url, {
			params: {
					store_id: $scope.store_id
				}
			})
			.success(function(data, status, headers, config) {
				$scope.loading = false;
				data.options = prepareOptions(data.options);

				var options = $scope.options;
				angular.extend($scope, data); // TODO: Deep extending
				$scope.options = angular.extend(options, $scope.options);

				// Fix multi domain
				$scope.param_preview_url = $scope.preview_url + '?style_customization=1';

				$scope.$broadcast('options_loaded', $scope.options);
			})
			.error(function (data) {
				if (angular.isDefined(data.error)) {
					$scope.showMessage('error', data.error);
				}

				$scope.loading = false;
			});
	};

	$scope.save = function () {
		$rootScope.$broadcast('save');

		$scope.compileStyle();

		$scope.removeMessage();
		$scope.loading = true;

		$http.post(Kuler.save_url, $.param({
			store_id: $scope.store_id,
			skin_id: $scope.store.skin_id,
			options: $scope.options,
			css: $scope.css
		}))
		.success(function (data) {
			if (data.status) {
				$scope.showMessage('success', data.message);
			}

			$scope.loading = false;
		})
		.error(function (data) {
			$scope.showMessage('error', data.error);
			$scope.loading = false;
		});

		$scope.stylePanelDisplay = false;
	};

	$scope.cancel = function () {
		// TODO: Check changes and alert to user
		$window.location = Kuler.module_url;
		document.cookie = 'kuler_style_customization=1';
	};

	$scope.saveSkinAs = function () {
        if ($scope.new_skin_name) {
	        $rootScope.$broadcast('save');

	        $scope.compileStyle();

	        $scope.removeMessage();
	        $scope.loading = true;

	        $http.post(Kuler.save_skin_as_url, {
		        'store_id': $scope.store_id,
		        'skin_id': $scope.store.skin_id,
		        'new_skin_name': $scope.new_skin_name,
		        'options': $scope.options,
		        css: $scope.css
	        })
	        .success(function (data) {
		        if (data.status) {

			        $scope.new_skin_name = '';

			        $scope.loadTheme().then(function () {
				        $scope.showMessage('success', data.message);
				        $scope.loading = false;
			        });
		        }
	        })
	        .error(function (data) {
		        $scope.showMessage('error', data.error);
		        $scope.loading = false;
	        });
        }
	};

	$scope.removeSkin = function () {
		// TODO: Check whether skin can be deleted or not

		if ($window.confirm('Do you want to delete this skin?')) {
			$scope.loading = true;
			$scope.removeMessage();

			$http.post(Kuler.remove_skin_url, {
				'store_id': $scope.store_id,
				'skin_id': $scope.store.skin_id
			})
			.success(function (data) {
				if (data.status) {
					$scope.loadTheme().then(function () {
						$scope.showMessage('success', data.message);
						$scope.loading = false;
					});
				}
			})
			.error(function (data) {
				$scope.showMessage('error', data.error);
				$scope.loading = false;
			});
		}
	};

	$scope.exportSkins = function () {
		$scope.download_url = Kuler.export_skins_url;
	};

	$scope.importSkins = function ($files) {
		$scope.loading = true;
		$scope.removeMessage();

		for (var i = 0; i < $files.length; i++) {
			var file = $files[i];

			$scope.upload = $upload.upload({
				url: Kuler.import_skins_url,
				file: file
			}).progress(function(evt) {
			}).success(function(data, status, headers, config) {
				$scope.loadTheme();
			});
		}
	};

	$scope.cancelStyleCustomization = function () {
		$scope.stylePanelDisplay = false;
		document.cookie = 'kuler_style_customization=0';
	};

	$scope.showStylePanel = function () {
		$scope.stylePanelDisplay = true;
		$scope.style_panel_template = Kuler.style_panel_url + '&store_id=' + $scope.store_id + '&skin_id=' + $scope.store.skin_id;
		$scope.stylePanelLoading = true;

		document.cookie = 'kuler_style_customization=1';
	};

	$scope.onLoadStylePanel = function () {
		$scope.stylePanelLoading = false;
		$scope.style.reloadCss = true;
	};

	$scope.hiddenStylePanel = false;

	$scope.toggleStylePanel = function () {
		$scope.hiddenStylePanel = !$scope.hiddenStylePanel;
	};

	$scope.style_groups = {}

	$scope.toggleGroupStyle = function (group_key) {
		$scope.style_groups[group_key] = !$scope.style_groups[group_key];
	};

	$scope.compileStyle = function () {
		$scope.css = styleCompiler.compile($scope.style_template, $scope.options);
	};

	$scope.showMessage = function (type, message) {
		// TODO: Improve error alert
		if (type == 'error') {
			$scope.messageType = 'danger';
			$scope.message = message;
		} else if (type == 'success') {
			$scope.messageType = 'success';
			$scope.message = message;
		}
	};

	$scope.removeMessage = function () {
		$scope.message = '';
	};

	$scope._loadTheme = function (storeId) {
		$scope.store_id = storeId;

		$location.search('store_id', storeId);
	};

	// Load store option when store_id changes
	$scope.$watch('store_id', function () {
		$scope.loadTheme();
	});

	// Load skin option when skin id changes
	$scope.$watch('store.skin_id', function (value, oldValue) {
		if (oldValue !== '') { // Not need to load at the first time
			$scope.loading = true;
			$scope.removeMessage();

			$http.get(Kuler.skin_url, {
				params: {
					store_id: $scope.store_id,
					skin_id: value
				}
			})
			.success(function (data) {
				$scope.options = prepareOptions(data.options);

				$scope.$broadcast('options_loaded', $scope.options);

				$scope.loading = false;
			})
			.error(function (data) {
				if (angular.isDefined(data.error)) {
					$scope.showMessage('error', data.error);
				}

					$scope.loading = false;
			});
		}
	});

	// Check where can remove skin or not
	$scope.canRemoveSkin = function () {
		return angular.isDefined($scope.skins) && $scope.skins[$scope.store.skin_id].can_remove;
	};

	function prepareOptions(options) {
		// Convert string to boolean
		angular.forEach(options, function (value, key) {
			if (value === 'true' || value === 'false') {
				options[key] = JSON.parse(value);
			}

			if (typeof value == 'string' && value.match(/^\d+$/)) {
				options[key] = parseInt(value);
			}
		});

		return options;
	}
}]);

KulerModule.controller('PaymentIconsCtrl', function ($scope) {
	$scope.icons = [];

	$scope.$on('options_loaded', function (event, options) {
		options.payment_icons = options.payment_icons || [];
		$scope.icons = options.payment_icons;

		// TODO: Change fool
		for (var i = 0; i < $scope.icons.length; i++) {
			$scope.icons[i].new_tab = JSON.parse($scope.icons[i].new_tab);

			if ($scope.icons[i].sort !== '') {
				$scope.icons[i].sort = parseInt($scope.icons[i].sort);
			}
		}
	});

	$scope.addPaymentIcon = function () {
		$scope.icons.push({
			image: '',
			name: {},
			link: '',
			sort: '',
			new_tab: true
		});
	};

	$scope.removePaymentIcon = function (index) {
		$scope.icons.splice(index, 1);
	};
});

KulerModule.controller('MailServiceCtrl', function ($scope, $http) {
	$scope.lists = {};

	$scope.listsLoading = true;

	$scope.$on('options_loaded', function (event, options) {
		$scope.getLists(options.mail_service);
	});

	$scope.getLists = function (mailService) {
		var params = {
			mail_service: mailService
		};

		if (mailService == 'mailchimp') {
			if (!$scope.options.mailchimp_api_key) {
				return false;
			}

			params.mailchimp_api_key = $scope.options.mailchimp_api_key;
		} else if (mailService == 'icontact') {
			if (!$scope.options.icontact_app_key || !$scope.options.icontact_username || !$scope.options.icontact_password) {
				return false;
			}

			params.icontact_app_key = $scope.options.icontact_app_key;
			params.icontact_username = $scope.options.icontact_username;
			params.icontact_password = $scope.options.icontact_password;
		} else {
			return ;
		}

		$scope.listsLoading = true;

		return $http.get(Kuler.newsletter_lists_url, {
			params: params
		})
		.success(function(data, status, headers, config) {
			if (data.status) {
				$scope.lists = data.lists;
			}

			$scope.listsLoading = false;
		})
		.error(function (data) {
			if (angular.isDefined(data.error)) {
				$scope.showMessage('error', data.error);
			}

			$scope.listsLoading = false;
		});
	};
});

KulerModule.directive('body', function () {
	return {
		restrict: 'A',
		controller: function ($scope, $element) {
			function watch(value) {
				if (value) {
					var $body = angular.element('body');

					$body.css('overflow', 'hidden');
				} else {
					if (!$scope.loading && !$scope.stylePanelDisplay) {
						angular.element('body').css('overflow', 'auto');
					}
				}
			}

			$scope.$watch('loading', watch);
			$scope.$watch('stylePanelDisplay', watch);
		}
	};
});

KulerModule.directive('preview', function (styleCompiler) {
	return {
		link: function (scope, element) {
			var f$, $frameHead, $customStyle, $fontLink;

			var previewFrame = element[0],
				previewWindow = previewFrame.contentWindow;

			function sendToFrame(data) {
				try {
					previewWindow.postMessage(data, previewFrame.src);
				} catch (e) {

				}
			};

			function compile() {
				var css = styleCompiler.compile(scope.style_template, scope.options);

				scope.style.css = css;

				sendToFrame({
					cmd: 'css',
					css: css
				});
			}

			function loadFont(font) {
				if (font) {
					// Process font name
					font = font.replace(/\s+/g, '+');

					sendToFrame({
						cmd: 'font',
						font: font
					});
				}
			}

			// TODO: DOM ready event
			element.on('load', function () {
				compile();

				$('.style-font').each(function () {
					var font = scope.fonts[$(this).val()];

					if (font) {
						loadFont(font.family);
					}
				});
			});

			scope.$watch('options', function () {
				if (scope.stylePanelDisplay) {
					compile();
				}
			}, true);

			// Load Font when font family changed
			scope.$watch('style.fontFamily', function (value) {
				loadFont(value);
			});
		}
	};
});