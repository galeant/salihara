<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Penampil;

use App\Http\Response\Customer\PenampilTransformer;


class PenampilController extends Controller
{
    public function index(Request $request)
    {
        $order_by = $request->input('order_by', 'id');
        $sort = $request->input('sort', 'asc');

        // $search_by = $request->search_by;
        // $keyword = $request->keyword;

        $per_page = $request->input('per_page', 10);

        try {
            $data = Penampil::order($order_by, $sort)
                ->when($request->has('program_slug'), function ($q) use ($request) {
                    $q->whereHas('program', function ($q1) use ($request) {
                        $q1->where('slug', $request->program_slug);
                    });
                });

            if ($request->has('all') || $request->all == true) {
                $data = $data->get();
            } else {
                $data = $data->paginate($per_page);
            }
            // dd($data);
            return PenampilTransformer::getList($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function detail(Request $request, $slug)
    {
        try {
            $data = Penampil::where('slug', $slug)->firstOrFail();
            return PenampilTransformer::getDetail($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
