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

Autoloader::add_core_namespace('Buildr');

Autoloader::add_classes(array(
    'Buildr\\Buildr'            => __DIR__.'/classes/buildr.php',
    'Buildr\\Buildr_Handlebars' => __DIR__.'/classes/buildr/handlebars.php',
    'Buildr\\Buildr_Javascript' => __DIR__.'/classes/buildr/javascript.php',
    'Buildr\\Buildr_Stylesheet' => __DIR__.'/classes/buildr/stylesheet.php',
));

Config::load('buildr', true);