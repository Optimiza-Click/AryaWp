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
    public function registerEndpoints()
    {
        register_rest_route('arya/v1', '/post/create', [
            'methods' => 'POST',
            'callback' => [$this, 'createPost'],
        ]);

        register_rest_route('arya/v1',  '/post/image', [
            'methods' => 'POST',
            'callback' => [$this, 'createImageByUrl'],
        ]);

        register_rest_route('arya/v1',  '/ping', [
            'methods' => 'GET',
            'callback' => [$this, 'checkStatus'],
        ]);
    }

    public function createImageByUrl(WP_REST_Request $request)
    {
        $parameters = (object) $request->get_params();

        if(!AryaWp::isAllowToCreatePost($parameters->key)) {
            return (object) ['result' => false, 'error' => 'The JWT auth code isn\'t correct'];
        }

        if(!$parameters->image_url) {
            return (object) ['result' => false, 'error' => 'please send image url in the request'];
        }

        $image = AryaWp::createImageFromRemoteUrl($parameters->image_url);

        if(!$image) {
            return  (object) ['result' => false, 'error' => $image];
        }

        $attachment = [
            'guid' => $image->url,
            'post_title' => $parameters->title ?? basename($image->file),
            'post_content' => $parameters->description ?? null,
            'post_mime_type' => $image->type,
        ];

        $id = wp_insert_attachment( $attachment, $image->file, 0);

        if(!$id) {
            return (object) ['result' => false, 'error' => $id];
        }

        // i'm hating wordpress and i will hate wordpress :)
        if (!function_exists('wp_generate_attachment_metadata')) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }

        update_post_meta($id, '_wp_attachment_metadata', wp_generate_attachment_metadata($id, $image->file));

        return (object) ['result' => true, 'id' => $id];
    }

    public function createPost(WP_REST_Request $request)
    {
        $post = (object) $request->get_params();

        if(!AryaWp::isAllowToCreatePost($post->key)) {
            return (object) ['result' => false, 'error' => 'The JWT auth code isn\'t correct'];
        }

        if(empty((array) $post)) {
            return (object) ['result' => false, 'error' => 'Complete fields first'];
        }

        $id = wp_insert_post([
            'post_title' => $post->title,
            'post_name' => $post->slug,
            'post_excerpt' => $post->excerpt,
            'post_status' => $post->status,
            'post_content' => $post->content,
        ]);

        if(!$id) {
            return (object) ['result' => false, 'error' => $id];
        }

        if(!empty($post->metas)) {
            AryaWp::createCustomFieldsByPostId($post->metas, $id);
        }

        if(isset($post->image_id)) {
            set_post_thumbnail( $id,  $post->image_id);
        }

        return (object) ['result' => true, 'id' => $id, 'url' => get_permalink($id)];
    }

    public function checkStatus(WP_REST_Request $request = null)
    {
        return (object) ['result' => true];
    }
}