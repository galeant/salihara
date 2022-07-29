<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Image;
use Illuminate\Support\Str;
use App\Http\Requests\Admin\Misc\BannerCreateRequest;
use App\Http\Requests\Admin\Misc\AboutCreateRequest;

use App\Http\Requests\Admin\Misc\FaqCreateRequest;
use App\Http\Requests\Admin\Misc\CommitteeCreateRequest;

use App\Http\Response\Admin\MiscTransformer;
use App\Misc;
use App\FAQ;
use App\Committee;

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
                        'hyperlink' => $vbn->hyperlink,
                    ];

                    $value_en = [
                        'title' => isset($vbn->title_en) ? $vbn->title_en : NULL,
                        'sub_title' => isset($vbn->sub_title_en) ? $vbn->sub_title_en : NULL,
                        'desc' => isset($vbn->desc_en) ? $vbn->desc_en : NULL,
                        'hyperlink' => $vbn->hyperlink,
                    ];
                    $misc[$i]->update([
                        'value_id' => json_encode($value_id),
                        'value_en' => json_encode($value_en)
                    ]);
                    $image = NULL;
                    if (isset($vbn->image)) {
                        $image = imageUpload('public/main_banner/', $vbn->image, NULL, Str::uuid());
                    }
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
                        'hyperlink' => $vbn->hyperlink,
                    ];

                    $value_en = [
                        'title' => isset($vbn->title_en) ? $vbn->title_en : NULL,
                        'sub_title' => isset($vbn->sub_title_en) ? $vbn->sub_title_en : NULL,
                        'desc' => isset($vbn->desc_en) ? $vbn->desc_en : NULL,
                        'hyperlink' => $vbn->hyperlink,
                    ];
                    $data = Misc::create([
                        'segment' => 'main_banner',
                        'value_id' => json_encode($value_id),
                        'value_en' => json_encode($value_en)
                    ]);
                    $image = NULL;
                    if (isset($vbn->image)) {
                        $image = imageUpload('public/main_banner/', $vbn->image, NULL, Str::uuid());
                    }
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
            $image = NULL;
            if ($request->filled('image')) {
                // $delete_path = str_replace('storage', '', $image);
                $image = imageUpload('public/misc/', $request->image, NULL, Str::uuid());
            }
            Image::updateOrCreate([
                'relation_id' => $data->id,
                'relation_type' => NULL,
                'function_type' => 'about',
            ], [
                'path' => $image
            ]);
            return $this->about($request);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function faqIndex(Request $request)
    {
        $order_by = $request->input('order_by', ['id']);
        $sort = $request->input('sort', ['asc']);

        $search_by = $request->search_by;
        $keyword = $request->keyword;

        $per_page = $request->input('per_page', 10);

        try {
            $data = Faq::order($order_by, $sort)
                ->search($search_by, $keyword)
                ->paginate($per_page);

            return MiscTransformer::faqList($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    public function faqDetail(Request $request, $id)
    {
        try {
            $data = Faq::where('id', $id)->firstOrFail();
            return MiscTransformer::faqDetail($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function faqCreate(FaqCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $data  = Faq::create([
                'group' => json_encode([
                    'id' => $request->group_id,
                    'en' => $request->group_en,
                ]),
                'question' => json_encode([
                    'id' => $request->question_id,
                    'en' => $request->question_en,
                ]),
                'answer' => json_encode([
                    'id' => $request->answer_id,
                    'en' => $request->answer_en,
                ]),
            ]);
            DB::commit();
            return $this->faqDetail($request, $data->id);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function faqUpdate(FaqCreateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data  = Faq::where('id', $id)->firstOrFail();
            $data->update([
                'group' => json_encode([
                    'id' => $request->group_id,
                    'en' => $request->group_en,
                ]),
                'question' => json_encode([
                    'id' => $request->question_id,
                    'en' => $request->question_en,
                ]),
                'answer' => json_encode([
                    'id' => $request->answer_id,
                    'en' => $request->answer_en,
                ]),
            ]);
            DB::commit();
            return $this->faqDetail($request, $data->id);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function faqDelete(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = Faq::where('id', $id)->firstOrFail();
            $data->delete();
            DB::commit();
            return MiscTransformer::faqDetail($data);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    // COMMITEEE
    public function committee(Request $request)
    {
        try {
            $data = Misc::committee()->first();
            return MiscTransformer::committee($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function committeePost(CommitteeCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = Misc::updateOrCreate([
                'segment' => 'committee'
            ], [
                'value_id' => $request->value_id,
                'value_en' => $request->value_en
            ]);
            DB::commit();
            return MiscTransformer::committee($data);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
    // public function committeeIndex(Request $request)
    // {

    //     $order_by = $request->input('order_by', ['id']);
    //     $sort = $request->input('sort', ['asc']);

    //     $search_by = $request->search_by;
    //     $keyword = $request->keyword;

    //     $per_page = $request->input('per_page', 10);

    //     try {
    //         $data = Committee::order($order_by, $sort)
    //             ->search($search_by, $keyword)
    //             ->paginate($per_page);

    //         return MiscTransformer::committeeList($data);
    //     } catch (\Exception $e) {
    //         throw new \Exception($e->getMessage());
    //     }
    // }
    // public function committeeDetail(Request $request, $id)
    // {
    //     try {
    //         $data = Committee::where('id', $id)->firstOrFail();
    //         return MiscTransformer::committeeDetail($data);
    //     } catch (\Exception $e) {
    //         throw new \Exception($e->getMessage());
    //     }
    // }

    // public function committeeCreate(CommitteeCreateRequest $request)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $data  = Committee::create([
    //             'division_id' => $request->division_id,
    //             'division_en' => $request->division_en,
    //             'name' => $request->name,
    //         ]);
    //         DB::commit();
    //         return $this->committeeDetail($request, $data->id);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         throw new \Exception($e->getMessage());
    //     }
    // }

    // public function committeeUpdate(CommitteeCreateRequest $request, $id)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $data  = Committee::where('id', $id)->firstOrFail();
    //         $data->update([
    //             'division_id' => $request->division_id,
    //             'division_en' => $request->division_en,
    //             'name' => $request->name,
    //         ]);
    //         DB::commit();
    //         return $this->committeeDetail($request, $data->id);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         throw new \Exception($e->getMessage());
    //     }
    // }

    // public function committeeDelete(Request $request, $id)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $data = Committee::where('id', $id)->firstOrFail();
    //         $data->delete();
    //         DB::commit();
    //         return MiscTransformer::committeeDetail($data);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         throw new \Exception($e->getMessage());
    //     }
    // }
}
