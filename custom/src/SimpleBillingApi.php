<?php

/**
 * Class SimpleBillingAPI
 *
 * API Class used to make curl rest requests to simple billing system
 * Supported Methods includes
 * Get
 * Post
 * Put
 * Delete
 */
class SimpleBillingAPI{

    /** @var   */
    private $apiURL;

    /** @var   */
    private $token;


    /**
     * SimpleBillingAPI constructor.
     * @param $url
     * @param $token
     */
    public function __construct($url, $token)
    {
        $this->apiURL = $url;
        $this->token = $token;

    }

    /**
     * @param $method
     * @param $url
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function exec($method, $url, $data = array()) {
        $curl = curl_init();
        $url = $this->apiURL. $url;

        switch($method) {
            case 'GET':
                if(strrpos($url, "?") === FALSE) {
                    $url .= '?' . http_build_query($data);
                }
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, TRUE);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'PUT':
            case 'DELETE':
            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, strtoupper($method)); // method
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data)); // body
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Content-Type: application/json',
                "X-Auth-Token: " . $this->token
            )
        );
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);

        // Exec curl request
        $response = curl_exec($curl);

        if(curl_error($curl)) {
            throw new Exception(curl_error($curl));
        }else{
            $info = curl_getinfo($curl);
            curl_close($curl);

            return  array(
                'status' => $info['http_code'],
                'data' => json_decode(substr($response, $info['header_size']))
            );
        }
    }

    /**
     * @param $url
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function get($url, $data = array()) {
        $response = $this->exec("GET", $url, $data);

        if($response['status'] == 200){
            return $response;
        }else{
            throw new Exception($response['data']->message, $response['status']);
        }
    }

    /**
     * @param $url
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function post($url, $data = array()) {
        $response =  $this->exec("POST", $url, $data);

        if($response['status'] == 200){
            return $response;
        }else{
            throw new Exception($response['data']->message, $response['status']);
        }
    }

    /**
     * @param $url
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function put($url, $data = array()) {
        $response = $this->exec("PUT", $url, $data);

        if($response['status'] == 200){
            return $response;
        }else{
            throw new Exception($response['data']->message, $response['status']);
        }
    }

    /**
     * @param $url
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function delete($url, $data = array()) {
        $response =  $this->exec("DELETE", $url, $data);

        if($response['status'] == 200){
            return $response;
        }else{
            throw new Exception($response['data']->message, $response['status']);
        }
    }
}