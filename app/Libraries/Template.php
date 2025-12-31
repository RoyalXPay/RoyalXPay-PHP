<?php

namespace App\Libraries;

class Template
{
	var $template_data = array();

	public	function set($content_area, $value)
	{
		$this->template_data[$content_area] = $value;
	}

	public	function render($template = '', $name = '', $view = '', $view_data = array(), $return = FALSE)
	{
		$this->set($name, view($view, $view_data));

		echo view($template, $this->template_data);
	}
}
