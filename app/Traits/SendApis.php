<?php
/**
 * Created by PhpStorm.
 * User: seamlabs
 * Date: 3/5/2020
 * Time: 1:57 PM
 */

namespace App\Traits;


trait SendApis
{
    public function getGuzzleRequest($api , $client)
    {
        $timeOut = 30;
        $request = $client->get($api, [
            'timeout' => $timeOut
        ]);

        return $request;
    }

    public function postOutRequest($api,$body , $type , $headers , $client = null)
    {
        if($type == 'body')
            $data = json_encode($body);
        elseif($type == 'form_params')
            $data = $body;

        if(is_null($client))
            $client = new \GuzzleHttp\Client(['headers' => $headers]);

        $response = $client->post($api, [
            $type => $data
        ]);

        return $response;
    }

    public function putGuzzleRequest($api,$body , $type , $headers)
    {
        if($type == 'body')
            $data = json_encode($body);
        elseif($type == 'form_params')
            $data = $body;

        $client = new \GuzzleHttp\Client(['headers' => $headers]);

        $response = $client->put($api, [
            $type => $data
        ]);

        return $response;
    }

    public function deleteGuzzleRequest($api , $headers = [])
    {
        $client = new \GuzzleHttp\Client(['headers' => $headers]);
        $request = $client->delete($api);

        return $request;
    }
}