<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Http\Response\Admin\UserTransformer;

use Illuminate\Support\Facades\Hash;
use DB;
use App\Http\Requests\Admin\User\CreateRequest;
use App\Http\Requests\Admin\User\UpdateRequest;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $order_by = $request->input('order_by', 'id');
        $sort = $request->input('sort', 'asc');

        $search_by = $request->search_by;
        $keyword = $request->keyword;

        $per_page = $request->input('per_page', 10);
        $user = auth()->user();

        try {
            $data = User::admin()
                ->order($order_by, $sort)
                ->where('id', '!=', $user->id)
                ->search($search_by, $keyword);

            if ($request->has('all') ||  $request->all == true) {
                $data = $data->get();
            } else {
                $data = $data->paginate($per_page);
            }

            return UserTransformer::getList($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function create(CreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = User::create([
                'role' => 'admin',
                'email' => $request->email,
                'name' => $request->name,
                'password' => Hash::make($request->password),
            ]);

            DB::commit();
            return UserTransformer::getDetail($data);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function detail(Request $request, $id)
    {
        try {
            $data = User::admin()
                ->where([
                    'id' => $id
                ])->firstOrfail();
            return UserTransformer::getDetail($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function update(UpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = User::admin()->where([
                'id' => $id
            ])->firstOrfail();

            $fill = [
                'role' => 'admin',
                'email' => $request->email,
                'name' => $request->name,
            ];

            if ($request->has('password') && $request->password != NULL) {
                $fill['password'] = Hash::make($request->password);
            }
            $data->update($fill);
            DB::commit();
            return UserTransformer::getDetail($data->fresh());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $data = User::where([
                'id' => $id
            ])->firstOrFail();
            $data->delete();
            DB::commit();
            return UserTransformer::getDetail($data);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function block(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = User::admin()
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
}
