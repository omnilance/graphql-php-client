<?php

namespace Omnilance\GraphQL;

use Omnilance\GraphQL\Exceptions\GraphQLInvalidResponse;
use Omnilance\GraphQL\Exceptions\GraphQLMissingData;

class Client
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var array
     */
    protected $options;

    /**
     * Client constructor.
     *
     * @param string $url
     */
    public function __construct($token)
    {
        $this->token = $token;
        $this->url = "https://api.omnilance.com/v3.0/";
        $this->options = [
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => 1
        ];
    }

    /**
     * Set the URL to query against
     *
     * @param string $url
     */
    public function setHost($url)
    {
        $this->url = $url;
    }

    /**
     * Make a GraphQL Request and get the raw response.
     *
     * @param string $query
     * @param array $variables
     * @param array $headers
     *
     * @return mixed
     */
    private function request($query, $variables, $headers)
    {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['query' => $query, 'variables' => $variables]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($headers, [
            "X-Token: {$this->token}",
            "Content-Type: application/json;charset=utf-8"
        ]));
        $output = curl_exec($ch);

        curl_close($ch);

        return $output;
    }

    /**
     * Make a GraphQL Request and get the response body in JSON form.
     *
     * @param string $query
     * @param array $variables
     * @param array $headers
     * @param bool $assoc
     *
     * @return mixed
     *
     * @throws GraphQLInvalidResponse
     * @throws GraphQLMissingData
     */
    public function json($query, $variables = [], $headers = [], $assoc = false)
    {
        $response = $this->request($query, $variables, $headers);

        $responseJson = json_decode($response, $assoc);

        if ($responseJson === null) {
            throw new GraphQLInvalidResponse('GraphQL did not provide a valid JSON response. Please make sure you are pointing at the correct URL.');
        } else if (!isset($responseJson->data) && $responseJson->data != null) {
            throw new GraphQLMissingData('There was an error with the GraphQL response, no data key was found.');
        }

        return $responseJson;
    }

    /**
     * Make a GraphQL Request and get the guzzle response .
     *
     * @param string $query
     * @param array $variables
     * @param array $headers
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function response($query, $variables = [], $headers = [])
    {
        $response = $this->json($query, $variables, $headers);

        return new Response($response);
    }
}
