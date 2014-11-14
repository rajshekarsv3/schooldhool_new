KulerModule.value('_tMessages', _tMessages);

KulerModule.controller('ShowcaseCtrl', ['$scope', '$http' ,'$location', '_t', 'shortCode', '$cookies', '$rootScope', function ($scope, $http, $location, _t, shortCode, $cookies, $rootScope) {
	_t.config(Kuler.messages);

	// Hack for post request: http://victorblog.com/2012/12/20/make-angularjs-http-service-behave-like-jquery-ajax/
	$http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
	$http.defaults.transformRequest = [function(data) {
		return angular.isObject(data) && String(data) !== '[object File]' ? jQuery.param(data) : data;
	}];

	$scope.loading          = false;

	$scope.store_id         = Kuler.store_id;
	$scope.extensionCode    = Kuler.extensionCode;
	$scope.defaultModule    = Kuler.defaultModule;
	$scope.modules          = Kuler.modules;
	$scope.languages        = Kuler.languages;
	$scope.configLanguage   = Kuler.configLanguage;

	// Init active showcase for each module
	if (Array.isArray($scope.modules)) {
		$scope.modules.forEach(function (module) {
			if (Array.isArray(module.showcases)) {
				module.showcases.forEach(function (showcase) {
					showcase.active = 0;

					if (Array.isArray(showcase.items)) {
						showcase.items.forEach(function (item) {
							item.open = false;
						});
					}
				});

				module.showcases[0].active = 1;
			}
		});
	}

	$scope.addModule = function () {
		var title = _t.get('text_module') + ' ' + ($scope.modules.length + 1),
			module = angular.copy($scope.defaultModule);

		module.mainTitle = title;
		module.title = {};
		module.active = true;
		module.status = '1';
		module.type = 'slide';

		angular.forEach($scope.languages, function (language) {
			module.title[language.code] = title;
		});

		module.shortcode = shortCode.generate($scope.extensionCode, title);

		$scope.modules.push(module);
	};

	$scope.removeModule = function (index) {
		$scope.modules.splice(index, 1);
	};

	$scope.onTitleChanged = function (index, title, languageCode) {
		if (languageCode == $scope.configLanguage) {
			$scope.modules[index].mainTitle = title;
			$scope.modules[index].shortcode = shortCode.generate($scope.extensionCode, title);
		}
	};

	$scope.save = function () {
		$scope.loading = true;

		$rootScope.$broadcast('save');

		$http
			.post(Kuler.actionUrl, {
				store_id: $scope.store_id,
				modules: $scope.modules
			})
			.success(function (data) {
				$scope.messageType = data.status ? 'success' : 'danger';
				$scope.message = data.message;

				$scope.loading = false;
			})
			.error(function (data) {
				$scope.loading = false;
			});
	};

	$scope.onSelectModule = function (index) {
		document.cookie = $scope.extensionCode + '_module=' + index;
	};

	$scope.selectModule = function (index) {
		if (angular.isDefined($scope.modules[index])) {
			$scope.modules[index].active = true;
		}
	};

	$scope.selectModule(getActiveModuleIndex());

	$scope.selectStore = function (storeId) {
		location = Kuler.storeUrl + '&store_id=' + storeId;
	};

    $scope.addShowcase = function (index) {
        if (!angular.isArray($scope.modules[index].showcases)) {
            $scope.modules[index].showcases = [];
        }

	    var mainTitle = _t.get('text_showcase', 'Showcase') + ' ' + ($scope.modules[index].showcases.length + 1);

        $scope.modules[index].showcases.push({
	        mainTitle: mainTitle,
	        title: getMultilingualTitle(mainTitle),
	        active: 1,
	        status: '1',
	        show_title: '0'
        });
    };

    $scope.removeShowcase = function (moduleIndex, showcaseIndex) {
        $scope.modules[moduleIndex].showcases.splice(showcaseIndex, 1);

	    if (Array.isArray($scope.modules[moduleIndex].showcases) && $scope.modules[moduleIndex].showcases[0]) {
		    $scope.modules[moduleIndex].showcases[0].active = 1;
	    }
    };

	$scope.onShowcaseTitleChanged = function (indexes, title, languageCode) {
		indexes = JSON.parse(indexes);

		if (languageCode == $scope.configLanguage) {
			$scope.modules[indexes.moduleIndex].showcases[indexes.showcaseIndex].mainTitle = title;
		}
	};

	$scope.addItem = function (moduleIndex, showcaseIndex) {
		if (!angular.isDefined($scope.modules[moduleIndex].showcases[showcaseIndex].items)) {
			$scope.modules[moduleIndex].showcases[showcaseIndex].items = [];
		}

		var items = $scope.modules[moduleIndex].showcases[showcaseIndex].items,
			mainTitle = _t.get('text_item', 'Item') + ' ' + (items.length + 1);

		items.push({
			mainTitle: mainTitle,
			title: getMultilingualTitle(mainTitle),
			open: true,
			status: '1',
			show_title: '0',
			type: 'product',
			product_type: 'latest',
			product_category: '0',
			show_product_deal_date: '0',
			show_product_image: '1',
			show_product_name: '1',
			show_product_description: '0',
			show_product_rating: '1',
			show_product_price: '1',
			show_add_to_cart_button: '1',
			show_wish_list_button: '1',
			show_compare_button: '1',
			product_image_width: '250',
			product_image_height: '250',
			product_description_limit: '100',
			product_limit: '4',
			products_per_row: '4'
		});
	};

	$scope.removeItem = function (moduleIndex, showcaseIndex, itemIndex) {
		$scope.modules[moduleIndex].showcases[showcaseIndex].items.splice(itemIndex, 1);
	};

	$scope.onItemTitleChanged = function (indexes, title, languageCode) {
		indexes = JSON.parse(indexes);

		if (languageCode == $scope.configLanguage) {
			$scope.modules[indexes.moduleIndex].showcases[indexes.showcaseIndex].items[indexes.itemIndex].mainTitle = title;
		}
	};

	function getActiveModuleIndex() {
		return $cookies[$scope.extensionCode + '_module'] || 0;
	}

	function getMultilingualTitle(title) {
		var titles = {};

		angular.forEach($scope.languages, function (language) {
			titles[language.code] = title;
		});

		return titles;
	}
}]);