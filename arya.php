<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 23/03/18
 * Time: 11:53
 */

/*
Plugin Name: AryaWp
Plugin URI: https://www.optimizaclick.com/
Description: Plugin to manage the posts via api
Author: David
Version: 1.0
Author URI: https://www.optimizaclick.com/
*/

use Blogging\AryaWp;

if ( file_exists( $composer_autoload = __DIR__ . '/vendor/autoload.php' )) {
    require_once $composer_autoload;
}

new AryaWp();
