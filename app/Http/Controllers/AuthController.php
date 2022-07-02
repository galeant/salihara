<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Support\Facades\Route;
use DB;
use Carbon\Carbon;

use App\Province;
use App\City;
use App\District;
use App\SubDistrict;
use App\Jobs\LocationQueue;
use App\Http\Payment;
use App\Http\Requests\Admin\RegisterRequest;
use App\Jobs\SendRegisterEmailJob;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Http\Response\AuthTransformer;

class AuthController extends Controller
{

    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {

            $user = User::create([
                'role' => 'customer',
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                "gender" => $request->gender,
                "birth_year" => $request->birth_year,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'province_id' => $request->province_id,
                'city_id' => $request->city_id,
                'district_id' => $request->district_id,
                'sub_district_id' => $request->sub_district_id,
            ]);
            $token = [
                'exp' => Carbon::now()->format('Y-m-d H:i:s'),
                'user_id' => $user->id,
                'created_at' => $user->created_at
            ];
            $token = tokenize($token);
            $user->update([
                'email_token' => $token
            ]);
            $user = $user->fresh();
            SendRegisterEmailJob::dispatch($user);
            DB::commit();
            return AuthTransformer::profile($user);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function login(LoginRequest $request)
    {
        $uri = Route::current()->uri;
        $customer = str_starts_with($uri, 'customer');
        $admin = str_starts_with($uri, 'admin');

        try {
            $user = User::when($customer == true, function ($q) {
                $q->customer()
                    ->verified();
            })->when($admin == true, function ($q) {
                $q->admin();
            })->where([
                'email' => $request->email,
            ])->firstOrFail();
            // dd($user);
            if (Hash::check($request->password, $user->password)) {
                $token = auth()->attempt([
                    'email' => $user->email,
                    'password' => $request->password
                ]);
                return AuthTransformer::login($user, $token);
            }
            throw new \Exception('Wrong Password');
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function profile()
    {
        try {
            return AuthTransformer::profile(auth()->user());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $data = User::where('id', $user->id)->firstOrFail();
            $field = [
                'name', 'password', 'phone', 'gender',
                'birth_year', 'address', 'province',
                'city', 'district', 'sub_district',
                'postal'
            ];
            $request_key = collect($request->all())->keys();
            $intersect = $request_key->intersect($field);

            foreach ($intersect as $isc) {
                // $key = str_replace('province', 'province_id', $isc);
                // $key = str_replace('city', 'city_id', $key);
                // $key = str_replace('district', 'district_id', $key);
                // $key = str_replace('sub_district', 'sub_district', $key);
                $data->$isc =  $request->$isc;
            }
            $data->save();
            DB::commit();
            return AuthTransformer::profile($data->fresh());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function logout()
    {
        try {
            auth()->logout();
            return response()->json([
                'code' => 200,
                'message' => 'Logout Success',
                'result' => NULL
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    public function test()
    {
        $payment = (new Payment)->paymentRequest([]);
        dd(json_encode($payment));
        // $data = DB::table('recurve')->get();
        // // $c = $this->buildTree($data);
        // $d = $this->buildtier($data);
        // dd($d);
    }

    private function buildTree($elements, $parent_id  = 0)
    {
        $branch = array();

        foreach ($elements as $element) {
            if ($element->parent_id == $parent_id) {
                $children = $this->buildTree($elements, $element->id);
                if ($children) {
                    $element->children = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    private function buildtier($data, $parent_id = [])
    {
        $return = [];

        // dd($return);

        if (count($parent_id) == 0) {
            $ap_return = collect($data)->filter(function ($v) {
                if ($v->parent_id == NULL) {
                    return $v;
                }
            });
            // dd($ap_return);
            $return[] = $ap_return;
            $parent_id = $ap_return->pluck('id');
        } else {
            $this->buildtier($data, $parent_id);
        }
        dd($return);
        dd('qwdqdq');
    }

    public function loc()
    {
        $province = DB::table('db_province_data')->get();
        foreach ($province as $pr) {
            Province::updateOrCreate([
                'id' => $pr->province_code
            ], [
                'name_id' => ucwords(strtolower($pr->province_name)),
                'name_en' => ucwords(strtolower($pr->province_name_en)),
            ]);
        }
        $d = Province::get();
        $regio = DB::table('db_postal_code_data')->get();

        $ar_city = [];
        $ar_district = [];
        $ar_sub_district = [];

        $city = collect($regio)->groupBy('city');
        foreach ($city as $ict => $ct) {
            $ar_city[] = [
                'province_id' => $ct->first()->province_code,
                'name_id' => ucwords(strtolower($ict)),
                'name_en' => ucwords(strtolower($ict)),
            ];
            // $city = City::firstOrCreate([
            //     'province_id' => $ct->first()->province_code,
            //     'name_id' => ucwords(strtolower($ict)),
            //     'name_en' => ucwords(strtolower($ict)),
            // ]);
            $s_ct = collect($ct)->groupBy('sub_district');
            foreach ($s_ct as $if_ct => $f_ct) {
                $ar_district[] = [
                    // 'city_id' => $city->id,
                    'city_name' => ucwords(strtolower($ict)),
                    'name_id' => ucwords(strtolower($if_ct)),
                    'name_en' => ucwords(strtolower($if_ct)),
                ];
                // $disrict = District::firstOrCreate([
                //     'city_id' => $city->id,
                //     'name_id' => ucwords(strtolower($if_ct)),
                //     'name_en' => ucwords(strtolower($if_ct)),
                // ]);

                foreach ($f_ct as $iff_ct => $ff_ct) {
                    $ar_sub_district[] = [
                        // 'district_id' => $disrict->id,
                        'district_name' => ucwords(strtolower($if_ct)),
                        'name_id' => ucwords(strtolower($ff_ct->urban)),
                        'name_en' => ucwords(strtolower($ff_ct->urban)),
                    ];
                    // $sub_district = SubDistrict::firstOrCreate([
                    //     'district_id' => $disrict->id,
                    //     'name_id' => ucwords(strtolower($ff_ct->urban)),
                    //     'name_en' => ucwords(strtolower($ff_ct->urban)),
                    // ]);
                    // dd($ff_ct);
                    // dd($ff_ct);
                }
            }
        }
        $limit = 500;

        $ar_city = collect($ar_city)->chunk($limit);
        foreach ($ar_city as $arc) {
            LocationQueue::dispatch($arc, 'city');
            // foreach ($arc as $rc) {
            //     City::firstOrCreate([
            //         'province_id' => $rc['province_id'],
            //         'name_id' => $rc['name_id'],
            //         'name_en' => $rc['name_en'],
            //     ]);
            // }
        }

        $ar_district = collect($ar_district)->chunk($limit);
        foreach ($ar_district as $ard) {
            LocationQueue::dispatch($ard, 'district');
            // foreach ($ard as $rd) {
            //     $arc_city = City::where('name_id', $rd['city_name'])->first();
            //     District::firstOrCreate([
            //         'city_id' => $arc_city->id,
            //         'name_id' => ucwords(strtolower($if_ct)),
            //         'name_en' => ucwords(strtolower($if_ct)),
            //     ]);
            // }
        }


        $ar_sub_district = collect($ar_sub_district)->chunk($limit);
        foreach ($ar_sub_district as $arsd) {
            LocationQueue::dispatch($arsd, 'sub_district');
            // foreach ($arsd as $rsd) {
            //     $rsd = District::where('name_id', $rsd['district_name'])->first();
            //     SubDistrict::firstOrCreate([
            //         'district_id' => $rsd->id,
            //         'name_id' => ucwords(strtolower($ff_ct->urban)),
            //         'name_en' => ucwords(strtolower($ff_ct->urban)),
            //     ]);
            // }
        }
        // dd($ar_city);
        dd($ar_sub_district);
        $district = collect($regio)->groupBy('urban');
        $sub_district = collect($regio)->groupBy('sub_district');
    }
}
