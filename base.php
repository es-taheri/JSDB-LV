<?php
namespace JSONDB\request;

use JSONDB\lib\connect;
use JSONDB\lib\json;
/**
 * send base section request to jsondb server/host by curl
 * 
 * This Class send asynchronously requests to jsondb server/host base section with "connect" class
 * 
 * @package    JSONDB-LV
 * @license    https://raw.githubusercontent.com/es-taheri/JSONDB-LV/JSONDB/LICENSE  MIT License
 * @link       https://github.com/es-taheri/JSONDB-LV#base-requests
 */
class base{
    public string $output='array';
    /**
     * initializing session id and creating connection to server
     *
     * @param string $output
     */
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
    /**
     * Select a base
     *
     * @param string $name              name of base
     * @return array|json|object        returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#select-base
     */
    public function select(string $name)
    {
        $result=$this->connection->send('base','select',[
            'base'=>$name,
        ],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    /**
     * create a new base
     *
     * @param string $name              name of new base
     * @param integer $maxrecord        limit max records count of base
     * @param string|null $comment      comment of new base
     * @param boolean $encrypt          should base be encrypted
     * @param boolean $select           should base be selected after creation
     * @return array|json|object        returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#create-base
     */
    public function create(string $name,int $maxrecord,string|null $comment=null,bool $encrypt=false,bool $select=false)
    {
        $result=$this->connection->send('base','create',[
            'name'=>$name,
            'maxRecord'=>$maxrecord,
            'opt'=>json::_out([
                'comment'=>$comment,
                'encrypt'=>$encrypt,
                'select'=>$select    
            ])
        ],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    /**
     * modify base setting
     *
     * @param string|array $what        setting you want to update it
     * @param string|array $set         value you want to set to that setting
     * @return array|json|object        returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#modify-base-setting
     */
    public function modify(string|array $what,string|array $set)
    {
        if(is_array($what))$what=json::_out($what);
        if(is_array($set))$set=json::_out($set);
        $result=$this->connection->send('base','modify',[
            'what'=>$what,
            'set'=>$set,
        ],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    /**
     * obtain base setting
     *
     * @param string|array $what        setting you want to obtain it
     * @return array|json|object        returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#obtain-base-setting
     */
    public function obtain(string|array $what)
    {
        if(is_array($what))$what=json::_out($what);
        $result=$this->connection->send('base','obtain',[
            'what'=>$what,
        ],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    /**
     * delete current selected base
     *
     * @return array|json|object        returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#delete-base
     */
    public function delete()
    {
        $result=$this->connection->send('base','delete',[],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    /**
     * clean current selected base
     *
     * @return array|json|object        returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#clean-base
     */
    public function clean()
    {
        $result=$this->connection->send('base','clean',[],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    /**
     * export current selected base
     *
     * @param boolean $save_file        should save exported base file
     * @param string|null $path_to_save path to directory exported base file should save
     * @return array|json|object        returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#export-base
     */
    public function export(bool $save_file=false,string $path_to_save=null)
    {
        $result=$this->connection->send('base','export',[],'POST',['x-s-auth'=>$this->session_id]);
        $fname=explode('/',$result['data']['dlLink']);
        $fname=$fname[count($fname)-1];
        if($save_file)file_put_contents($path_to_save.'/'.$fname,$result['data']['backup']);
        return self::output($result,$this->output);
    }
    /**
     * import a base
     *
     * @param string $source json encoded format exported base
     * @return array|json|object        returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#import-base
     */
    public function import(string $source)
    {
        if(json::_is($source)){
            $result=$this->connection->send('base','import',[
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