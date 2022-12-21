<?php

namespace App\Services\Database;

use App\Services\Arafat\CalculatorChain;
use Illuminate\Support\Facades\Config;

class Compare implements Comparer
{

    public array $source;
    public array $current;
    public array $data;
    public function __construct($source, $current, $data)
    {
        $this->source = $source;
        $this->current = $current;
        $this->data = $data;
    }

    public function handle()
    {
        return self::listTables($this->source, 'source')->listTables($this->current, 'current');
    }




    public function add($val)
    {
        return new static(CalculatorChain::add($this->number, $val));
    }

    public function listTables($connection, $type): void
    {
        self::setConnection($connection);
        $tables = \DB::select('SHOW TABLES');
        $data = $this->data;
        foreach ($tables as $table) {
            $data[$type] = array_values((array)$table);
        }
        $data = collect($data)->flatten();
        $this->data = $data;
//        return $data;
    }

    public static function fetchTable(): array
    {
        $tables = \DB::select('DESC audios');
        dd($tables);
        return [];
    }

    public static function setConnection($connection): void
    {
        Config::set("database.connections.mysql", [
            'driver' => 'mysql',
            "host" => $connection['host'],
            "database" => $connection['db'],
            "username" => $connection['user'],
            "password" => $connection['pass'],
            "port" => $connection['port'],
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
        ]);

    }
}

?>
