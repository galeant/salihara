<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
}
