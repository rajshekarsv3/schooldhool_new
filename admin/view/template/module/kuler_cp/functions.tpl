<?php
	function renderBeginOptionContainer()
	{
		return '<div class="form-horizontal">';
	}

	function renderCloseOptionContainer()
	{
		return '</div>';
	}

	function renderOpenOption(array $options)
	{
		$options['rowAttrs'] = !empty($options['rowAttrs']) ? $options['rowAttrs'] : array();

		$html = '<div class="form-group" '. implode(' ', $options['rowAttrs']) .'>';
		$html .= "<label class=\"col-lg-3 col-sm-3 control-label\">{$options['label']}";

		if (!empty($options['image_hint']))
		{
			$html .= '<img src="view/kuler/image/icon/q.png" image-modal image="'. $options['image_hint'] .'" />';
		}

		$html .= '</label>';

		return $html;
	}

	function renderCloseOption(array $options)
	{
		return '</div>';
	}

	function renderOption(array $options)
	{
		$html = renderOpenOption($options);
		$html .= renderByType($options);
		$html .= renderCloseOption($options);

		return $html;
	}

	function renderStyleOption(array $options)
	{
		$html = '<div class="form-group">';
		$html .= "<label>{$options['label']}</label>";
		$html .= renderByType($options);
		$html .= '</div>';

		return $html;
	}

	function renderByType(array $options)
	{
		$html = '';

		switch ($options['type'])
		{
			case 'input':
				$html .= renderInput($options);
				break;
			case 'select':
				$html .= renderSelect($options);
				break;
			case 'switch':
				$html .= renderSwitch($options);
				break;
			case 'select_group':
				$html .= renderSelectGroup($options);
				break;
			case 'color':
				$html .= renderColor($options);
				break;
			case 'editor':
				$html .= renderEditor($options);
				break;
			case 'multilingual_input':
				$html .= renderMultilingualInput($options);
				break;
			case 'style_image_selector':
				$html .= renderStyleImageSelector($options);
				break;
			case 'style_font':
				$html .= renderStyleFont($options);
				break;
			case 'style_pattern':
				$html .= renderStylePatterns($options);
				break;
			case 'textarea':
				$html .= renderTextArea($options);
				break;
			case 'html':
				$html .= $options['html'];
				break;
		}

		return $html;
	}

	function renderOpenInput(array $options)
	{
		$options['column'] = !empty($options['column']) ? $options['column'] : 2;

		return '<div class="col-sm-'. $options['column'] .'">';
	}

	function renderCloseInput(array $options)
	{
		$html = '';

		if (!empty($options['hint']))
		{
			$html .= "<p class=\"help-block\">{$options['hint']}</p>";
		}

		if (!empty($options['hint_out']))
		{
			$html = '</div>' . $html;
		}
		else
		{
			$html = $html . '</div>';
		}

		return $html;
	}

	function renderInput(array $options)
	{
		if (empty($options['format']))
		{
			$options['format'] = 'text';
		}

		if (!isset($options['inputAttrs']))
		{
			$options['inputAttrs'] = '';
		}

		$html = renderOpenInput($options);
		$html .= "<input type=\"{$options['format']}\" class=\"form-control\" ng-model=\"options.{$options['name']}\" {$options['inputAttrs']}/>";
		$html .= renderCloseInput($options);

		return $html;
	}

	function renderSelect(array $option)
	{
		if (!isset($option['inputAttrs']))
		{
			$option['inputAttrs'] = '';
		}

		$html = renderOpenInput($option);
		$html .= "<select class=\"form-control\" name=\"{$option['name']}\" ng-model=\"options.{$option['name']}\" {$option['inputAttrs']}>";

		if (!empty($option['option_html']))
		{
			$html = $option['option_html'];
		}
		else
		{
			foreach ($option['options'] as $value => $label)
			{
				$html .= "<option value=\"$value\">$label</option>";
			}
		}

		$html .= '</select>';
		$html .= renderCloseInput($option);

		return $html;
	}

	function renderSwitch(array $options)
	{
		$html = renderOpenInput($options);
		$html .= "<div><switch input=\"options.{$options['name']}\" /></div>";
		$html .= renderCloseInput($options);

		return $html;
	}

	function renderSelectGroup(array $options)
	{
		$name = $options['format'] == 'radio' ? $options['name'] : $options['name'] . '[]';

		$html = '<div class="btn-group" data-toggle="buttons">';

		foreach ($options['options'] as $value => $label)
		{
			$html .= '<label class="btn btn-default">';
			$html .= "<input type=\"radio\" name=\"$name\" ng-model=\"options.{$options['name']}\" value=\"$value\" id=\"". ($name . '_' . $value) ."\" />$label";
			$html .= '</label>';
		}

		$html .= '</div>';

		return $html;
	}

	function renderEditor(array $options)
	{
		if (!isset($options))
		{
			$options['wrapper'] = false;
		}

		$html = '';

		if (!empty($options['wrapper']))
		{
			$html .= renderOpenInput($options);
		}

		$parent_input = preg_replace('#\[[\'"]\w+[\'"]\]$#', '', $options['name']);

		if ($parent_input == $options['name']) {
			$parent_input = '__temp';
		}

		$html .= "<textarea editor ng-model=\"options.{$options['name']}\" ng-init=\"options.{$parent_input} = options.{$parent_input} ? options.{$parent_input} : {}\" class=\"form-control\"></textarea>";

		if (!empty($options['wrapper']))
		{
			$html .= renderCloseInput($options);
		}

		return $html;
	}

	function renderTextArea(array $options)
	{
		$options['cols'] = !empty($options['cols']) ? $options['cols'] : 30;
		$options['rows'] = !empty($options['rows']) ? $options['rows'] : 10;

		$options['column'] = 6;

		$html = renderOpenInput($options);
		$html .= "<textarea name=\"{$options['name']}\" ng-model=\"options.{$options['name']}\" class=\"form-control\" cols=\"{$options['cols']}\" rows=\"{$options['rows']}\"></textarea>";
		$html .= renderCloseInput($options);

		return $html;
	}

	function renderMultilingualInput(array $options)
	{
		$html = renderOpenInput($options);


		$html .= "<div><multilingual-input languages=\"languages\" input=\"options.{$options['name']}\" /></div>";

		$html .= renderCloseInput($options);

		return $html;
	}

	function renderColor(array $options)
	{
		$options['format'] = !empty($options['format']) ? $options['format'] : 'hex';
		$format_attr = ' data-color-format="' . $options['format'] . '"';

		$options['column'] = $options['format'] == 'hex' ? 1 : 2;

		$html = '';

		if (!empty($options['wrapper']))
		{
			$html .= renderOpenInput($options);
		}

		$html .= "<input type=\"text\" color-picker name=\"{$options['name']}\" ng-model=\"options.{$options['name']}\" class=\"form-control color-picker-input\"$format_attr />";

		if (!empty($options['wrapper']))
		{
			$html .= renderCloseInput($options);
		}

		return $html;
	}

	function renderStyleImageSelector(array $options)
	{
		$html = "<style-image-selector image=\"options.{$options['name']}\" />";

		return $html;
	}

	function renderStyleFont(array $options)
	{
		static $styles = array('font_family', 'font_weight', 'font_size', 'font_style', 'text_transform');

		foreach ($styles as $style)
		{
			if (!isset($options[$style]))
			{
				$options[$style] = 1;
			}
		}

		$html = "<style-font fonts=\"fonts\" font=\"options.{$options['name']}\" watching-font=\"style.fontFamily\" font-family=\"{$options['font_family']}\" font-weight=\"{$options['font_weight']}\" font-size=\"{$options['font_size']}\" font-style=\"{$options['font_style']}\" text-transform=\"{$options['text_transform']}\" />";

		return $html;
	}

	function renderStylePatterns(array $options)
	{
		$html = "<style-pattern pattern=\"options.{$options['name']}\" source-patterns=\"source_patterns\" />";

		return $html;
	}
?>