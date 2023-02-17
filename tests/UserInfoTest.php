<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

final class UserInfoTest extends TestCase
{
    public function testCanBeReceiveDataFromUsersWithIpRegion(): void
    {
        $graphQLquery = '{"query": "query {  users {  name id ip ip_region } }  "}';
        $uri = 'http://localhost:8080/graphql';

        $response = (new Client)->request('post', $uri, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => $graphQLquery
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        foreach ($data['data']['users'] as $datum) {
            $this->assertNotEmpty($datum['name']);
            $this->assertNotEmpty($datum['ip']);
            $this->assertNotEmpty($datum['ip_region']);
        }
    }

    public function testCanBeReceiveDataFromUserWithIpRegion(): void
    {
        $graphQLquery = '{"query": "query {  user(id: 1) {  name id ip ip_region} }  "}';
        $uri = 'http://localhost:8080/graphql';

        $response = (new Client)->request('post', $uri, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => $graphQLquery
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertNotEmpty($data['data']['user']['name']);
        $this->assertNotEmpty($data['data']['user']['ip']);
        $this->assertNotEmpty($data['data']['user']['ip_region']);

    }

    public function testCantBeReceiveDataFromUsersThenThrowEmptyValue(): void
    {
        $graphQLquery = '{"query": "query {  user (id:12) {  name id ip_region } }  "}';
        $uri = 'http://localhost:8080/graphql';

        $response = (new Client)->request('post', $uri, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => $graphQLquery
        ]);
        $this->assertEquals(
            '{"data":{"user":{"name":null,"id":null,"ip_region":"[]"}}}',
            $response->getBody()->getContents()
        );
    }
}
