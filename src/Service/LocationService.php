<?php

namespace App\Service;

class LocationService
{
    public static function getIpRegionByIp(string $ip): string
    {
        $key = getenv('IPSTACK_ACCESS_KEY');
        if (empty($ip) || !$key) {
            return json_encode([]);
        }
        $baseUrl = "https://api.ipstack.com/"; // free enroll. free use. https://ipwhois.io/documentation can be use instead
        //https://rapidapi.com/whoisapi/api/ip-geolocation/ => free and low latency
        $fullUrl = $baseUrl . rawurlencode($ip) . "?access_key=" . rawurlencode($key);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($result, true);
        return "{$data['city']} - {$data['region_name']} ({$data['country_name']}) ";
    }

    public static function getIpRegionByIpv2(string $ip): string
    {
        $key = getenv('RAPIDAPI_KEY');
        if (empty($ip) || !$key) {
            return json_encode([]);
        }
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://whoisapi-ip-geolocation-v1.p.rapidapi.com/api/v1?ipAddress=$ip",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "X-RapidAPI-Host: whoisapi-ip-geolocation-v1.p.rapidapi.com",
                "X-RapidAPI-Key: " . $key
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $ipData = json_encode([]);
        } else {
            $data = json_decode($response, true);
            $ipData = "{$data['city']} - {$data['region']} ({$data['country']}) ";
        }
        return $ipData;
    }
}
