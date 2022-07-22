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
use App\Transaction;
use App\Http\Payment;
use App\Cart;

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

            $fill = [
                'type' => $request->type,
                'external_url' => NULL,
                // 'program_id' => $request->program_id,
                'order' => $order,
                'name' => $request->name,
                'slug' => $slug,

                'price_idr' => NULL,
                'price_usd' => NULL,

                'desc_id' => $request->desc_id,
                'desc_en' => $request->desc_en,

                'snk_id' => $request->snk_id,
                'snk_en' => $request->snk_en,

            ];
            if ($request->type == Ticket::type[1]) {
                $fill['external_url'] = $request->external_url;
            } else if ($request->type == Ticket::type[0]) {
                $fill['price_idr'] = $request->price_idr;
                $fill['price_usd'] = $request->price_usd;
            }

            $data = Ticket::create($fill);

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

            $data->program()->sync($request->program_id);

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

            $fill = [
                'type' => $request->type,
                'external_url' => NULL,
                // 'program_id' => $request->program_id,
                'order' => $order,
                'name' => $request->name,
                'slug' => $slug,

                'price_idr' => $request->price_idr,
                'price_usd' => $request->price_usd,

                'desc_id' => $request->desc_id,
                'desc_en' => $request->desc_en,

                'snk_id' => $request->snk_id,
                'snk_en' => $request->snk_en,

            ];
            if ($request->type == Ticket::type[1]) {
                $fill['external_url'] = $request->external_url;
            } else if ($request->type == Ticket::type[0]) {
                $fill['price_idr'] = $request->price_idr;
                $fill['price_usd'] = $request->price_usd;
            }

            $data->update($fill);
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

            $data->program()->sync($request->program_id);

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
            ])->withCount('comments')
                ->firstOrFail();

            $transaction = Transaction::whereHas('detail', function ($q) use ($id) {
                $q->where('ticket_id', $id);
            })->count();

            $cart = Cart::where('ticket_id', $id)->count();

            if ($data->user_count != 0 || $transaction != 0 || $cart != 0) {
                throw new \Exception('Ticket has been bought by customer');
            }
            $data->program()->detach();
            $data->user()->detach($id);
            $data->delete();

            DB::commit();
            return TicketTransformer::getDetail($data);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}
