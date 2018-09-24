<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 24/09/18
 * Time: 9:54
 */

namespace Blogging;

class AryaUpdater
{
    public static function getInfoAboutPlugin() {
        $args = (object)['slug' => 'arya-wp'];

        $request = ['action' => 'plugin_information', 'timeout' => 15, 'request' => serialize($args)];

        $response = wp_remote_post('http://api.wordpress.org/plugins/info/1.0/', ['body' => $request]);

        $plugin_info = unserialize($response['body']);
    }
}