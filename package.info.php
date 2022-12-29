<?php
/**
 * JSONDB-LV is a PHP library for easier usage of JSONDB server/host versions.
 * You can use this library for more async and better connection to your jsondb server/host version in PHP programming language
 * Installation docs : https://github.com/es-taheri/JSONDB-LV#installation
 * Usage docs : https://github.com/es-taheri/JSONDB-LV#usage
 * Report issue : https://github.com/es-taheri/JSONDB-LV/issues/new/choose
 * Contact developer : # Telegram : https://t.me/estaheri - # Email : ta.es1383@gmail.com
 * @category   Database-Manager
 * @package    JSONDB-LV
 * @author     Esmaeil Taheri <ta.es1383@gmail.com>
 * @license    https://raw.githubusercontent.com/es-taheri/JSONDB-LV/JSONDB/LICENSE  MIT License
 * @version    Release: 1.0.2-beta
 * @link       https://github.com/es-taheri/JSONDB-LV
 * @since      File available since Release v1.0
 */
// JSONDB main NameSpace
namespace JSONDB;
class JSONDB{
    public function __construct()
    {
        $this->category="Database-Manager";
        $this->package="JSONDB-LV";
        $this->author="Esmaeil Taheri <ta.es1383@gmail.com>";
        $this->license="https://raw.githubusercontent.com/es-taheri/JSONDB-LV/JSONDB/LICENSE  MIT License";
        $this->version="Release: 1.0.2-beta";
        $this->link="https://github.com/es-taheri/JSONDB-LV";
        $this->since="File available since Release v1.0";
    }
}