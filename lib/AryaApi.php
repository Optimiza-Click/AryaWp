<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 24/09/18
 * Time: 9:54
 */

namespace Blogging;

use WP_REST_Request;

class AryaApi
{
    public static function registerEndpoints()
    {
        register_rest_route('arya/v1', '/posts/', [
            'methods' => 'POST',
            'callback' => [self::class, 'createPost'],
        ]);

        register_rest_route('arya/v1',  '/posts/image', [
            'methods' => 'POST',
            'callback' => [self::class, 'createImageByUrl'],
        ]);
    }

    public static function createImageByUrl(WP_REST_Request $request) {
        $image_url = $request->get_param('image_url');
        $image = AryaWp::createImageFromRemoteUrl($image_url);

        if(!$image) {
            return ['result' => false, 'error' => $image];
        }

        $attachment = [
            'guid' => $image->url,
            'post_title' => basename($image->file),
            'post_mime_type' => $image->type,
        ];

        $id = wp_insert_attachment( $attachment, $image->file, 0);

        if(!$id) {
            return ['result' => false, 'error' => $id];
        }

        //i hate wordpress, i'm hating wordpress and i will hate wordpress :)
        if (!function_exists('wp_generate_attachment_metadata')) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }

        update_post_meta($id, '_wp_attachment_metadata', wp_generate_attachment_metadata($id, $image->file));

        return ['result' => true, 'id' => $id];
    }

    public static function createPost(WP_REST_Request $request) {

        $post = (object) $request->get_body_params();

        if(empty((array) $post)) {
            return ['result' => false, 'error' => 'Complete fields first'];
        }

        if(!AryaWp::isAllowToCreatePost($post->auth)) {
            return ['result' => false, 'error' => 'The JWT auth code isn\'t correct'];
        }

        $id = wp_insert_post([
            'post_title' => $post->title,
            'post_excerpt' => $post->excerpt,
            'post_status' => $post->status,
            'post_content' => $post->content,
        ]);

        if(!$id) {
            return ['result' => false, 'error' => $id];
        }

        $imageId = set_post_thumbnail( $id,  $post->image_id);

        if(!$imageId) {
            return ['result' => false, 'error' => $imageId];
        }

        return ['result' => true, 'id' => $id, 'url' => get_permalink($id)];
    }
}