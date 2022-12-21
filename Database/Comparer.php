<?php

namespace App\Services\Database;

interface Comparer
{

    public function listTables($connection, $type) :void;

    public static function fetchTable() :array;

}
