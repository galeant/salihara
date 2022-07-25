<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Penampil\CreateRequest;
use App\Http\Requests\Admin\Penampil\UpdateRequest;
use App\Penampil;
use App\Http\Response\Admin\PenampilTransformer;

use Illuminate\Support\Str;
use App\Image;

class PenampilController extends Controller
{
    public function index(Request $request)
    {
        $order_by = $request->input('order_by', ['id']);
        $sort = $request->input('sort', ['asc']);

        $search_by = $request->search_by;
        $keyword = $request->keyword;

        $per_page = $request->input('per_page', 10);

        try {
            $data = Penampil::order($order_by, $sort)
                ->search($search_by, $keyword);

            if ($request->has('all') ||  $request->all == true) {
                $data = $data->get();
            } else {
                $data = $data->paginate($per_page);
            }

            return PenampilTransformer::getList($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function create(CreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $slug = Str::slug($request->name, '-');
            $data = Penampil::create([
                'name' => $request->name,
                'slug' => $slug,
                'desc_id' => $request->desc_id,
                'desc_en' => $request->desc_en
            ]);
            if ($request->filled('image') && isset($request->image)) {
                // $delete_path = str_replace('storage', '', $image);
                $image = imageUpload('public/penampil/', $request->image, NULL, Str::uuid());
                Image::updateOrCreate([
                    'relation_id' => $data->id,
                    'relation_type' => 'penampil',
                    'function_type' => 'banner',
                ], [
                    'path' => $image
                ]);
            }
            DB::commit();
            return PenampilTransformer::getDetail($data);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function detail(Request $request, $id)
    {
        try {
            $data = Penampil::where([
                'id' => $id
            ])->firstOrfail();
            return PenampilTransformer::getDetail($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function update(UpdateRequest $request, $id)
    {
        DB::beginTransaction();
        $delete_path = NULL;
        try {
            $data = Penampil::where([
                'id' => $id
            ])->firstOrfail();

            $slug = Str::slug($request->name, '-');
            $data->update([
                'name' => $request->name,
                'slug' => $slug,
                'desc_id' => $request->desc_id,
                'desc_en' => $request->desc_en
            ]);
            $image = NULL;
            if ($request->filled('image') && isset($request->image)) {
                $image = imageUpload('public/penampil/', $request->image, NULL, Str::uuid());
            }
            Image::updateOrCreate([
                'relation_id' => $data->id,
                'relation_type' => 'penampil',
                'function_type' => 'banner',
            ], [
                'path' => $image
            ]);
            DB::commit();
            return PenampilTransformer::getDetail($data->fresh());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $data = Penampil::where([
                'id' => $id
            ])->firstOrFail();
            $data->program()->detach();
            $data->delete();
            DB::commit();
            return PenampilTransformer::getDetail($data);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}
