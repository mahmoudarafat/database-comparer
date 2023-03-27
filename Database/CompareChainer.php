<?php

namespace App\Services\Database;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class CompareChainer
{
    public $current;
    public $source;

    public static function index()
    {
        $autoupdate = false;
//        dd(request()->all());
        if (strtolower(request()->getMethod()) == 'get') {
            self::publish();
            return view('Comparer::index', compact('autoupdate'));
        }
        try {
            return self::compare(request('source'), request('current'))->compareResults;
        } catch (\Exception $e) {
            return self::error($e);
        }
    }

    public static function error($e): string
    {
        return $e->getMessage() . ' on line: ' . $e->getLine() . ' in file: ' . $e->getFile() . ' with code: ' . $e->getCode();
    }

    public static function publish(): void
    {
        /********************** FOR Public Root { /public } ***************************/
        $destination = public_path('services' . DIRECTORY_SEPARATOR . 'database');
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }
        File::copy(base_path('app/Services/Database/views/assets/bootstrap.min.css'), $destination . '/bootstrap.min.css');
        File::copy(base_path('app/Services/Database/views/assets/bootstrap.min.js'), $destination . '/bootstrap.min.js');
        File::copy(base_path('app/Services/Database/views/assets/jquery.min.js'), $destination . '/jquery.min.js');
        File::copy(base_path('app/Services/Database/views/assets/clipboard.min.js'), $destination . '/clipboard.min.js');
        /************************* FOR Basic Root { / } ********************************/
        $destination2 = base_path('services' . DIRECTORY_SEPARATOR . 'database');
        if (!file_exists($destination2)) {
            mkdir($destination2, 0777, true);
        }
        File::copy(base_path('app/Services/Database/views/assets/bootstrap.min.css'), $destination2 . '/bootstrap.min.css');
        File::copy(base_path('app/Services/Database/views/assets/bootstrap.min.js'), $destination2 . '/bootstrap.min.js');
        File::copy(base_path('app/Services/Database/views/assets/jquery.min.js'), $destination2 . '/jquery.min.js');
        File::copy(base_path('app/Services/Database/views/assets/clipboard.min.js'), $destination2 . '/clipboard.min.js');
        /********************************************************************************/
        view()->addNamespace('Comparer', base_path('app/Services/Database/views'));
    }

    public static function take($data)
    {
        return new Comparable($data);
    }

    public static function compare($source, $current)
    {
        $defaultConn = config("database.connections.mysql");
        return
            self::take([
                'source' => $source,
                'current' => $current,
                'default_conn' => $defaultConn
            ])
                ->listTables()
                ->listTablesData()
                ->operate();
    }

    public static function listTables($data): object
    {
        $connection = $data['source'];
        $source = self::fetchTables($connection, 'source');

        $arr[] = $source;
        $connection = $data['current'];
        $current = self::fetchTables($connection, 'current');
        $arr = array_merge((array)$data, (array)$source, (array)$current);

        return (object)$arr;
    }

    public static function listTablesData($data)
    {

        $sourceDB = $data->source;
        $currentDB = $data->current;
        $sourceTables = $data->source_tables;
        $currentTables = $data->current_tables;

        $sourceDBTables = self::fetchDBTables($sourceDB, $sourceTables);
        $currentDBTables = self::fetchDBTables($currentDB, $currentTables);
        $sourceDBTables = ['source_tables' => (array)$sourceDBTables];
        $currentDBTables = ['current_tables' => (array)$currentDBTables];

        $result = array_merge((array)$data, $sourceDBTables, $currentDBTables);
        return collect($result);
    }

    public static function operate($data)
    {
        $source = (array)$data['source_tables'];
        $current = (array)$data['current_tables'];

        $differences = self::getDifferences($source, $current, $data);

        /*
        return [
            'source' => $sourceTableNames, // table names of source
            'current' => $currentTableNames, // table names of current
            'create' => $forCreate, // new tables to store in current
            'reverse' => $reverseTables, // new tables to store in source
            'updateColumns' => $newColumns, // tables need update in current
            'checkedColumns' => $checkColumns, // table need update in source
            'changedColumns' => $changedColumns, // table need update their data types
        ];
        */
        $currentUpdateQuery = $currentCreateQuery = ''; // queries for current DB;
        $reverseUpdateQuery = $reverseCreateQuery = ''; // queries for  source DB;
        $updateSourceQuery = $updateCurrentQuery = ''; // queries for dismatched Datatypes;

        if (sizeof($differences['create'])) {
            $currentCreateQuery .= "START TRANSACTION;SET sql_mode = '';";
            $currentCreateQuery .= self::createTables($differences['create']);
            $currentCreateQuery .= "COMMIT;";
        }
        if (sizeof($differences['reverse'])) {
            $reverseCreateQuery .= "START TRANSACTION;SET sql_mode = '';";
            $reverseCreateQuery .= self::createTables($differences['reverse']);
            $reverseCreateQuery .= "COMMIT;";
        }
        if (sizeof($differences['updateColumns'])) {
            $currentUpdateQuery .= "START TRANSACTION;SET sql_mode = '';";
            $currentUpdateQuery .= self::updateTables($differences['updateColumns']);
            $currentUpdateQuery .= "COMMIT;";

        }

        if (sizeof($differences['checkedColumns'])) {
            $reverseUpdateQuery .= "START TRANSACTION;SET sql_mode = '';";
            $reverseUpdateQuery .= self::updateTables($differences['checkedColumns']);
            $reverseUpdateQuery .= "COMMIT;";
        }

        $misSource = false;
        $misCurrent = false;
        if (sizeof($differences['changedColumns'])) {

            $misMatch = self::updateDataTypes($differences['changedColumns']);
            $inSource = $misMatch['source'];
            $inCurrent = $misMatch['current'];
            if (strlen($inSource)) {
                $misSource = true;
                $updateSourceQuery .= "START TRANSACTION;SET sql_mode = '';";
                $updateSourceQuery .= $inSource;
                $updateSourceQuery .= "COMMIT;";
            }
            if (strlen($inCurrent)) {
                $misCurrent = true;
                $updateCurrentQuery .= "START TRANSACTION;SET sql_mode = '';";
                $updateCurrentQuery .= $inCurrent;
                $updateCurrentQuery .= "COMMIT;";
            }
        }

        self::setConnection([], (array)$data['default_conn']);
        $query = [
            'currentCreate' => $currentCreateQuery,
            'currentUpdate' => $currentUpdateQuery,
            'reverseCreate' => $reverseCreateQuery,
            'reverseUpdate' => $reverseUpdateQuery,
            'misCurrent' => $misCurrent,
            'misSource' => $misSource,
            'updateSource' => $updateSourceQuery,
            'updateCurrent' => $updateCurrentQuery,
        ];

        view()->addNamespace('Comparer', base_path('app/Services/Database/views'));
        $db = [
            'source' => request('source')['db'],
            'current' => request('current')['db']
        ];
        $changedColumns = $differences['changedColumns'];

        self::autoUpdate($data, $query);

        $sourceDB = request('source');
        $currentDB = request('current');

        $autoupdate = false;
        if (array_key_exists('auto-update', $data['current']) || array_key_exists('auto-update', $data['source'])) {
            $autoupdate = true;
        }

        $view = view('Comparer::result', compact('query', 'db', 'changedColumns', 'sourceDB', 'currentDB', 'autoupdate'))->render();
        /*********************************** OR *****************************************/
        // view()->addLocation(base_path('app/Services/Database/views'));
        // $view = view('result', compact('query'))->render();
        /********************************************************************************/
        self::clear();
        return $view;
    }

    public static function autoUpdate($data, $query)
    {
        $sourceQueries =
            [
                $query['reverseCreate'],
                $query['reverseUpdate'],
            ];

        $sourceQueries = implode(' ', $sourceQueries);

        $currentQueries =
            [
                $query['currentCreate'],
                $query['currentUpdate'],
            ];
        $currentQueries = implode(' ', $currentQueries);

        if (array_key_exists('auto-update', $data['source'])) {
            self::setConnection($data['source']);
            if (in_array(request('datatype-update'), ['source', 'current']) && request('datatype-update') == 'source') {
                $sourceQueries .= $query['updateSource'];
            }
            $sourceQueries = trim(rtrim($sourceQueries));
            if (strlen($sourceQueries)) {
                \DB::unprepared($sourceQueries);
            }
        }
        if (array_key_exists('auto-update', $data['current'])) {
            self::setConnection($data['current']);
            if (in_array(request('datatype-update'), ['source', 'current']) && request('datatype-update') == 'current') {
                $currentQueries .= $query['updateCurrent'];
            }
            $currentQueries = trim(rtrim($currentQueries));
            if (strlen($currentQueries)) {
                \DB::unprepared($currentQueries);
            }
        }
    }

    public static function applyUpdates()
    {
        $connection = request('db');
        self::setConnection($connection);
        $query = trim(rtrim(str_replace('<br>', '', request('content'))));
        \DB::unprepared($query);
        return ['status' => true, 'msg' => 'Applied'];
    }

    public static function getChangesInColumns($table, $columnsData)
    {
        $querySource = '';
        $queryCurrent = '';
        foreach ($columnsData as $col) {
            $columnSource = self::prepareColumnData($col['source']);
            $columnCurrent = self::prepareColumnData($col['current']);

            $columnSource->nullString = '';
            $columnCurrent->nullString = '';
            $querySource .= 'ALTER TABLE `' . $table . '` CHANGE `' . $columnSource->field . '` `' . $columnCurrent->field . '` ' . $columnCurrent->type . ' ' . $columnCurrent->nullString . ';';
            $queryCurrent .= 'ALTER TABLE `' . $table . '` CHANGE `' . $columnSource->field . '` `' . $columnSource->field . '` ' . $columnSource->type . ' ' . $columnSource->nullString . ';';
        }
        return ['source' => $querySource, 'current' => $queryCurrent];
    }

    public static function updateDataTypes($tables)
    {
        $source = '';
        $current = '';

        foreach ($tables as $table => $columnsData) {
            $queries = self::getChangesInColumns($table, $columnsData);
            $source .= $queries['source'];
            $current .= $queries['current'];
        }
        $results = ['source' => $source, 'current' => $current];
        return $results;
    }


    public static function fetchTables($connection, $type): object
    {
        $data = [];
        self::setConnection($connection);

        $tables = \DB::select("SHOW FULL TABLES where Table_Type = 'BASE TABLE'");
      
        if (isset($tables)) {
            foreach ($tables as $table) {
                $data[$type . '_tables'][] = collect(array_values((array)$table))->first();
            }
        }
      
        if (sizeof($data) == 0) {
            $data[$type . '_tables'] = [];
        }
        return (object)$data;
    }

    public static function fetchDBTables($connection, $tables)
    {
        $result = [];
        self::setConnection($connection);
        if (isset($tables)) {
            foreach ($tables as $table) {
                $columns = \DB::select('DESC  `' . $table . '`');
                $result[$table][] = $columns;
            }
        }
        return $result;
    }

    public static function getchanges($leak, $data)
    {
        $forCreate = [];
        $forUpdate = [];
        // compare source with current;
        collect($leak)->map(function ($columns, $table) use ($data, &$forCreate, &$forUpdate) {
            if (!in_array($table, $data)) {
                $forCreate[$table] = $columns;
            } else {
                $forUpdate[$table] = $columns;
            }
        });

        return ['create' => $forCreate, 'update' => $forUpdate];
    }

    public static function getDifferences($resource, $compared, $data)
    {
        self::setConnection($data['source']);

        // $sourceTableNames = \DB::getDoctrineSchemaManager()->listTableNames(); // source tables
        $sourceTableNames = array_keys($data['source_tables']); // source tables

        self::setConnection($data['current']);
        // $currentTableNames = \DB::getDoctrineSchemaManager()->listTableNames(); // current tables
        $currentTableNames = array_keys($data['current_tables']); // current tables

        $forCreate = array_diff($sourceTableNames, $currentTableNames); // tables in source but not in new
        $forCheck = array_diff($currentTableNames, $sourceTableNames); // tables in new but not in source
        $forUpdate = array_diff($sourceTableNames, $forCreate); // tables in both and we will check them again for columns

        $update = self::getTablesForChange($forUpdate, $data);
        $createdTables = self::getTablesForChange($forCreate, $data);

        $createdTables = self::getRequiredColumns($data, $createdTables, 'source');
        $newColumns = self::getRequiredColumns($data, $update, 'source');
        $checkColumns = self::getRequiredColumns($data, $update, 'current');
        $changedColumns = self::getDisMatchedColumns($data, $update);

        $reverseTables = self::getReverseTables($data, $forCheck);

        return [
            'source' => $sourceTableNames, // table names of source
            'current' => $currentTableNames, // table names of current
            'create' => $createdTables, // new tables to store in current
            'reverse' => $reverseTables, // new tables to store in source
            'updateColumns' => $newColumns, // tables need update in current
            'checkedColumns' => $checkColumns, // table need update in source
            'changedColumns' => $changedColumns, // table need update in source
        ];
    }

    public static function getReverseTables($data, $tables)
    {
        $result = [];
        if (isset($tables)) {
            foreach ($tables as $table) {
                $result[$table] = $data['current_tables'][$table][0];
            }
        }
        return $result;
    }

    public static function getDisMatchedColumns($data, $update, $type = 'source')
    {
        $reverse = ($type == 'source') ? 'current' : 'source';

        $Columns = [];

        if (isset($update[$type])) {
            foreach ($update['source'] as $table => $dataTable) {
                $currentDatatable = $update[$reverse][$table] ?? [];

                if ($reverse == 'source') {
                    $first = $dataTable;
                    $second = $currentDatatable[0];
                } else {
                    $first = $dataTable[0];
                    $second = $currentDatatable;
                }
                $match = array_intersect($first, $second);

                if (sizeof($match)) {
                    // todo
                    /*  @add_queries-for-dismatch-data-types */

                    $tableColumns = $data[$type . '_tables'][$table];
                    $tableColumns = collect($tableColumns)->first();
                    $tableColumns = collect($tableColumns)->keyBy('Field');

                    $reverseColumns = $data[$reverse . '_tables'][$table];
                    $reverseColumns = collect($reverseColumns)->first();
                    $reverseColumns = collect($reverseColumns)->keyBy('Field');

                    foreach ($tableColumns as $column => $dataColumn) {
                        if (
                            $reverseColumns->has($column) && $dataColumn->Type != optional($reverseColumns->get($column))->Type) {
                            $Columns[$table][$column] = [
                                'source' => $dataColumn,
                                'current' => $reverseColumns->get($column)
                            ];
                        }
                    }
                }
            }
        }
        return $Columns;
    }

    public static function getRequiredColumns($data, $update, $type)
    {
        $reverse = ($type == 'source') ? 'current' : 'source';

        $Columns = [];

        if (isset($update[$type])) {
            foreach ($update[$type] as $table => $dataTable) {
                // dd($table, $dataTable);
                $currentDatatable = $update[$reverse][$table] ?? [];

                if ($reverse == 'source') {
                    $first = $dataTable;
                    $second = $currentDatatable[0];
                } else {
                    $first = $dataTable[0];
                    $second = $currentDatatable;
                }

                $diff = array_diff($first, $second);

                if (sizeof($diff)) {
                    foreach ($diff as $columnI) {
                        $tableColumns = $data[$type . '_tables'][$table];
                        $tableColumns = collect($tableColumns)->first();

                        $tableColumns = collect($tableColumns)->keyBy('Field');
                        $column = self::prepareColumnData($tableColumns->get($columnI));
                        $Columns[$table][] = self::getColumnData($column, $table, $data, $type);
                    }
                }

            }
        }
        return $Columns;
    }

    public static function getTablesForChange($forUpdate, $data)
    {
        $arr = [];
        self::setConnection($data['source']);
        if (isset($forUpdate)) {
            foreach ($forUpdate as $table) {
                $columnNames = \DB::getSchemaBuilder()->getColumnListing($table);
                $arr['source'][$table][] = $columnNames;
            }
        }
        self::setConnection($data['current']);
        if (isset($forUpdate)) {
            foreach ($forUpdate as $table) {
                $columnNames = \DB::getSchemaBuilder()->getColumnListing($table);
                $arr['current'][$table] = $columnNames;
            }
        }
        return $arr;
    }

    public static function setConnection($connection, $connData = null): void
    {
        self::clear();
        if (is_null($connData)) {
            $connData = [
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
            ];
        }
        Config::set("database.connections.mysql", $connData);
        \DB::purge('mysql');
    }

    public static function clear(): void
    {
        \Artisan::call('config:clear');
    }

    public static function createTables($tables)
    {
        /*
        CREATE TABLE `city` (
         `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
         `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
         `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
         `country_id` int(11) NOT NULL,
         `created_at` timestamp NULL DEFAULT NULL,
         `updated_at` timestamp NULL DEFAULT NULL,
         `deleted_at` timestamp NULL DEFAULT NULL,
         `saving` int(11) NOT NULL DEFAULT '0',
         PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        */
        $query = '';
        collect($tables)->map(function ($columns, $table) use (&$query) {
            $query .= 'CREATE TABLE `' . $table . '` (';
            $query .= self::createTableQuery($table, $columns);
            $query = rtrim($query, ',');
            $query .= ') ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';
        });
        return $query;
    }

    public static function createTableQuery($table, $columns)
    {
        $array = sizeof($columns) ? collect($columns) : collect();
        $query = '';
        if (isset($array)) {
            foreach ($array as$k => $column) {
                $column = self::prepareColumnData($column);
//                if($k > 1)dd($column);
                $query .= " `$column->field` $column->type $column->extraString $column->nullString $column->defaultString $column->keyString,";
            }
        }

        return $query;
    }

    public static function prepareColumnData($column)
    {

        $Field = $column->Field;
        $Type = $column->Type;
        $Null = $column->Null;
        $Key = $column->Key;
        $Default = $column->Default;
        $Extra = $column->Extra;

        $nullString = (strtolower($Null) == 'no') ? ' NOT NULL ' : 'NULL ';

        $defaultTo = request('default-update'); // [ no, null, string ]
        if (in_array($defaultTo, ['string'])) {
            $nullString = ' NOT NULL ';
        }

        if (in_array($defaultTo, ['null', 'string'])) {
            $Default = ($defaultTo == 'null') ? ' NULL ' : " '' ";
            $nullString = ($defaultTo == 'null') ? str_replace('NOT NULL', ' NULL ', $nullString) : $nullString;
        }

        if(strtolower($Key) == 'pri'){
            $Default = ' NOT NULL ';
            $nullString = '';
        }

        $defaultString = !is_null($Default) ? ' DEFAULT ' . $Default : '';
        
        if(is_string($Default)){
            $defaultString = !is_null($Default) ? "DEFAULT '$Default' "  : '';
        }

        if(strtolower($Key) == 'pri'){
            $defaultString = str_replace('DEFAULT', '', $defaultString);
        }

        $extraString = (strlen($Extra) && strtolower($Extra) == 'auto_increment') ? ' AUTO_INCREMENT ' : '';
        $keyString = '';
        if (strtolower($Key) == 'pri') {
            $keyString = ',PRIMARY KEY (`' . $Field . '`) ';
        }
        if (strtolower($Key) == 'uni') {
            $keyString = ',UNIQUE (`' . $Field . '`) ';
        }
        if (strtolower($Key) == 'mul') {
            $keyString = ',KEY (`' . $Field . '`) ';
        }
        return (object)[
            'field' => $Field,
            'type' => $Type,
            'nullString' => $nullString,
            'defaultString' => $defaultString,
            'extraString' => $extraString,
            'keyString' => $keyString,
        ];
    }

    public static function updateTables($tables)
    {
        $query = '';
        collect($tables)->map(function ($columns, $table) use (&$query) {
            $query .= self::updateTableQuery($table, $columns);
        });
        return $query;
    }

    public static function updateTableQuery($table, $columns)
    {

        $array = sizeof($columns) ? collect($columns) : collect();
        $query = '';
        if (isset($array)) {
            foreach ($array as $column) {
                $column = self::prepareColumnData($column);
                $query .= "ALTER TABLE  $table ADD COLUMN `$column->field` $column->type $column->nullString $column->defaultString ;";
            }
        }
        return $query;

    }

    public static function fetchColumnData($column, $table, $data)
    {
        $sourceData = self::getColumnData($column, $table, $data, 'source');
        $currentData = self::getColumnData($column, $table, $data, 'current');
        return (object)['current' => $currentData, 'source' => $sourceData];
    }

    public static function getColumnData($column, $table, $data, $type)
    {
        $type = $data[$type . '_tables'][$table][0];
        $type = collect($type);
        $type = $type->keyBy('Field');
        return $type->get($column->field);
    }

}
