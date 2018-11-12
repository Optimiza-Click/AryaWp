<?php

class testAryaApi extends TestRestAryaApi
{

    public function testAryaPingMethod()
    {
        $request = new WP_REST_Request('GET', '/arya/v1/ping');
        $this->loginUser($request);

        $response = $this->server->dispatch($request);

        $this->assertResponseIsOk($response);
        $this->assertEquals(true, ($response->get_data()->result));
    }

    public function testAryaCreatePostWithoutMetaKeys()
    {
        $request = new WP_REST_Request('POST', '/arya/v1/post/create');

        $request->set_query_params($this->getQueryParams());

        $this->loginUser($request);

        $response = $this->server->dispatch($request);

        $post = get_post($response->get_data()->id);
        $this->assertResponseIsOk($response);
        $this->assertEquals($post->post_title, $this->getQueryParams()['title']);
    }

    public function testAryaCreatePostWithMetaKeys()
    {
        $request = new WP_REST_Request('POST', '/arya/v1/post/create');
        $params = $this->getQueryParams();
        $params['metas'] = [
            'seo_keywords' => 'hola, prueba, adios',
            'seo_title' => 'prueba de seo title'
        ];

        $request->set_query_params($params);

        $this->loginUser($request);

        $response = $this->server->dispatch($request);

        $post = get_post($response->get_data()->id);
        $seo_title = get_post_meta($post->ID, 'seo_title', true);

        $this->assertResponseIsOk($response);
        $this->assertEquals($post->post_title, $this->getQueryParams()['title']);
        $this->assertEquals($seo_title, $params['metas']['seo_title']);

    }

    public function testAryaUploadImage()
    {
        $request = new WP_REST_Request('POST', '/arya/v1/post/image');
        $request->set_query_params([
            'image_url' => 'https://www.optimizaclick.com/wp-content/uploads/2016/12/logo_optimizaclick_solocal-300x62.png',
            'title' => 'imagen prueba',
            'description' => 'esto es una prueba'
        ]);

        $this->loginUser($request);

        $response = $this->server->dispatch($request);

        $post = get_post($response->get_data()->id);

        $this->assertResponseIsOk($response);
        $this->assertEquals($post->post_name, 'imagen-prueba');
        $this->assertEquals($post->guid, 'http://example.org/wp-content/uploads/2018/11/logo_optimizaclick_solocal-300x62-5.png');
    }


    public function getQueryParams(): array
    {
        return [
            'title' => 'post prueba',
            'slug' => 'prueba',
            'excerpt' => 'esto es una prueba',
            'status' => 'publish',
            'content' => 'esto es una prueba'
        ];
    }
}
