<?php

namespace App\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\City;
use App\District;
use App\Province;
use App\SubDistrict;

class LocationQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $data, $type;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch ($this->type) {
            case 'city':
                $field = 'province_id';
                $model = new City;
                break;
            case 'district':
                $field = 'city_id';
                $model = new District;
                break;
            case 'sub_district':
                $field = 'district_id';
                $model = new SubDistrict;
                break;
        }
        foreach ($this->data as $rc) {
            switch ($this->type) {
                case 'city':
                    $ref = Province::where('id', $rc['province_id'])->first();
                    $ref = $ref->id;
                    break;
                case 'district':
                    $ref = City::where('name_id', $rc['city_name'])->first();
                    $ref = $ref->id;
                    $field = 'city_id';
                    break;
                case 'sub_district':
                    $ref = District::where('name_id', $rc['district_name'])->first();
                    $ref = $ref->id;
                    $field = 'district_id';
                    break;
            }
            $dtf = $model->firstOrCreate([
                $field => $ref,
                'name_id' => $rc['name_id'],
                'name_en' => $rc['name_en'],
            ]);
        }
    }
}
