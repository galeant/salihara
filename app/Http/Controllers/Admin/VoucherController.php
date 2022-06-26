<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Response\Admin\VoucherTransformer;
use Illuminate\Http\Request;
use App\Voucher;
use App\Http\Requests\Admin\Voucher\CreateRequest;
use App\Http\Requests\Admin\Voucher\UpdateRequest;

use DB;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $order_by = $request->input('order_by', 'id');
        $sort = $request->input('sort', 'asc');

        $search_by = $request->search_by;
        $keyword = $request->keyword;

        $per_page = $request->input('per_page', 10);

        try {
            $data = Voucher::order($order_by, $sort)
                ->search($search_by, $keyword);

            if ($request->has('all') ||  $request->all == true) {
                $data = $data->get();
            } else {
                $data = $data->paginate($per_page);
            }

            return VoucherTransformer::getList($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function create(CreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = Voucher::create([
                'code' => $request->code,
                'discount' => $request->discount,
                'quota' => $request->quota,
            ]);

            DB::commit();
            return VoucherTransformer::getDetail($data);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function detail(Request $request, $id)
    {
        try {
            $data = Voucher::where([
                'id' => $id
            ])->firstOrfail();
            return VoucherTransformer::getDetail($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function update(UpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = Voucher::where([
                'id' => $id
            ])->firstOrfail();
            $data->update([
                'unique_code' => $request->code,
                'discount' => $request->discount,
                'quota' => $request->quota,
            ]);


            DB::commit();
            return VoucherTransformer::getDetail($data->fresh());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $data = Voucher::where([
                'id' => $id
            ])->firstOrFail();
            $data->delete();
            DB::commit();
            return VoucherTransformer::getDetail($data);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}
