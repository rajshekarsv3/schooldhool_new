KulerModule.controller('InstagramCtrl', ['$scope', '$http' ,'$location', '_t', 'shortCode', '$cookies', '$rootScope', function ($scope, $http, $location, _t, shortCode, $cookies, $rootScope) {
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

	// Init active testimonial for each module
	if (Array.isArray($scope.modules)) {
		$scope.modules.forEach(function (module) {
			if (Array.isArray(module.testimonials)) {
				module.testimonials.forEach(function (testimonial) {
					testimonial.active = 0;
				});

				module.testimonials[0].active = 1;
			}
		});
	}

	$scope.addModule = function () {
		var title = _t.get('text_module') + ' ' + ($scope.modules.length + 1),
			module = angular.copy($scope.defaultModule);

		module.mainTitle = title;
		module.title = {};
		module.active = true;

		angular.forEach($scope.languages, function (language) {
			module.title[language.code] = title;
		});

		module.short_code = shortCode.generate($scope.extensionCode, title);

		$scope.modules.push(module);
	};

	$scope.removeModule = function (index) {
		$scope.modules.splice(index, 1);
	};

	$scope.onTitleChanged = function (index, title, languageCode) {
		if (languageCode == $scope.configLanguage) {
			$scope.modules[index].mainTitle = title;
			$scope.modules[index].short_code = shortCode.generate($scope.extensionCode, title);
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

    $scope.addTestimonial = function (index) {
        if (!angular.isArray($scope.modules[index].testimonials)) {
            $scope.modules[index].testimonials = [];
        }

        $scope.modules[index].testimonials.push({
	        active: 1
        });
    };

    $scope.removeTestimonial = function (moduleIndex, testimonialIndex) {
        $scope.modules[moduleIndex].testimonials.splice(testimonialIndex, 1);

	    if (Array.isArray($scope.modules[moduleIndex].testimonials) && $scope.modules[moduleIndex].testimonials[0]) {
		    $scope.modules[moduleIndex].testimonials[0].active = 1;
	    }
    };

	function getActiveModuleIndex() {
		return $cookies[$scope.extensionCode + '_module'] || 0;
	}
}]);