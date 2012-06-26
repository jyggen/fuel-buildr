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

class Buildr
{

	public static $installed = array();

	public $arguments, $content, $lint, $output_path, $source_path;


	/**
	 * Gets a new instance of a Buildr class.
	 *
	 * @param   string  driver name
	 * @param   array   asset array or array of asset arrays.
	 * @return  Buildr_<driver>
	 */
	public static function forge($driver, $file) {

		$class = \Inflector::words_to_upper('Buildr_'.$driver);

		// Make sure that the driver is functional.
		if(!in_array($class, static::$installed) && !$class::is_installed()) {
			throw new \Exception($class.' isn\'t properly installed.');
		}

		return new $class($file);

	}

	/**
	 * Check the configuration for a driver mapped to the extension.
	 *
	 * @param   string 	file extension
	 * @return  string
	 */
	public static function get_instance_by_driver($extension) {

		$extensions = \Config::get('buildr.extensions');

		return (array_key_exists($extension, $extensions)) ? $extensions[$extension] : 'default';

	}

	public static function generate_links($link_type) {

		$assets       = \Config::get('buildr.assets');
		$files        = array();
		$combine_mode = \Config::get('buildr.combine_mode');
		$public_url   = \Config::get('buildr.public_url');

		// Loop through the asset types.
		foreach($assets as $type => $settings) {
			foreach($settings['groups'] as $group => $assets) {
				foreach($assets as $asset) {

					$extension    = pathinfo($asset, PATHINFO_EXTENSION);
					$filename     = substr($asset, 0, -(strlen($extension)+1));
					$ext          = (array_key_exists('ext', $settings)) ? $settings['ext'] : $extension;
					$output_path  = $type.'/';
					$output_path .= (array_key_exists('ext', $settings)) ? $filename.'.'.$settings['ext'] : $asset;

					if($ext == $link_type) {

						switch($combine_mode) {
							case 'type':
								$files[$public_url.$type.'/'.md5($type).'.'.$ext] = $ext;
								break;
							case 'group':
								$files[$public_url.$type.'/'.md5($type.$group).'.'.$ext] = $ext;
								break;
							case 'asset':
								$files[$public_url.$output_path] = $ext;
								break;
							default:
								throw new \Exception('Unknown combine mode "'.$compress_mode.'"');
								break;
						}

					}

				}
			}
		}

		$output = '';
		foreach($files as $file => $ext) {

			$output .= call_user_func(array('Asset', $ext), \Config::get('buildr.url').$file).CRLF;

		}

		return $output;

	}

	/**
	 * Check if the called class is installed correctly.
	 *
	 * @return  boolean
	 */
	protected static function is_installed() {

		// Quick hack to get current class without namespace.
		static::$installed[] = str_replace(__NAMESPACE__.'\\', '', get_called_class());
		return true;

	}

	/**
	 * Check if a command is executable in the shell.
	 *
	 * @param   string  name of required command
	 * @return  boolean
	 */
	protected static function require_command($cmd) {

		if(\Cli::is_windows()) {

			exec('where '.escapeshellarg($cmd).' 2> NUL', $output);
			return (empty($output)) ? false : true;

		} else {

			exec(' command -v '.escapeshellarg($cmd).' 2> /dev/null', $output);
			return (empty($output)) ? false : true;

		}

	}

	/**
	 * Execute a command on the system.
	 *
	 * @param   string  command to execute
	 * @return  array
	 */
	protected static function execute_command($cmd) {

		$stdout         = array();
		$stderr         = array();
	    $outfile        = tempnam(APPPATH.'tmp', 'cmd');
	    $errfile        = tempnam(APPPATH.'tmp', 'cmd');
    	$descriptorspec = array(0 => array("pipe", "r"), 1 => array("file", $outfile, "w"), 2 => array("file", $errfile, "w"));
    	$proc           = proc_open($cmd, $descriptorspec, $pipes);

    	if(!is_resource($proc)) { return 255; }

    	fclose($pipes[0]);    //Don't really want to give any input

	    $exit   = proc_close($proc);
	    $stdout = file($outfile);
	    $stderr = file($errfile);

	    unlink($outfile);
	    unlink($errfile);

	    if(!empty($stderr)) {

	    	$error  = $cmd;
			$error .= CRLF;

			foreach($stderr as $row) {
				$error .= $row.CRLF;
			}

			$error .= CRLF;

	    	throw new \Exception($error.'Command failed to execute, check output above for more information', E_USER_ERROR);

	    }

    	return $stdout;

	}

	public function __construct($asset) {

		foreach($asset as $key => $value) {

			$this->$key = $value;

		}

	}

}