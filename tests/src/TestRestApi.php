<?php

class TestRestAryaApi extends WP_UnitTestCase {

    const JWT_KEY = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.ImZsYW1pbmdvLXZhbGlkLW9wZXJhdGlvbiI.UD4xE0hrWv112u0b4MHzNHjLw8FfJHIGhNRtA92CcekrCN1qNRpzqaw_7e0rW3JeBeMepucLFbs6mvGCJ7jMQBaniO710VNbzRJZnFvr5-iFzerWeeWn0hqbIJASWfUJId1Fudw4iMfXeNmtjUj_rlpYlE9dsceRxvsg4nbAbyw';

    /**
     * @param $request
     */
    public function loginUser($request): void
    {
        $request->set_param('key', self::JWT_KEY);
    }
    /**
	 * Test REST Server
	 *
	 * @var WP_REST_Server
	 */
	protected $server;

	protected $namespaced_route = '/arya/v1';



	public function tearDown() {
		parent::tearDown();

		global $wp_rest_server;
		$wp_rest_server = null;
	}


	public function setUp() {
		parent::setUp();

		/** @var WP_REST_Server $wp_rest_server */
		global $wp_rest_server;
		$this->server = $wp_rest_server = new \WP_REST_Server;
		do_action( 'rest_api_init' );
	}



	public function assertResponseIsOk($response)
	{
		$this->assertResponseCodeIs( 200, $response);
	}

	public function assertResponseCodeIs($code,$response)
    {
        $this->assertEquals( $code, $response->get_status() );
    }

}