<?php
namespace JSONDB\lib;
use JSONDB\lib\CURL;
use JSONDB\lib\json;
class connect{
    public string $server_type;
    public string $address;
    public int $port=443;
    public int $timeout=5;
    public array $proxy=[];
    public function __construct(string $server_type,string $address,int $port=443,int $timeout=5,array $proxy=[])
    {
        switch($server_type):
            case 'hv':
                $curl = new CURL($address,$port);
                $curl->addurl('/system/status/embed');
                $curl->execute();
                $result=json::_in($response=$curl->response);
                if($result->success){
                    $this->server_type=$server_type;
                    $_ENV['JSONDB_server_type']=$server_type;
                    $this->address=$address;
                    $_ENV['JSONDB_address']=$address;
                    $this->port=$port;
                    $_ENV['JSONDB_port']=$port;
                    $this->timeout=$timeout;
                    $_ENV['JSONDB_timeout']=$timeout;
                    $this->proxy=$proxy;
                    $_ENV['JSONDB_proxy']=$proxy;
                    $this->stats_dc=$result->data->dataCenter;
                    $this->stats_loadavrg=$result->data->loadAverage;
                    $this->stats_fspace=$result->data->freeSpace;
                    $this->stats_tspace=$result->data->totalSpace;
                    $this->stats_uspace=$result->data->usedSpace;
                    $this->respond_time=$result->result->timeTaken;
                    return [
                        'result'=>'ok',
                        'request_id'=>$result->requestId,
                        'data'=>$result->data,
                        'time'=>$result->time
                    ];
                }else{
                    if($result->error)
                        return [
                            'result'=>'error',
                            'request_id'=>$result->requestId,
                            'error'=>$result->result->code,
                            'msg'=>$result->result->message,
                            'while'=>$result->result->while,
                            'timeTaken'=>$result->result->timeTaken,
                            'fix'=>$result->result->fix,
                            'time'=>$result->time
                        ];
                    else
                        return [
                            'result'=>'unknown_error',
                            'request_id'=>$result->requestId,
                            'response'=>$response,
                            'msg'=>'valid jsondb server response not found',
                            'fix'=>'may be invalid server address used or incorrect server type',
                            'time'=>time()
                        ];
                }
            break;
            case 'sv':
                return [
                    'result'=>'lib_error',
                    'msg'=>'not available for update',
                    'fix'=>'will be available for v2',
                    'time'=>time()
                ];
            break;
            default:
                return [
                    'result'=>'lib_error',
                    'msg'=>'invalid "server_type" parameter value detected',
                    'fix'=>'fill this parameter with "hv" or "sv"',
                    'time'=>time()
                ];
            break;
        endswitch;
    }
    public function send(string $section,string $action,array $params=[],string $method='POST',array $header=[])
    {
        switch($this->server_type):
            case 'hv':
                $curl = new CURL($this->address,$this->port);
                if(!empty($this->proxy))$curl->set_proxy($this->proxy['address'],$this->proxy['port'],$this->proxy['type']);
                $params['action']=$action;
                $curl->addurl('/'.$section);
                $curl->set_method($method,$params);
                if(!empty($header))$curl->set_header($header);
                $curl->execute();
                $result=json::_in($response=$curl->response);
                if(@$result->success){
                    $this->response=$response;
                    $this->response_code=$curl->httpcode;
                    if($this->response_code==200){
                        return json::_in($response,true);
                    }else{
                        return [
                            'success'=>false,
                            'result'=>'http_error',
                            'code'=>$this->response_code,
                            'response'=>$response,
                            'time'=>time()
                        ];
                    }
                }else{
                    if(@$result->error)
                        return json::_in($response,true);
                    else
                        return [
                            'success'=>false,
                            'result'=>'unknown_error',
                            'request_id'=>@$result->requestId,
                            'response'=>$response,
                            'msg'=>'valid jsondb server response not found',
                            'fix'=>'may be invalid server address used or incorrect server type',
                            'time'=>time()
                        ];
                }
            break;
            case 'sv':
                return [
                    'success'=>false,
                    'result'=>'lib_error',
                    'msg'=>'not available for update',
                    'fix'=>'check for update from https://github/es-taheri/jsondb-LV',
                    'time'=>time()
                ];
            break;
            default:
                return [
                    'success'=>false,
                    'result'=>'lib_error',
                    'msg'=>'invalid "server_type" parameter value detected',
                    'fix'=>'must fill this parameter with "hv" or "sv"',
                    'time'=>time()
                ];
            break;
        endswitch;
    }
    public function ping()
    {
        $extra=explode('/',$this->address)[3];
        $host=str_replace(["/$extra",'https://'],['',''],$this->address);
        $st=microtime(true);
        fsockopen("ssl://$host",$this->port);
        $et=microtime(true);
        $ping=floor(($et-$st)*1000);
        return $ping;
    }
}