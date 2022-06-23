<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Image;
use Illuminate\Support\Str;
use App\Http\Requests\Admin\Misc\BannerCreateRequest;
use App\Http\Response\Admin\MiscTransformer;

class MiscController extends Controller
{

    public function banner(Request $request)
    {
        try {
            $data = Image::where([
                'function_type' => 'main_banner'
            ])
                ->whereNull('relation_type')
                ->whereNull('relation_id')
                ->get();
            return MiscTransformer::banner($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function postBanner(BannerCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $req_banner = $request->banner;
            $exist = Image::where([
                'function_type' => 'main_banner'
            ])
                ->whereNull('relation_type')
                ->whereNull('relation_id')
                ->get();
            $max_count = count($exist);
            if (count($req_banner) > $max_count) {
                $max_count = count($request->banner);
            }

            for ($i = 0; $i < $max_count; $i++) {
                if (isset($req_banner[$i]) && isset($exist[$i])) {
                    $image = imageUpload('main_banner/', $req_banner[$i], NULL, Str::uuid());
                    $exist[$i]->update([
                        'path' => $image
                    ]);
                } else if (!isset($req_banner[$i]) && isset($exist[$i])) {
                    $exist[$i]->delete();
                } else if (isset($req_banner[$i]) && !isset($exist[$i])) {
                    $image = imageUpload('public/main_banner/', $req_banner[$i], NULL, Str::uuid());
                    Image::create([
                        'relation_id' => NULL,
                        'relation_type' => NULL,
                        'function_type' => 'main_banner',
                        'path' => $image
                    ]);
                }
            }
            DB::commit();
            return $this->banner($request);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}
