'use strict';

KulerModule.directive('persistentTab', function($cookies) {
	return {
		controller: function ($scope, $element, $attrs) {
			this.hideTabs = function () {
				$element.find('.tab-content').hide();
			};

			this.getActiveTab = function () {
				return 'module-1';
			};
		},
		link: function(scope, element, attr) {
			var $tab = element.children(),
				tabKey = attr.persistentTab;

			$tab.removeClass('active');

			// Show persistent tab
			var activeTab;
			if (typeof $cookies[tabKey] != 'undefined') {
				activeTab = $cookies[tabKey];
			} else {
				activeTab = $tab.eq(0).attr('data-target');
			}

			element.find('.tab-content').hide();

			// Hide tabs and show only active tab
			$tab
				.show()
				.each(function () {
					var dist = this.getAttribute('data-target');
					if (dist == activeTab) {
						$(dist).show();
						$(this).addClass('active');
					} else {
						$(dist).hide();
					}
				});

			element.on('click', '[data-target]', function (evt) {
				evt.preventDefault();

				var $this = $(this),
					distSelector = this.getAttribute('data-target');

				if ($this.data('targetDisabled')) {
					return;
				}

				element.find('[data-target]')
					.removeClass('active')
					.each(function () {
						$(this.getAttribute('data-target')).hide();
					});

				// Show active tab
				$this.addClass('active');
				$(distSelector).show();

				// Store active tab in cookie
				document.cookie = tabKey + '=' + distSelector;
			});
		}
	};
});

KulerModule.directive('tab', function () {
	return {
		restrict: 'A',
		replace: false,
		require: '^persistentTab',
		link: function (scope, element, attrs, persistentTabCtrl) {

		}
	};
});

KulerModule.directive('tabContent', function () {
	return {
		restrict: 'A',
		replace: false,
		require: '^persistentTab',
/*		link: function (scope, element, attrs, persistentTabCtrl) {
			console.log(element.attr('id'), persistentTabCtrl.getActiveTab());

			if (element.attr('id') != persistentTabCtrl.getActiveTab()) {
				element.hide();
			} else {
				element.show();
			}
		},*/
	};
});

KulerModule.directive('newModule', function () {
	return {
		restrict: 'CA',
		replace: false,
		link: function (scope, element) {
			element.trigger('click');
		}
	};
});

KulerModule.directive('editor', function ($rootScope, $timeout) {
	CKEDITOR.config.autoParagraph = false;

	return {
		require: '?ngModel',
		link: function(scope, elm, attr, ngModel) {
			var ck = CKEDITOR.replace(elm[0], {
				filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=' + Kuler.token,
				filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=' + Kuler.token,
				filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=' + Kuler.token,
				filebrowserUploadUrl: 'index.php?route=common/filemanager&token=' + Kuler.token,
				filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=' + Kuler.token,
				filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=' + Kuler.token
			});

			if (!ngModel) return;

			function updateModel() {
				scope.$apply(function () {
					ngModel.$setViewValue(ck.getData());
				});
			}

			ck.on('imageDone', updateModel);
			ck.on('change', updateModel);
			ck.on('key', updateModel);
			ck.on('pasteState', updateModel);

			ngModel.$render = function(value) {
				ck.setData(ngModel.$viewValue);
			};
		}
	};
});

KulerModule.directive('switch', function() {
	return {
		replace: true,
		restrict: 'E',
		scope: {
			input: '='
		},
		templateUrl: 'view/kuler/angular/templates/switch.html',
		link: function(scope, element, attrs) {
			var input = element.find('input[type="checkbox"]')[0];

			if (!angular.isDefined(scope.input)) {
				scope.input = false;
			}

			input.checked = scope.input;
		}
	};
});

KulerModule.directive('switch2', function() {
	return {
		replace: true,
		restrict: 'E',
		scope: {
			input: '='
		},
		templateUrl: 'view/kuler/angular/templates/switch2.html',
		link: function(scope, element, attrs) {
			var input = element.find('input[type="checkbox"]')[0];

			if (!angular.isDefined(scope.input)) {
				scope.input = false;
			}

			input.checked = scope.input;
		}
	};
});

