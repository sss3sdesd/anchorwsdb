<?php
namespace Livedirector;
use GuzzleHttp\Client;
class Director{
    public static $timeout = 10;
    public static $base = 'http://console.direct.chinanetcenter.com';

    /**
     * 创建导播实例
     * @param $instanceName
     * @param $userId
     * @param $inputSize
     * @param $endTime
     * @param $callBackUrl
     * @param $random
     * @param $sign
     * @return false|\Psr\Http\Message\StreamInterface
     */
    public static function createInstance($instanceName,$userId,$inputSize,$endTime,$random,$callBackUrl,$sign)
    {
        $uri = '/api/create';
        $data = [
            'instanceName' => $instanceName,
            'inputSize' => $inputSize,
            'endTime' => $endTime,
            'userId' => $userId,
            'callBackUrl' => $callBackUrl,
            'random' => $random,
            'sign' => $sign //md5($userId.$secret_key.$random)
        ];
        // Create a client with a base URI
        return self::request($uri, 'POST', $data, []);
    }

    /**
     * 增加输入配置
     * @param $id
     * @param $token
     * @param $pull_address
     * @param $source_index
     * @return false|\Psr\Http\Message\StreamInterface
     */
    public static function input($id,$token,$pull_address,$source_index = 1,$type = '0')
    {
        //输入配置
        $uri = "/v1/{$id}/source/input/{$source_index}";
        $data = [
            "buffertime" => 2,
            "maxbuffertime" => 2,
            "type" => $type,
            "url" => $pull_address
        ];
        $headers = [
            'X-Direct-InstanceId' => $id,
            'X-Direct-Token' => $token
        ];
        return self::request($uri, 'POST', $data, $headers);
    }

    /**
     * 输出配置
     * @param $id
     * @param $token
     * @param $push_address
     * @param string $size
     * @param string $cbr
     * @param int $rate
     * @param int $fps
     * @return false|\Psr\Http\Message\StreamInterface
     */
    public static function output($id,$token,$push_address,$size = "540x960",$cbr="0",$rate=2000,$fps=30){
        $uri = "/v1/{$id}/source/output";
        $data = [
            "cbr" =>$cbr,
            "fps" => $fps,
            "qualityvalue" => "custom",
            "rate" => $rate,
            "size" => $size,
            "urlList" => [$push_address]
        ];
        $headers = [
            'X-Direct-InstanceId' => $id,
            'X-Direct-Token' => $token
        ];
        return self::request($uri, 'POST', $data, $headers);
    }

    /**
     * 增加模板
     * @param $id
     * @param $token
     * @param $data
     * @return false|\Psr\Http\Message\StreamInterface
     */
    public static function createTemplate($id,$token,$data){
        $uri = "/v1/{$id}/template";
        $headers = [
            'X-Direct-InstanceId' => $id,
            'X-Direct-Token' => $token
        ];
        return self::request($uri,'POST',$data,$headers);
    }

    /**
     * 使用自定义模板
     * @param $id
     * @param $token
     * @param $temp_id
     * @param string $audios
     * @return false|\Psr\Http\Message\StreamInterface
     */
    public static function custom($id,$token,$temp_id,$audios = '100,100'){
        $uri = "/v1/{$id}/control/custom";
        $data = [
            "templateId" => $temp_id,
            "audios" => $audios,
        ];
        $headers = [
            'X-Direct-InstanceId' => $id,
            'X-Direct-Token' => $token
        ];
        return self::request($uri,'POST',$data,$headers,'query');
    }

    /**
     * 使用单人模式
     * @param $id
     * @param $token
     * @param int $videoIndex
     * @param int $audio
     * @return false|\Psr\Http\Message\StreamInterface
     */
    public static function single($id,$token,$videoIndex = 1,$audio = 100){
        $uri = "/v1/{$id}/control/single";
        $data = [
            "videoIndex" => $videoIndex,
            "audio" => $audio,
        ];
        $headers = [
            'X-Direct-InstanceId' => $id,
            'X-Direct-Token' => $token
        ];
        return self::request($uri,'POST',$data,$headers,'query');
    }

    /**
     * 停止导播
     * @param $id
     * @param $token
     * @return false|\Psr\Http\Message\StreamInterface
     */
    public static function stop($id,$token){
        $uri = "/v1/{$id}/config/stop";
        $headers = [
            'X-Direct-InstanceId' => $id,
            'X-Direct-Token' => $token
        ];
        return self::request($uri,'POST',[],$headers);
    }

    /**
     * 发送请求的方法
     * @param $uri
     * @param $method
     * @param $data
     * @param array $headers
     * @param string $type
     * @return false|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function request($uri,$method,$data,$headers=[],$type='json'){
        $client = new Client([
            'base_uri' => self::$base,
            'timeout' => self::$timeout
        ]);
        $senddata = [
            'headers' => $headers
        ];
        if(!empty($data)){
            $senddata[$type] = $data;
        }
        try {
            $response = $client->request($method,$uri,$senddata);
        }catch (\Exception $e){
            return false;
        }
        return $response->getBody();
    }
}