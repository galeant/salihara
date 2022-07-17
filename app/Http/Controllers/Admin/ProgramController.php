<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;
use App\Http\Requests\Admin\Program\CreateRequest;
use App\Http\Requests\Admin\Program\UpdateRequest;
use App\Http\Response\Admin\ProgramTransformer;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Program;
use App\Penampil;
use App\Image;

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
                ->search($search_by, $keyword);

            if ($request->has('all') || $request->all == true) {
                $data = $data->get();
            } else {
                $data = $data->paginate($per_page);
            }
            // dd($data);
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

            $only_indo = $request->input('only_indo', false);
            $schedule = [];
            foreach ($request->schedule as $sch) {
                $tmp = [
                    'date' => Carbon::parse($sch['date']),
                    'unix_date' => strtotime(Carbon::parse($sch['date'])),
                    'hour' => (int)$sch['hour'],
                    'minute' => (int)$sch['minute'],
                ];
                $schedule[] = $tmp;
            }

            $fill_prog = [
                'order' => $order,
                'name' => $request->name,
                'slug' => $slug,
                'schedule' => json_encode($schedule),
                // 'schedule_unix' => $schedule,
                // 'schedule_date' => $schedule_date,
                // 'duration_hour' => $request->duration_hour,
                // 'duration_minute' => $request->duration_minute,
                'desc_id' => $request->desc_id,
                'desc_en' => $request->desc_en,
                'only_indo' => $only_indo,
                'trailer_url' => $request->trailer_url,
                'video_url' => $request->video_url,
                'type' => $request->type,
                'category' => $request->category,
                'color' => $request->color,
                'luring_url' => NULL
            ];
            if ($request->type == Program::type[1]) {
                $fill_prog['luring_url'] = $request->luring_url;
            }

            $data = Program::create($fill_prog);

            if ($request->filled('image') && isset($request->image)) {
                // $delete_path = str_replace('storage', '', $image);
                $image = imageUpload('public/program/', $request->image, NULL, Str::uuid());
                Image::updateOrCreate([
                    'relation_id' => $data->id,
                    'relation_type' => 'program',
                    'function_type' => 'banner',
                ], [
                    'path' => $image
                ]);
            }

            $get_penampil = Penampil::select('id')->get();
            $get_penampil = $get_penampil->pluck('id')->toArray();

            $exist_id = array_intersect($request->penampil_id, $get_penampil);
            $data->penampil()->sync($exist_id);

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

            $only_indo = $request->input('only_indo', false);
            $schedule = [];
            foreach ($request->schedule as $sch) {
                $tmp = [
                    'date' => Carbon::parse($sch['date']),
                    'unix_date' => strtotime(Carbon::parse($sch['date'])),
                    'hour' => $sch['hour'],
                    'minute' => $sch['minute'],
                ];
                $schedule[] = $tmp;
            }

            $fill_prog = [
                'order' => $order,
                'name' => $request->name,
                'slug' => $slug,
                'schedule' => json_encode($schedule),
                // 'schedule_unix' => $schedule,
                // 'schedule_date' => $schedule_date,
                // 'duration_hour' => $request->duration_hour,
                // 'duration_minute' => $request->duration_minute,
                'desc_id' => $request->desc_id,
                'desc_en' => $request->desc_en,
                'only_indo' => $only_indo,
                'trailer_url' => $request->trailer_url,
                'video_url' => $request->video_url,
                'type' => $request->type,
                'category' => $request->category,
                'color' => $request->color,
                'luring_url' => NULL,
            ];
            if ($request->type == Program::type[1]) {
                $fill_prog['luring_url'] = $request->luring_url;
            }
            $data->update($fill_prog);

            $image = NULL;
            if ($request->filled('image') && isset($request->image)) {
                // $delete_path = str_replace('storage', '', $image);
                $image = imageUpload('public/program/', $request->image, NULL, Str::uuid());
            }
            Image::updateOrCreate([
                'relation_id' => $data->id,
                'relation_type' => 'program',
                'function_type' => 'banner',
            ], [
                'path' => $image
            ]);

            $get_penampil = Penampil::select('id')->get();
            $get_penampil = $get_penampil->pluck('id')->toArray();
            $exist_id = array_intersect($request->penampil_id, $get_penampil);
            $data->penampil()->sync($exist_id);
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
