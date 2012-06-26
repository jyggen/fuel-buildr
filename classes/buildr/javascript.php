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

class Buildr_Javascript extends Buildr
{

	public static function minify_code($assets, $output) {

		$content = '';
		foreach($assets as $asset) {
			$content .= $asset->content;
		}

		file_put_contents($output, $content);
		static::execute_command('uglifyjs --overwrite -nc '.escapeshellarg($output));

	}

	public static function beautify_code($assets, $output) {

		$content = '';
		foreach($assets as $asset) {
			$content .= $asset->content;
		}

		file_put_contents($output, $content);
		static::execute_command('uglifyjs --overwrite -b '.escapeshellarg($output));

	}

	public static function is_installed() {

		if(!static::require_command('jshint') || !static::require_command('uglifyjs')) {

			return false;

		} else return parent::is_installed();

	}

	public function lint() {

		$command = 'jshint '.escapeshellarg($this->source_path);
		$output  = $this->execute_command($command);

		if(!empty($output)) {

			// Strip redundant output.
			array_pop($output);
			array_pop($output);

			$error = CRLF;
			foreach($output as $row) {
				$error .= $row.CRLF;
			}
			$error .= CRLF;

			throw new \Exception($error.basename($this->source_path).' is invalid, see output above.');

		}

	}

}