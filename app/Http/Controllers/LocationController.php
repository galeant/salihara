<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Province;
use App\City;
use App\District;
use App\SubDistrict;

use App\Http\Response\LocationTransformer;

class LocationController extends Controller
{
    public function province(Request $request)
    {
        try {
            $data = Province::when($request->has('id'), function ($q) use ($request) {
                $q->where('id', $request->id);
            })->when($request->has('name'), function ($q) use ($request) {
                $q->where('name_id', 'like', '%' . $request->name . '%')
                    ->orWhere('name_en', 'like', '%' . $request->name . '%');
            })->get();

            return LocationTransformer::region($data, 'province');
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function city(Request $request)
    {
        $validated = $request->validate([
            'province_id' => 'required|exists:province,id',
        ]);
        try {
            $data = City::where('province_id', $request->province_id)
                ->when($request->has('id'), function ($q) use ($request) {
                    $q->where('id', $request->id);
                })->when($request->has('name'), function ($q) use ($request) {
                    $q->where('name_id', 'like', '%' . $request->name . '%')
                        ->orWhere('name_en', 'like', '%' . $request->name . '%');
                })->get();

            return LocationTransformer::region($data, 'city');
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function district(Request $request)
    {
        $validated = $request->validate([
            'city_id' => 'required|exists:city,id',
        ]);
        try {
            $data = District::where('city_id', $request->city_id)
                ->when($request->has('id'), function ($q) use ($request) {
                    $q->where('id', $request->id);
                })->when($request->has('name'), function ($q) use ($request) {
                    $q->where('name_id', 'like', '%' . $request->name . '%')
                        ->orWhere('name_en', 'like', '%' . $request->name . '%');
                })->get();

            return LocationTransformer::region($data, 'district');
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function subDistrict(Request $request)
    {
        $validated = $request->validate([
            'district_id' => 'required|exists:district,id',
        ]);
        try {
            $data = SubDistrict::where('district_id', $request->district_id)
                ->when($request->has('id'), function ($q) use ($request) {
                    $q->where('id', $request->id);
                })->when($request->has('name'), function ($q) use ($request) {
                    $q->where('name_id', 'like', '%' . $request->name . '%')
                        ->orWhere('name_en', 'like', '%' . $request->name . '%');
                })->get();

            return LocationTransformer::region($data, 'sub_district');
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
