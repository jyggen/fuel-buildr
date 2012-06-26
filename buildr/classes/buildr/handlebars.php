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
namespace Buildr;

class Buildr_Handlebars extends Buildr
{

	public static function minify_code($assets, $output) {

		$files = array();

		foreach($assets as $asset) {
			$files[] = escapeshellarg($asset->source_path);
		}

		$files = implode(' ', $files);

		static::execute_command('handlebars '.$files.' -m -f '.escapeshellarg($output));

	}

	public static function beautify_code($assets, $output) {

		$files = array();

		foreach($assets as $asset) {
			$files[] = escapeshellarg($asset->source_path);
		}

		$files = implode(' ', $files);

		static::execute_command('handlebars '.$files.' -f '.escapeshellarg($output));

	}

	public static function is_installed() {

		if(!static::require_command('handlebars')) {

			return false;

		} else return parent::is_installed();

	}

}