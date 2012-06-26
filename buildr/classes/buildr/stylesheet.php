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

class Buildr_Stylesheet extends Buildr
{

	public static function minify_code($assets, $output) {

		$files = array();

		foreach($assets as $asset) {
			$files[] = escapeshellarg($asset->source_path);
		}

		krsort($files);
		$files = implode(' ', $files);

		static::execute_command('recess '.$files.' --compress > '.escapeshellarg($output));

	}

	public static function beautify_code($assets, $output) {

		$files = array();

		foreach($assets as $asset) {
			$files[] = escapeshellarg($asset->source_path);
		}

		krsort($files);
		$files = implode(' ', $files);

		static::execute_command('recess '.$files.' --compile > '.escapeshellarg($output));

	}

	public static function save_code($assets, $output) {

		static::beautify_code($assets, $output);

	}

	public static function is_installed() {

		if(!static::require_command('recess')) {

			return false;

		} else return parent::is_installed();

	}

	public function lint() {

		$command = 'recess '.escapeshellarg($this->source_path).' --noOverqualifying false';
		$output  = static::execute_command($command);

		if(count($output) == 4) {

			throw new \Exception($output[3].CRLF.basename($this->source_path).' is invalid, see output above.');

		} else {

			$status  = trim(substr($output[4], 13));

			if($status != 'Perfect!') {

				array_shift($output);
	            array_shift($output);
	            array_shift($output);
	            array_shift($output);
	            array_shift($output);
	            array_shift($output);
	            array_shift($output);

				$error = CRLF;
				foreach($output as $row) {
					$error .= $row.CRLF;
				}
				$error .= CRLF;

				throw new \Exception($error.basename($this->source_path).' is invalid, see output above.');

			}

		}

	}

}