KulerModule.directive('imageSelector', function () {
	var idCounter = 0;

	var DIALOG_ID = 'dialog',
		DIALOG_SELECTOR = '#' + DIALOG_ID;

	return {
		restrict: 'E',
		replace: true,
		scope: {
			image: '=',
			hasButton: '@'
		},
		templateUrl: 'view/kuler/angular/templates/image-selector.html',
		controller: function ($scope) {
			if (!angular.isDefined($scope.image)) {
				$scope.image = '';
			}

			$scope.input_id = 'image_selector_' + ++ idCounter;

			$scope.browse = function () {
				var token = Kuler.token,
					fieldId = $scope.input_id;

				$(DIALOG_SELECTOR).remove();

				$('body').append('<div id="'+ DIALOG_ID +'"><iframe src="index.php?route=common/filemanager&token=' + token + '&field=' + fieldId + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');

				$(DIALOG_SELECTOR).dialog({
					title: 'Image Selector',
					bgiframe: false,
					width: 700,
					height: 400,
					resizable: false,
					modal: false,
					close: function() {
						var val = $('#' + fieldId).val();

						if(val) {
							$scope.image = val;
							$scope.$apply();
						}
					}
				});
			};

			// Clear image
			$scope.clear = function () {
				$scope.image = '';
			};

			$scope.$watch('image', function (value) {
				if (value) {
					$scope.thumb = Kuler.front_base + 'image/' + value;
				} else {
					// TODO: Process thumb
					$scope.thumb = 'http://www.placehold.it/100x100/EFEFEF/AAAAAA&text=no+image';
				}
			});
		}
	};
});

KulerModule.directive('multilingualInput', function () {
	return {
		scope: {
			languages: '=',
			input: '=',
			index: '@',
			onChange: '='
		},
		restrict: 'E',
		replace: true,
		templateUrl: 'view/kuler/angular/templates/multilingual-input.html',
		controller: function ($scope, $element, $attrs) {
			if (!angular.isDefined($scope.input)) {
				$scope.input = {};
			}
		},
		link: function (scope) {

		}
	};
});

KulerModule.directive('colorPicker', function() {
	return {
		require: '?ngModel',
		restrict: 'A',
		link: function(scope, element, attrs, ngModel) {
			element
				.on('changeColor', function (evt) {
					var format = element.data('colorFormat'), color;

					if (format == 'rgba') {
						var rgba = evt.color.toRGB();

						color = 'rgba('+ rgba.r + ', '+ rgba.g + ', ' + rgba.b + ', ' + rgba.a + ')';
					} else {
						color = evt.color.toHex();
					}

					$(this).val(color);

					ngModel.$setViewValue(color);
					scope.$apply();
				})
				.colorpicker();

			scope.$watch(attrs['ngModel'], function (value) {
				// TODO: Change background color in format rgba
				element.css('backgroundColor', value);
			});
		}
	};
});

KulerModule.directive('styleImageSelector', function () {
	var DIALOG_ID = 'dialog',
		DIALOG_SELECTOR = '#' + DIALOG_ID;

	var idCounter = 0;

	return {
		scope: {
			image: '='
		},
		restrict: 'E',
		replace: true,
		templateUrl: 'view/kuler/angular/templates/style-image-selector.html',
		controller: ['$scope', '$element', '$attrs', function ($scope, $element, $attrs) {
			$scope.input_id = 'style_image_selector_' + ++idCounter;

			if (!angular.isObject($scope.image)) {
				$scope.image = {};
			}

			// Show the image selector dialog
			$scope.browse = function () {
				var token = Kuler.token,
					fieldId = $scope.input_id;

				$(DIALOG_SELECTOR).remove();

				$('body').append('<div id="'+ DIALOG_ID +'"><iframe src="index.php?route=common/filemanager&token=' + token + '&field=' + fieldId + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');

				$(DIALOG_SELECTOR).dialog({
					title: 'Image Selector',
					bgiframe: false,
					width: 700,
					height: 400,
					resizable: false,
					modal: false,
					close: function() {
						var val = $('#' + fieldId).val();

						if(val) {
							$scope.image.path = 'image/' + val;
							$scope.$apply();
						}
					}
				});
			};

			// Clear image
			$scope.clear = function () {
				$scope.image.path = '';
			};

			// Auto update image when the path model changes
			$scope.$watch('image.path', function (value) {
				if (value) {
					$scope.thumb = Kuler.front_base + value;
				} else {
					// TODO: Process thumb
					$scope.thumb = 'http://www.placehold.it/100x100/EFEFEF/AAAAAA&text=no+image';
				}
			});
		}],
		link: function (scope, element, attrs) {

		}
	};
});

KulerModule.directive('styleFont', function () {
	return {
		restrict: 'E',
		replace: true,
		scope: {
			fonts: '=',
			font: '=',
			watchingFont: '=',
			fontFamily: '@',
			fontWeight: '@',
			fontSize: '@',
			fontStyle: '@',
			textTransform: '@'
		},
		templateUrl: 'view/kuler/angular/templates/style-font.html',
		controller: function ($scope, $element, $attrs) {
			if (!angular.isDefined($scope.font)) {
				$scope.font = {};
			}

			$scope.loadFont = function () {
				if ($scope.font.font_family) {
					$scope.watchingFont = $scope.font.font_family;
				}
			};
		}
	};
});

KulerModule.directive('stylePattern', function () {
	return {
		restrict: 'E',
		replace: true,
		templateUrl: 'view/kuler/angular/templates/style-pattern.html',
		scope: {
			pattern: '=',
			sourcePatterns: '='
		},
		controller: function ($scope, $element, $attrs) {
			$scope.selectPattern = function (pattern) {
				$scope.pattern = pattern;
			};

			$scope.activate = function (pattern) {
				return pattern == $scope.pattern;
			};

			$scope.clear = function () {
				$scope.pattern = '';
			};
		}
	};
});

KulerModule.directive('ngEnter', function () {
	return function (scope, element, attrs) {
		element.bind("keydown keypress", function (event) {
			if(event.which === 13) {
				scope.$apply(function (){
					scope.$eval(attrs.ngEnter);
				});

				event.preventDefault();
			}
		});
	};
});

KulerModule.directive('eventPreventDefault', function () {
	return {
		strict: 'EAC',
		replace: false,
		link: function (scope, element) {
			element.on('click', function (evt) {
				evt.preventDefault();
			});
		}
	};
});

KulerModule.directive('eventStopPropagation', function () {
	return {
		restrict: 'EAC',
		replace: false,
		link: function (scope, element) {
			element.on('click', function (evt) {
				evt.stopPropagation();
			});
		}
	};
});

KulerModule.directive('imageModal', function ($modal) {
	return {
		restrict: 'A',
		replace: false,
		link: function (scope, element, attrs) {
			element.on('click', function () {
				$modal.open({
					template: '<div class="modal-body"><img src="'+ attrs['image'] +'" /></div>'
				});
			});
		}
	};
});

KulerModule.directive('autoFocus', function () {
	return {
		restrict: 'AC',
		controller: function ($scope, $element, $attrs) {
			$scope.focus = function () {

			};
		}
	};
});

KulerModule.directive('autocomplete', function ($http) {
	// Hack for post request: http://victorblog.com/2012/12/20/make-angularjs-http-service-behave-like-jquery-ajax/
	$http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
	$http.defaults.transformRequest = [function(data) {
		return angular.isObject(data) && String(data) !== '[object File]' ? jQuery.param(data) : data;
	}];

	return {
		restrict: 'E',
		replace: true,
		scope: {
			model: '=',
			itemType: '@'
		},
		templateUrl: 'view/kuler/angular/templates/autocomplete.html',
		controller: function ($scope, $element, $attrs) {
			$scope.itemUrl = 'index.php?route=module/kuler_cp/autocomplete&store_id='+ Kuler.store_id +'&type='+ $scope.itemType +'&token='+ Kuler.token;

			if (!angular.isArray($scope.model)) {
				$scope.model = [];
			}

			$scope.items = [];

			$scope.itemsLoading = false;

			$scope.options = {
				options: {
					html: true,
					onlySelect: true,
					source: function (request, response) {
						$.ajax({
							url: $scope.itemUrl +'&filter_name=' +  encodeURIComponent(request.term),
							dataType: 'json',
							success: function(json) {
								if (json.length == 0) {
									json.push({
										name: 'Not Found', // TODO: Support multilingual for not found text
										item_id: 0
									});
								}

								response($.map(json, function(item) {
									return {
										label: item.name,
										value: item.item_id,
										link: item.link
									}
								}));
							}
						});
					},
					select: function (event, ui) {
						if (!ui.item.value) {
							return;
						}

						ui.item.name = ui.item.label;
						$scope.items.push(ui.item);

						$scope.model.push(ui.item.value);

						$scope.input = '';
					}
				},
				methods: {}
			};

			$scope.removeItem = function (index) {
				$scope.model.splice(index, 1);
				$scope.items.splice(index, 1);
			};

			$scope.loadItems = function () {
				$scope.itemsLoading = true;

				$.ajax({
					url: $scope.itemUrl,
					data: {
						item_id: $scope.model
					},
					dataType: 'json',
					success: function(data) {
						$scope.$apply(function () {
							$scope.items = data;
							$scope.itemsLoading = false;
						});
					}
				});
			};

			if (angular.isArray($scope.model) && $scope.model.length) {
				$scope.loadItems();
			}
		}
	};
});