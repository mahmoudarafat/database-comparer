<?php

namespace App\Services\Database;

class Comparable{

    // todo
    /*
    `
        Hello::listSource()->listCurrent()->fetchSourceTables()->fetchCurrentTables()->Compare()->print();
    `;
    */

    public $compareResults = [];
    public function __construct($compareResults)
    {
        $this->compareResults = $compareResults;
    }
    
    public function listTables()
    {
        $data = $this->compareResults;
        return new static(CompareChainer::listTables($data));
    }

    public function compare()
    {
        $data = $this->compareResults;

        return new static(CompareChainer::compare($data));
    }

    public function listTablesData()
    {
        $data = $this->compareResults;

        return new static(CompareChainer::listTablesData($data));
    }


    public function operate()
    {
        $data = $this->compareResults;
        return new static(CompareChainer::operate($data));
    }



}
