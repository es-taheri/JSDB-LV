<?php
namespace JSONDB\request;
use JSONDB\lib\connect;
use JSONDB\lib\json;

class datacenter{
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
    public function replace(string|array $object,string|array $value,string|array|null $where_object=null,string|array|null $where_value=null,int $limit=null)
    {
        if(is_array($object))$object=json::_out($object);
        if(is_array($value))$value=json::_out($value);
        $result=$this->connection->send('datacenter','replace',[
            'object'=>$object,
            'value'=>$value,
            'opt'=>json::_out([
                'object'=>$where_object,
                'value'=>$where_value,
                'limit'=>$limit    
            ])
        ],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    public function receive(string|array $object,string|array|null $where_object=null,string|array|null $where_value=null,int $limit=null)
    {
        if(is_array($object))$object=json::_out($object);
        $result=$this->connection->send('datacenter','receive',[
            'object'=>$object,
            'opt'=>json::_out([
                'object'=>$where_object,
                'value'=>$where_value,
                'limit'=>$limit    
            ])
        ],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    public function add(string|array $object,string|array $value)
    {
        if(is_array($object))$object=json::_out($object);
        if(is_array($value))$value=json::_out($value);
        $result=$this->connection->send('datacenter','add',[
            'object'=>$object,
            'value'=>$value,
        ],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    public function delete(int|array|string $id,string|array|null $where_object=null,string|array|null $where_value=null,int $limit=null)
    {
        if(is_array($id))$id=json::_out($id);
        $result=$this->connection->send('datacenter','delete',[
            'id'=>$id,
            'opt'=>json::_out([
                'object'=>$where_object,
                'value'=>$where_value,
                'limit'=>$limit    
            ])
        ],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    public function remove(string|array $object,string|array|null $where_object=null,string|array|null $where_value=null,int $limit=null)
    {
        if(is_array($object))$object=json::_out($object);
        if(is_array($where_object))$where_object=json::_out($where_object);
        if(is_array($where_value))$where_value=json::_out($where_value);
        $result=$this->connection->send('datacenter','remove',[
            'object'=>$object,
            'opt'=>json::_out([
                'object'=>$where_object,
                'value'=>$where_value,
                'limit'=>$limit    
            ])
        ],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
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