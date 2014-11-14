KulerModule.filter('_t', function (translator) {
	return function (input, placeholder) {
		return translator.get(input, placeholder);
	};
});