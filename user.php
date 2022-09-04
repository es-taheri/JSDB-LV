<?php
namespace JSONDB\request;

use JSONDB\lib\connect;
use JSONDB\lib\CURL;
use JSONDB\lib\json;

class user{
    public string $username;
    public string $password='';
    public string $output='array';
    private const _SSDIR_=__DIR__.'/.sid';
    public function __construct($username,$password,$output='array')
    {
        $this->username=$username;
        $this->password=$password;
        $this->output=$output;
        $this->auto_terminate=false;
        $this->connection=new connect(
            $_ENV['JSONDB_server_type'],
            $_ENV['JSONDB_address'],
            $_ENV['JSONDB_port'],
            $_ENV['JSONDB_timeout'],
            $_ENV['JSONDB_proxy'],
        );
    }
    public function rapid_login(bool $save_session=true)
    {
        $curl=new CURL($_ENV['JSONDB_address'],$_ENV['JSONDB_port']);
        $curl->addurl('/user/login@'.$this->username);
        $curl->set_method('POST',['password'=>$this->password]);
        $curl->execute();
        if(json::_is($curl->response)){
            return self::output(json::_in($curl->response,true),$this->output);
        }else{
            if($save_session):
                file_put_contents(self::_SSDIR_,base64_encode($curl->response.':'.time()+600));
                chmod(self::_SSDIR_,0750);
                $this->auto_terminate=false;
            else:
                $this->auto_terminate=true;
            endif;
            $this->session_id=$curl->response;
            $_ENV['JSONDB_session_id']=$this->session_id;
            return $curl->response;
        }
    }
    public function login(bool $save_session=true)
    {
        $result=$this->connection->send('user','login',['username'=>$this->username,'password'=>$this->password],'POST');
        if(@$result['success']){
            if($save_session):
                file_put_contents(self::_SSDIR_,base64_encode($result['data']['sessionId'].':'.$result['data']['validUntil']));
                chmod(self::_SSDIR_,0750);
                $this->auto_terminate=false;
            else:
                $this->auto_terminate=true;
            endif;
            $this->session_id=$result['data']['sessionId'];
            $_ENV['JSONDB_session_id']=$this->session_id;
            return self::output($result,$this->output);
        }else{
            return self::output($result,$this->output);
        }
    }
    public function continue(string $session_id=null)
    {
        if(empty($session_id))$session_id=self::last_session();
        if($session_id){
            $result=$this->connection->send('user','check',[],'POST',['x-s-auth'=>$session_id]);
            if(@$result['success']){
                $this->session_id=$session_id;
                $_ENV['JSONDB_session_id']=$this->session_id;
                $this->auto_terminate=false;
                return self::output($result,$this->output);
            }else{
                return self::output($result,$this->output);
            }
        }else{
            return self::output([
                'success'=>false,
                'result'=>'lib_error',
                'msg'=>'no valid session found from last login',
                'fix'=>'login again for getting new session from your server. (Each session is only valid for 10 minutes by default)',
                'time'=>time()
            ],$this->output);
        }
    }
    public function terminate()
    {
        $result=$this->connection->send('user','terminate',[],'POST',['x-s-auth'=>@$this->session_id]);
        return self::output($result,$this->output);
    }
    public function update(string|array $what,string|array $set,string $username=null)
    {
        if(is_array($what))$what=json::_out($what);
        if(is_array($set))$set=json::_out($set);
        $params=['what'=>$what,'set'=>$set];
        $params['opt']=json::_out(['username'=>$username]);
        $result=$this->connection->send('user','update',$params,'POST',['x-s-auth'=>@$this->session_id]);
        return self::output($result,$this->output);
    }
    public function get(string|array $what)
    {
        if(is_array($what))$what=json::_out($what);
        $result=$this->connection->send('user','get',['what'=>$what],'POST',['x-s-auth'=>@$this->session_id]);
        return self::output($result,$this->output);
    }
    public function create(string $username,string $password,string $type,bool $active=true)
    {
        $result=$this->connection->send('user','create',['username'=>$username,'password'=>$password,'type'=>$type,'active'=>$active],'POST',['x-s-auth'=>@$this->session_id]);
        return self::output($result,$this->output);
    }
    public function delete(string $username)
    {
        $result=$this->connection->send('user','delete',['username'=>$username],'POST',['x-s-auth'=>@$this->session_id]);
        return self::output($result,$this->output);
    }
    public static function last_session()
    {
        if(file_exists(self::_SSDIR_)){
            $fdata=base64_decode(file_get_contents(self::_SSDIR_));
            $explode=explode(':',$fdata);
            $session_id=$explode[0];
            $valid_time=$explode[1];
            if($valid_time>time()){
                return $session_id;
            }else{
                $connection=new connect(
                    $_ENV['JSONDB_server_type'],
                    $_ENV['JSONDB_address'],
                    $_ENV['JSONDB_port'],
                    $_ENV['JSONDB_timeout'],
                    $_ENV['JSONDB_proxy'],
                );
                $connection->send('user','terminate',[],'POST',['x-s-auth'=>$session_id]);
                return false;
            }
        }else{
            return false;
        }
    }
    private static function clear_session_cache()
    {
        if(file_exists(self::_SSDIR_))unlink(self::_SSDIR_);
    }
    private static function output(array $data,string $output_type='array')
    {
        switch($output_type):
            case 'array':
                return $data;
            break;
            case 'json':
                return json::_out($data);
            break;
            case 'object':
                return json::_in(json::_out($data));
            break;
        endswitch;
    }
    public function __destruct()
    {
        if($this->auto_terminate):
            self::clear_session_cache();
            self::terminate();
        endif;
    }
}
