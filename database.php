<?php
namespace JSONDB\request;

use JSONDB\lib\connect;
use JSONDB\lib\json;
/**
 * send database section request to jsondb server/host by curl
 * 
 * This Class send asynchronously requests to jsondb server/host database section with "connect" class
 * 
 * @package    JSONDB-LV
 * @version    Release: v1.0-beta
 * @license    https://raw.githubusercontent.com/es-taheri/JSONDB-LV/JSONDB/LICENSE  MIT License
 * @link       https://github.com/es-taheri/JSONDB-LV#database-requests
 */
class database{
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
     * Select a database
     *
     * @param string $name              name of database
     * @return array|json|object        returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#select-database
     */
    public function select(string $name)
    {
        $result=$this->connection->send('database','select',[
            'database'=>$name,
        ],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    /**
     * Build a new database
     *
     * @param string $name              name of new database
     * @param integer $maxsize          limit max size of database in kilobyte
     * @param integer $maxbase          limit max base database allowed to have
     * @param array|null $users         an array of users have access too this database
     * @param string|null $comment      comment of new database
     * @param boolean $encrypt          should database be encrypted
     * @param boolean $select           should database be selected after built
     * @return array|json|object        returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#build-database
     */
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
    /**
     * modify database setting
     *
     * @param string|array $what        setting you want to update it
     * @param string|array $set         value you want to set to that setting
     * @return array|json|object        returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#modify-database-setting
     */
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
    /**
     * obtain database setting
     *
     * @param string|array $what        setting you want to obtain it
     * @return array|json|object        returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#obtain-database-setting
     */
    public function obtain(string|array $what)
    {
        if(is_array($what))$what=json::_out($what);
        $result=$this->connection->send('database','obtain',[
            'what'=>$what,
        ],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    /**
     * delete current selected database
     *
     * @return array|json|object        returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#delete-database
     */
    public function delete()
    {
        $result=$this->connection->send('database','delete',[],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    /**
     * clean current selected database
     *
     * @return array|json|object        returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#clean-database
     */
    public function clean()
    {
        $result=$this->connection->send('database','clean',[],'POST',['x-s-auth'=>$this->session_id]);
        return self::output($result,$this->output);
    }
    /**
     * export current selected database
     *
     * @param boolean $save_file        should save exported database file
     * @param string|null $path_to_save path to directory exported database file should save
     * @return array|json|object        returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#export-database
     */
    public function export(bool $save_file=false,string $path_to_save=null)
    {
        $result=$this->connection->send('database','export',[],'POST',['x-s-auth'=>$this->session_id]);
        $fname=explode('/',$result['data']['dlLink']);
        $fname=$fname[count($fname)-1];
        if($save_file)file_put_contents($path_to_save.'/'.$fname,$result['data']['backup']);
        return self::output($result,$this->output);
    }
    /**
     * import a database
     *
     * @param string $source json encoded format exported database
     * @return array|json|object        returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#import-database
     */
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