<?php

namespace App\Modules\HistoricalRecords\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\HistoricalRecords\Interfaces\LoggerModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogsWorkstationProfile extends Model implements LoggerModel {

	use SoftDeletes;

     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'logs_workstation_profile';

	/**
	 * The fillable fields for the model
	 */
	protected $fillable = [
		'worker_id',
		'host_name',
		'host_ip_address',
		'subnet',
		'gateway',
		'VLAN',
		'DNS_1',
		'DNS_2',
		'station_number',
		'ISP',
		'download_speed',
		'upload_speed',
		'public_ip',
		'country',
		'region',
		'city',
		'zip_code',
		'latitude',
		'longitude',
		'cpu',
        'cpu_util',
        'ram',
        'ram_usage',
        'DISK',
        'free_disk',
        'mtr_highest_avg',
        'mtr_highest_loss',
        'mtr_host'
	];
}
