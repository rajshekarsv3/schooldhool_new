'use strict';

if (typeof Kuler === 'undefined') {
	var Kuler = {};
}

Kuler.waitHtml = '<span class="wait"><img src="view/image/loading.gif" /></span>';

var KulerModule = angular.module('kulerModule', ['ngCookies', 'angularFileUpload', 'ui.bootstrap', 'ui.autocomplete']);

KulerModule.filter('trusted', ['$sce', function ($sce) {
	return function(url) {
		return $sce.trustAsResourceUrl(url);
	};
}]);