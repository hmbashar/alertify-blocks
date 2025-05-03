<?php
// This file is generated. Do not modify it manually.
return array(
	'blocks' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'alertify/blocks',
		'version' => '0.1.0',
		'title' => 'notice',
		'category' => 'widgets',
		'icon' => 'smiley',
		'description' => 'Example block scaffolded with Create Block tool.',
		'example' => array(
			
		),
		'supports' => array(
			'html' => false
		),
		'textdomain' => 'blocks',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php',
		'viewScript' => 'file:./view.js'
	),
	'notice-box' => array(
		'apiVersion' => 3,
		'name' => 'alertify/notice-box',
		'title' => 'Alertify Notice Box',
		'category' => 'widgets',
		'icon' => 'warning',
		'description' => 'Add beautiful notice/alert boxes: info, success, warning, and error.',
		'attributes' => array(
			'content' => array(
				'type' => 'string',
				'source' => 'html',
				'selector' => '.alertify-box-content'
			),
			'noticeType' => array(
				'type' => 'string',
				'default' => 'info'
			)
		),
		'supports' => array(
			'html' => false,
			'color' => array(
				'background' => true,
				'text' => true
			),
			'spacing' => true,
			'typography' => true
		),
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	)
);
