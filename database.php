<?php
namespace JSONDB\request;
use JSONDB\lib\connect;
use JSONDB\lib\json;

class database{
    public string $output='array';
    public function __construct(string $output='array')
    {
        $this->output=$output;
        $this->session_id=$_ENV['JSONDB_session_id'];
        $this->connection=new connect(
            $_ENV['JSONDB_server_type'],
            $_ENV['JSONDB_address'],
            $_ENV['JSONDB_port'],
            $_ENV['JSONDB_timeout'],
            $_ENV['JSONDB_proxy'],
        );
    }
    public function select(string $name)
    {
        $result=$this->connection->send('database','select',[
            'database'=>$name,
        ],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    public function build(string $name,int $maxsize,int $maxbase,array $users=null,string|null $comment=null,bool $encrypt=false,bool $select=false)
    {
        if(is_array($users))$users=json::_out($users);
        $result=$this->connection->send('database','build',[
            'name'=>$name,
            'maxSize'=>$maxsize,
            'maxBase'=>$maxbase,
            'opt'=>json::_out([
                'users'=>$users,
                'comment'=>$comment,
                'encrypt'=>$encrypt,
                'select'=>$select    
            ])
        ],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    public function modify(string|array $what,string|array $set)
    {
        if(is_array($what))$what=json::_out($what);
        if(is_array($set))$set=json::_out($set);
        $result=$this->connection->send('database','modify',[
            'what'=>$what,
            'set'=>$set,
        ],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    public function obtain(string|array $what)
    {
        if(is_array($what))$what=json::_out($what);
        $result=$this->connection->send('database','obtain',[
            'what'=>$what,
        ],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    public function delete()
    {
        $result=$this->connection->send('database','delete',[],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    public function clean()
    {
        $result=$this->connection->send('database','clean',[],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    public function export(bool $save_file=false,string $path_to_save=null)
    {
        $result=$this->connection->send('database','export',[],'POST',['x-s-auth'=>$this->session_id]);
        $fname=explode('/',$result['data']['dlLink']);
        $fname=$fname[count($fname)-1];
        if($save_file)file_put_contents($path_to_save.'/'.$fname,$result['data']['backup']);
        return self::output($result,$this->output);
    }
    public function import(string $source)
    {
        if(json::_is($source)){
            $result=$this->connection->send('database','import',[
                'source'=>$source,
            ],'POST',['x-s-auth'=>$this->session_id]);
            return self::output($result,$this->output);
        }else{
            return self::output([
                'result'=>'lib_error',
                'msg'=>'invalid "source" parameter value detected',
                'fix'=>'fill this parameter with json format',
                'time'=>time()
            ],$this->output);
        }
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
}