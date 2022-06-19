<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;
use App\Http\Requests\Admin\Program\CreateRequest;
use App\Http\Requests\Admin\Program\UpdateRequest;
use App\Http\Response\ProgramTransformer;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Program;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $order_by = $request->input('order_by', 'id');
        $sort = $request->input('sort', 'asc');

        $search_by = $request->search_by;
        $keyword = $request->keyword;

        $per_page = $request->input('per_page', 10);

        try {
            $data = Program::order($order_by, $sort)
                ->search($search_by, $keyword)
                ->paginate($per_page);

            return ProgramTransformer::getList($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function create(CreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $order = Program::count() + 1;
            $order = $request->input('order', $order);
            $slug = Str::slug($request->name, '-');
            $schedule_date = Carbon::parse($request->schedule_date);
            $schedule = strtotime($schedule_date);
            $only_indo = $request->input('only_indo', false);

            $data = Program::create([
                'order' => $order,
                'name' => $request->name,
                'slug' => $slug,
                'schedule_unix' => $schedule,
                'schedule_date' => $schedule_date,
                'duration_hour' => $request->duration_hour,
                'duration_minute' => $request->duration_minute,
                'desc_id' => $request->desc_id,
                'desc_en' => $request->desc_en,
                'only_indo' => $only_indo,
            ]);

            $data->penampil()->sync($request->penampil_id);

            DB::commit();
            return ProgramTransformer::getDetail($data);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function detail(Request $request, $id)
    {
        try {
            $data = Program::where([
                'id' => $id
            ])->firstOrfail();
            return ProgramTransformer::getDetail($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function update(UpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = Program::where([
                'id' => $id
            ])->firstOrfail();

            $order = Program::count() + 1;
            $order = $request->input('order', $order);
            $slug = Str::slug($request->name, '-');
            $schedule_date = Carbon::parse($request->schedule_date);
            $schedule = strtotime($schedule_date);
            $only_indo = $request->input('only_indo', false);

            $data->update([
                'order' => $order,
                'name' => $request->name,
                'slug' => $slug,
                'schedule_unix' => $schedule,
                'schedule_date' => $schedule_date,
                'duration_hour' => $request->duration_hour,
                'duration_minute' => $request->duration_minute,
                'desc_id' => $request->desc_id,
                'desc_en' => $request->desc_en,
                'only_indo' => $only_indo,
            ]);

            $data->penampil()->sync($request->penampil_id);

            DB::commit();
            return ProgramTransformer::getDetail($data->fresh());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $data = Program::where([
                'id' => $id
            ])->firstOrFail();
            $data->penampil()->detach();
            $data->delete();
            DB::commit();
            return ProgramTransformer::getDetail($data);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}
