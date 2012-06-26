<?php
return array(

	/**
	 * name - The name of your application.
	 *
	 * This is only used to make the Buildr Task more "personal"
	 * by printing "Building <name>" in the begining.
	 */
	'name' => 'Application',

	/**
	 * source_path - The path to your source files.
	 *
	 * These are the files you'll make all your changes to.
	 */
	'source_path' => APPPATH.'assets'.DS,

	/**
	 * build_path - The path to the save location of the built assets.
	 *
	 * This is the location where Buildr will save all assets to when
	 * the build process is completed. It's the files in this location
	 * that you should include in your html.
	 */
	'build_path' => DOCROOT.'public'.DS.'assets'.DS,

	'public_url' => Uri::base(false).'assets/',

	/**
	 * combine_mode - How the assets should be combined/concatenated.
	 *
	 * Possible modes/values are:
	 * - type : Every asset in each type will be combined into one file. (recommended for production).
	 * - group: Every asset in each group will be combined into one file.
	 * - asset: Each asset will have its own file (recommended for development).
	 */
	'combine_mode'  => 'type',

	/**
	 * compress_mode - How the assets should be compresed/minified.
	 *
	 * Possible modes/values are:
	 * - beautify: The code will be beautified (if supported by the driver, fallback to normal mode) for
	 *             better reading and debugging (recommended for development).
	 * - minify  : The code be minifed (and optimized if supported) for faster and smaller code (recommended for production).
	 */
	'compress_mode' => 'minify',

	/**
	 * assets - An array containing information about all your assets.
	 *
	 * The first level are your asset types. Types are not locked to a specific
	 * format/language,so "templates" for example can contain both Mustache and
	 * Jade files. Each asset should be located within its type's subfolder in
	 * <assets.source_path> and will be copied to the type's subfolder in
	 * <assets.build_path> during the build process.
	 *
	 * The second level should most importantly contain your asset groups, but can
	 * also contain type-specific settings. Implemented settings are:
	 * - ext   : What extension we should give the assets during build (without the dot).
	 *           If this isn't set Buildr will use the same extension as the source file.
	 * - groups: This should contain arrays with your assets with each key being the
	 *  		 group name.
	 * - lint  : Which assets should we lint (if supported). Set this to false to skip
	 *           the lint process for this asset type, or you can tell us to only lint
	 *           specific groups by using an array instead.
	 *
	 * -- Example --
	 *
	 * 'assets' => array(
	 *	'css' => array(
	 *		'groups' => array(
	 *			'standard' => array('base.less', 'autocomplete.less', 'main.css'),
	 *		),
	 *		'ext'  => '.css',
	 *		'lint' => false,
	 *	),
	 *	'js'  => array(
	 *		'groups' => array(
	 *			'libs'    => array('jquery.js', 'handlebars.runtime.js'),
	 *			'plugins' => array('jquery.scrollTo.js', 'jquery.colorbox.js'),
	 *			'script'  => array('module.js', 'comments.js', 'analytics.js),
	 *		),
	 *		'lint' => array('script'),
	 *	),
 	 *	'tpl' => array(
	 *		'groups' => array(
	 *			'templates' => array('comment.handlebars', 'paging.handlebars'),
	 *		),
	 *		'ext'  => '.js',
	 *		'args' => '-k if',
	 *	),
	 *)
	 */
	'assets' => array(),

	/**
	 * extensions - Maps file extensions to Buildr drivers.
	 *
	 * This shouldn't be modified unless you add your own extensions/drivers. File extensions
	 * that isn't connected to a driver will fallback to the default driver.
	 */
	'extensions' => array(
		'css'        => 'stylesheet',
		'handlebars' => 'handlebars',
		'js'         => 'javascript',
		'less'       => 'stylesheet',
	),

);