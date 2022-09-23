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
 * @link       https://github.com/es-taheri/JSONDB-LV#datacenter-requests
 */
class datacenter
{
    public string $output = 'array';
    /**
     * initializing session id and creating connection to server
     *
     * @param string $output
     */
    public function __construct(string $output = 'array')
    {
        $this->output = $output;
        $this->session_id = $_ENV['JSONDB_session_id'];
        $this->connection = new connect(
            $_ENV['JSONDB_server_type'],
            $_ENV['JSONDB_address'],
            $_ENV['JSONDB_port'],
            $_ENV['JSONDB_timeout'],
            $_ENV['JSONDB_proxy'],
        );
    }
    /**
     * replace/add data to record(s)
     *
     * @param string|array $object              name of data you want to add/update to record(s)
     * @param string|array $value               value of data you want to add/update to record(s)
     * @param string|array|null $where_object   filter records data name
     * @param string|array|null $where_value    filter records data value
     * @param integer|null $limit               limit number of records should update
     * @return array|json|object                returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#replace-record
     */
    public function replace(string|array $object, string|array $value, string|array|null $where_object = null, string|array|null $where_value = null, int $limit = null)
    {
        if (is_array($object)) $object = json::_out($object);
        if (is_array($where_object)) $where_object = json::_out($where_object);
        if (is_array($value)) $value = json::_out($value);
        if (is_array($where_value)) $where_value = json::_out($where_value);
        $result = $this->connection->send('datacenter', 'replace', [
            'object' => $object,
            'value' => $value,
            'opt' => json::_out([
                'object' => $where_object,
                'value' => $where_value,
                'limit' => $limit
            ])
        ], 'POST', ['x-s-auth' => $this->session_id]);
        return self::output($result, $this->output);
    }
    /**
     * receive data from record(s)
     *
     * @param string|array $object              name of data you want to receive from record(s)
     * @param string|array|null $where_object   filtering records by  data name
     * @param string|array|null $where_value    filtering records by  data value
     * @param integer|null $limit               limit number of records to update
     * @return array|json|object                returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#receive-record
     */
    public function receive(string|array $object, string|array|null $where_object = null, string|array|null $where_value = null, int $limit = null)
    {
        if (is_array($object)) $object = json::_out($object);
        if (is_array($where_object)) $where_object = json::_out($where_object);
        if (is_array($where_value)) $where_value = json::_out($where_value);
        $result = $this->connection->send('datacenter', 'receive', [
            'object' => $object,
            'opt' => json::_out([
                'object' => $where_object,
                'value' => $where_value,
                'limit' => $limit
            ])
        ], 'POST', ['x-s-auth' => $this->session_id]);
        return self::output($result, $this->output);
    }
    /**
     * add a record
     *
     * @param string|array $object      name of data(s) you want to add
     * @param string|array $value       value of data(s) you want to add
     * @return array|json|object        returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#add-record
     */
    public function add(string|array $object, string|array $value)
    {
        if (is_array($object)) $object = json::_out($object);
        if (is_array($value)) $value = json::_out($value);
        $result = $this->connection->send('datacenter', 'add', [
            'object' => $object,
            'value' => $value,
        ], 'POST', ['x-s-auth' => $this->session_id]);
        return self::output($result, $this->output);
    }
    /**
     * delete record(s)
     *
     * @param integer|array|string $id         id of record(s) you want to delete (if you want to delete all or delete by filtering set "*")
     * @param string|array|null $where_object  filtering records by  data name
     * @param string|array|null $where_value   filtering records by  data value
     * @param integer|null $limit              limit number of records to delete
     * @return array|json|object               returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#delete-record
     */
    public function delete(int|array|string $id, string|array|null $where_object = null, string|array|null $where_value = null, int $limit = null)
    {
        if (is_array($id)) $id = json::_out($id);
        if (is_array($where_object)) $where_object = json::_out($where_object);
        if (is_array($where_value)) $where_value = json::_out($where_value);
        $result = $this->connection->send('datacenter', 'delete', [
            'id' => $id,
            'opt' => json::_out([
                'object' => $where_object,
                'value' => $where_value,
                'limit' => $limit
            ])
        ], 'POST', ['x-s-auth' => $this->session_id]);
        return self::output($result, $this->output);
    }
    /**
     * remove data from record(s)
     *
     * @param string|array $object              name of data you want to remove from record(s)
     * @param string|array|null $where_object   filtering records by  data name
     * @param string|array|null $where_value    filtering records by  data value
     * @param integer|null $limit               limit number of records to remove
     * @return array|json|object                returned data depends on your output selection in illuminating class (more details in link)
     * @link https://github.com/es-taheri/JSONDB-LV#remove-record
     */
    public function remove(string|array $object, string|array|null $where_object = null, string|array|null $where_value = null, int $limit = null)
    {
        if (is_array($object)) $object = json::_out($object);
        if (is_array($where_object)) $where_object = json::_out($where_object);
        if (is_array($where_value)) $where_value = json::_out($where_value);
        $result = $this->connection->send('datacenter', 'remove', [
            'object' => $object,
            'opt' => json::_out([
                'object' => $where_object,
                'value' => $where_value,
                'limit' => $limit
            ])
        ], 'POST', ['x-s-auth' => $this->session_id]);
        return self::output($result, $this->output);
    }
    private static function output(array $data, string $output_type = 'array')
    {
        switch ($output_type):
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
