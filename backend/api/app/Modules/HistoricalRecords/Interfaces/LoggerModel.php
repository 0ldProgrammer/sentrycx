<?php 

namespace App\Modules\HistoricalRecords\Interfaces;


interface LoggerModel {

    public function save();

    public static function query();
}