<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Image;
use App\Http\Response\Admin\MiscTransformer;
use App\Cart;
use App\Misc;

use Illuminate\Support\Facades\Log;

class MiscController extends Controller
{

    public function banner(Request $request)
    {
        try {
            $data = Misc::with('mainImageBanner')->mainBanner()->first();
            return MiscTransformer::banner($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function about(Request $request)
    {
        try {
            $data = Misc::with('aboutImageBanner')->about()->first();
            return MiscTransformer::about($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function paymentTest(Request $request)
    {
        Log::info(json_encode($request->all()));
        dd('ini url penerima');
    }

    public function paymentRedirect(Request $request)
    {
        Log::info(json_encode($request->all()));
        dd('ini url redirect');
    }
}
