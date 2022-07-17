<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Ticket\CreateRequest;
use App\Http\Requests\Admin\Ticket\UpdateRequest;
use App\Http\Response\Admin\TicketTransformer;
use Illuminate\Support\Str;
use Carbon\Carbon;

use DB;
use App\Ticket;
use App\Image;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $order_by = $request->input('order_by', 'id');
        $sort = $request->input('sort', 'asc');

        $search_by = $request->search_by;
        $keyword = $request->keyword;

        $per_page = $request->input('per_page', 10);

        try {
            $data = Ticket::order($order_by, $sort)
                ->search($search_by, $keyword)
                ->paginate($per_page);

            return TicketTransformer::getList($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function create(CreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $order = Ticket::count() + 1;
            $order = $request->input('order', $order);
            $slug = Str::slug($request->name, '-');


            $data = Ticket::create([
                'program_id' => $request->program_id,
                'order' => $order,
                'name' => $request->name,
                'slug' => $slug,

                'price_idr' => $request->price_idr,
                'price_usd' => $request->price_usd,

                'desc_id' => $request->desc_id,
                'desc_en' => $request->desc_en,

                'snk_id' => $request->snk_id,
                'snk_en' => $request->snk_en,

            ]);

            if ($request->filled('image') && isset($request->image)) {
                // $delete_path = str_replace('storage', '', $image);
                $image = imageUpload('public/ticket/', $request->image, NULL, Str::uuid());
                Image::updateOrCreate([
                    'relation_id' => $data->id,
                    'relation_type' => 'ticket',
                    'function_type' => 'banner',
                ], [
                    'path' => $image
                ]);
            }

            DB::commit();
            return TicketTransformer::getDetail($data);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function detail(Request $request, $id)
    {
        try {
            $data = Ticket::where([
                'id' => $id
            ])->firstOrfail();
            return TicketTransformer::getDetail($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function update(UpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = Ticket::where([
                'id' => $id
            ])->firstOrfail();

            $order = Ticket::count() + 1;
            $order = $request->input('order', $order);
            $slug = Str::slug($request->name, '-');

            $data->update([
                'program_id' => $request->program_id,
                'order' => $order,
                'name' => $request->name,
                'slug' => $slug,

                'price_idr' => $request->price_idr,
                'price_usd' => $request->price_usd,

                'desc_id' => $request->desc_id,
                'desc_en' => $request->desc_en,

                'snk_id' => $request->snk_id,
                'snk_en' => $request->snk_en,
            ]);
            $image = NULL;
            if ($request->filled('image') && isset($request->image)) {
                // $delete_path = str_replace('storage', '', $image);
                $image = imageUpload('public/ticket/', $request->image, NULL, Str::uuid());
            }
            Image::updateOrCreate([
                'relation_id' => $data->id,
                'relation_type' => 'ticket',
                'function_type' => 'banner',
            ], [
                'path' => $image
            ]);

            DB::commit();
            return TicketTransformer::getDetail($data->fresh());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $data = Ticket::where([
                'id' => $id
            ])->firstOrFail();
            $data->delete();
            DB::commit();
            return TicketTransformer::getDetail($data);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}
