<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Image;
use App\Http\Response\Admin\MiscTransformer;

class MiscController extends Controller
{

    public function banner(Request $request)
    {
        try {
            $data = Image::where([
                'function_type' => 'main_banner'
            ])
                ->whereNull('relation_type')
                ->whereNull('relation_id')
                ->get();
            return MiscTransformer::banner($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function cart(Request $request)
    {
        dd('fwfwfw');
        try {
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
