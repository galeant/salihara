<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Image;
use Illuminate\Support\Str;
use App\Http\Requests\Admin\Misc\BannerCreateRequest;
use App\Http\Requests\Admin\Misc\AboutCreateRequest;
use App\Http\Response\Admin\MiscTransformer;
use App\Misc;

class MiscController extends Controller
{

    public function banner(Request $request)
    {
        try {
            $data = Misc::with('mainImageBanner')->mainBanner()->get();
            return MiscTransformer::banner($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function postBanner(BannerCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $banner = $request->banner;
            $max = count($banner);
            $misc = Misc::where('segment', 'main_banner')->get();
            if ($misc->count() > $max) {
                $max = $misc->count();
            }
            for ($i = 0; $i < $max; $i++) {

                if (isset($banner[$i]) && isset($misc[$i])) {
                    $vbn = (object)$banner[$i];
                    $value_id = [
                        'title' => $vbn->title_id,
                        'sub_title' => $vbn->sub_title_id,
                        'desc' => $vbn->desc_id,
                    ];

                    $value_en = [
                        'title' => isset($vbn->title_en) ? $vbn->title_en : NULL,
                        'sub_title' => isset($vbn->sub_title_en) ? $vbn->sub_title_en : NULL,
                        'desc' => isset($vbn->desc_en) ? $vbn->desc_en : NULL,
                    ];
                    $misc[$i]->update([
                        'value_id' => json_encode($value_id),
                        'value_en' => json_encode($value_en)
                    ]);
                    $image = imageUpload('public/main_banner/', $vbn->image, NULL, Str::uuid());
                    Image::updateOrCreate([
                        'relation_id' => $misc[$i]->id,
                        'relation_type' => NULL,
                        'function_type' => 'main_banner',
                    ], [
                        'path' => $image
                    ]);
                } else if (isset($banner[$i]) && !isset($misc[$i])) {
                    $vbn = (object)$banner[$i];
                    $value_id = [
                        'title' => $vbn->title_id,
                        'sub_title' => $vbn->sub_title_id,
                        'desc' => $vbn->desc_id,
                    ];

                    $value_en = [
                        'title' => isset($vbn->title_en) ? $vbn->title_en : NULL,
                        'sub_title' => isset($vbn->sub_title_en) ? $vbn->sub_title_en : NULL,
                        'desc' => isset($vbn->desc_en) ? $vbn->desc_en : NULL,
                    ];
                    $data = Misc::create([
                        'segment' => 'main_banner',
                        'value_id' => json_encode($value_id),
                        'value_en' => json_encode($value_en)
                    ]);
                    $image = imageUpload('public/main_banner/', $vbn->image, NULL, Str::uuid());
                    Image::updateOrCreate([
                        'relation_id' => $data->id,
                        'relation_type' => NULL,
                        'function_type' => 'main_banner',
                    ], [
                        'path' => $image
                    ]);
                } else if (!isset($banner[$i]) && isset($misc[$i])) {
                    $misc[$i]->delete();
                }
            }
            DB::commit();
            return $this->banner($request);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function about(Request $request)
    {
        try {
            $data = Misc::with('aboutImageBanner')->about()->first();
            return MiscTransformer::about($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function postAbout(AboutCreateRequest $request)
    {
        try {
            $value_id = [
                'title' => $request->title_id,
                'sub_title' => $request->sub_title_id,
                'desc' => $request->desc_id,
            ];

            $value_en = [
                'title' => $request->input('title_en', NULL),
                'sub_title' => $request->input('sub_title_en', NULL),
                'desc' => $request->input('desc_en', NULL),
            ];
            $data = Misc::updateOrCreate([
                'segment' => 'about'
            ], [
                'value_id' => json_encode($value_id),
                'value_en' => json_encode($value_en)
            ]);
            if ($request->has('image')) {
                // $delete_path = str_replace('storage', '', $image);
                $image = imageUpload('public/misc/', $request->image, NULL, Str::uuid());
                Image::updateOrCreate([
                    'relation_id' => $data->id,
                    'relation_type' => NULL,
                    'function_type' => 'about',
                ], [
                    'path' => $image
                ]);
            }
            return $this->about($request);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
