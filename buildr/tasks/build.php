<?php
/**
 * Buildr: Build Task for assets in FuelPHP.
 *
 * @package    Buildr
 * @subpackage Buildr
 * @version    1.0
 * @author     Jonas Stendahl <jonas.stendahl@gmail.com>
 * @license    MIT License
 * @copyright  (c) 2012 Jonas Stendahl
 */
namespace Fuel\Tasks;

class Build {

	protected static $assets = array();
	protected static $hr     = '##################################################';

	protected static $env;
	protected static $mode;
	protected static $path  = 'public/assets/';
	protected static $src   = 'fuel/app/assets/';
	protected static $files = array();
	protected static $lint  = true;
	protected static $cache = true;

	public static function run() {

		// Output the header.
		\Cli::write(CRLF.static::$hr);
		\Cli::write('Building '.\Config::get('buildr.name').'...');
		\Cli::write(static::$hr.CRLF);

		// Let's go! :)
		static::get_assets();
		static::run_buildr();

		// Output the footer.
		\Cli::write(CRLF.static::$hr);
		\Cli::write(\Config::get('buildr.name').' successfully built at '.date('h:iA').'.');
		\Cli::write(static::$hr.CRLF);
		exit;

	}

	protected static function get_assets() {

		// Get assets from config.
		$assets       = \Config::get('buildr.assets');
		$combine_mode = \Config::get('buildr.combine_mode');

		// Loop through the asset types.
		foreach($assets as $type => $settings) {

			// Make sure that there's some groups defined.
			if(!array_key_exists('groups', $settings)) {
				static::error('Groups are not defined for type "'.$type.'".');
			}

			// Loop through the groups and the assets in it.
			foreach($settings['groups'] as $group => $assets) {
				foreach($assets as $asset) {

					// Get the path, extension and name of the asset.
					$source_path = \Config::get('buildr.source_path').$type.DS.$asset;
					$extension   = pathinfo($asset, PATHINFO_EXTENSION);
					$filename    = substr($asset, 0, -(strlen($extension)+1));

					// Make sure that the asset exists.
					if(!file_exists($source_path)) {
						static::error('Couldn\'t find asset "'.$source_path.'".');
					}

					// Get output path, and override the extension if requested.
					$output_path  = \Config::get('buildr.build_path').$type.DS;
					$output_path .= (array_key_exists('ext', $settings)) ? $filename.'.'.$settings['ext'] : $asset;

					try {

						$driver   = \Buildr::get_instance_by_driver($extension);
						$instance = \Buildr::forge($driver, array(
							'arguments'   => (array_key_exists('args', $settings)) ? $settings['args'] : null,
							'content'     => \File::read($source_path, true),
							'lint'        => (array_key_exists('lint', $settings) && ($settings['lint'] == false || !in_array($group, $settings['lint']))) ? 0 : 1,
							'output_path' => $output_path,
							'source_path' => $source_path,
						));

						if($instance->lint && method_exists($instance, 'lint')) {
							$instance->lint();
						}

						$path = \Config::get('buildr.build_path').$type.DS;
						$ext  = (array_key_exists('ext', $settings)) ? $settings['ext'] : $extension;

						switch($combine_mode) {
							case 'type':
								static::$assets[$path.md5($type).'.'.$ext][$driver][] = $instance;
								break;
							case 'group':
								static::$assets[$path.md5($type.$group).'.'.$ext][$driver][] = $instance;
								break;
							case 'asset':
								static::$assets[$instance->output_path][$driver][] = $instance;
								break;
							default:
								throw new \Exception('Unknown combine mode "'.$compress_mode.'"');
								break;
						}

					} catch(\Exception $e) { static::error($e->getMessage()); }

				}
			}

		}

		\Cli::write(str_pad('Initializing...', 50).\Cli::color('✔', 'green').' Done');
		\Cli::new_line();

	}

	protected static function run_buildr() {

		try {

			$compress_mode = \Config::get('buildr.compress_mode');

			foreach(static::$assets as $path => $drivers) {
				foreach($drivers as $driver => $assets) {

					$cachekey = 'buildr.'.md5($path);
					$checksum = md5(serialize($assets));

					try { $lastrun = \Cache::get($cachekey); }
					catch (\CacheNotFoundException $e) { $lastrun = null; }

					if($checksum != $lastrun) {

						$dir = pathinfo($path, PATHINFO_DIRNAME);
						if(!file_exists($dir) && !mkdir($dir, 0777, true)) {

							throw new \Exception('Unable to create directory "'.$dir.'"');

						} else {

							$class = \Inflector::words_to_upper('Buildr_'.$driver);
							switch($compress_mode) {
								case 'minify':
									$class::minify_code($assets, $path);
									break;
								case 'beautify':
									$class::beautify_code($assets, $path);
									break;
								default:
									throw new \Exception('Unknown compress mode "'.$compress_mode.'"');
									break;
							}
							\Cache::set($cachekey, $checksum, 1209600);
							\Cli::write(str_pad('Building '.basename($path).'...', 50).\Cli::color('✔', 'green').' Done');

						}

					} else {

						\Cli::write(str_pad('Building '.basename($path).'...', 50).\Cli::color('✔', 'green').' Cache');

					}


				}
			}

		} catch(\Exception $e) { static::error($e->getMessage()); }

	}

	protected static function error($msg) {

		\Cli::error($msg);
		\Cli::new_line();
		exit(1);

	}

}