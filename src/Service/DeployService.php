<?php
namespace Guttmann\NautCli\Service;

use GuzzleHttp\Client;

class DeployService
{

    public function deploy(Client $client, $instance, $environment, $branch)
    {
        $payload = json_encode([
            'ref_type' => 'branch',
            'ref' => $branch,
            'bypass_and_start' => true
        ]);

        $response = $client->request(
            'POST',
            '/naut/project/' . $instance . '/environment/' . $environment . '/deploys',
            [
                'body' => $payload
            ]
        );

        $responseData = json_decode($response->getBody()->getContents(), true);

        return str_replace('deploys/', 'deploys/log/', $responseData['data']['links']['self']);
    }

}
