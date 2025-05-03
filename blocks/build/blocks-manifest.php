<?php
// This file is generated. Do not modify it manually.
return array(
	'dismissible-notice' => array(
		'apiVersion' => 3,
		'name' => 'alertify/dismissible-notice',
		'title' => 'Dismissible Notice',
		'category' => 'widgets',
		'icon' => 'megaphone',
		'description' => 'A notice block that visitors can dismiss (close).',
		'attributes' => array(
			'content' => array(
				'type' => 'string',
				'source' => 'html',
				'selector' => '.alertify-box-content'
			),
			'noticeType' => array(
				'type' => 'string',
				'default' => 'info'
			),
			'dismissDuration' => array(
				'type' => 'number',
				'default' => 24
			),
			'alertId' => array(
				'type' => 'string'
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
		'style' => 'file:./style-index.css',
		'viewScript' => 'file:./dismissible-notice.js'
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
