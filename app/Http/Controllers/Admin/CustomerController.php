<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use DB;

use App\Http\Response\Admin\CustomerTransformer;
use App\Http\Controllers\AuthController;
use App\Http\Requests\Admin\RegisterRequest;
use App\Http\Requests\Admin\User\ProgramAccessRequest;
use App\Http\Controllers\TransactionController;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $order_by = $request->input('order_by', 'id');
        $sort = $request->input('sort', 'asc');

        $search_by = $request->search_by;
        $keyword = $request->keyword;

        $per_page = $request->input('per_page', 10);
        try {
            $data = User::customer()
                ->order($order_by, $sort)
                ->search($search_by, $keyword);

            if ($request->has('all') ||  $request->all == true) {
                $data = $data->get();
            } else {
                $data = $data->paginate($per_page);
            }
            return CustomerTransformer::getList($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function create(RegisterRequest $request)
    {
        return (new AuthController)->register($request);
    }


    public function detail(Request $request, $id)
    {
        try {
            $data = User::customer()
                ->where([
                    'id' => $id
                ])->firstOrfail();
            return CustomerTransformer::getDetail($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function block(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = User::customer()
                ->where([
                    'id' => $id
                ])->firstOrfail();

            $data->update([
                'is_disabled' => !$data->is_disabled
            ]);
            DB::commit();
            return $this->index($request);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function programAccess($id, ProgramAccessRequest $request)
    {
        DB::beginTransaction();
        try {

            $user = User::customer()->where('id', $id)->firstOrfail();
            $akses_request = $request->program_id;
            $exist_access = $user->access->pluck('id')->toArray();

            $final_access = array_diff($akses_request, $exist_access);
            $user->access()->attach($final_access);
            DB::commit();
            return $this->detail($request, $id);
        } catch (\Exception $e) {

            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function transaction(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = User::customer()->where('id', $id)->firstOrfail();
            $request->request->add(['user' => $user]);
            return (new TransactionController)->transaction($request);
        } catch (\Exception $e) {

            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}
