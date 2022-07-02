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
            $data = Image::where([
                'function_type' => 'main_banner'
            ])
                ->whereNull('relation_type')
                ->whereNull('relation_id')
                ->first();
            return MiscTransformer::banner($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function about(Request $request)
    {
        try {
            $data = Misc::where([
                'segment' => 'about'
            ])
                ->first();
            // dd($data);
            return MiscTransformer::about($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function cart(Request $request)
    {
        $user = auth()->user();
        try {
            $data = Cart::with('user', 'ticket.program')->where('user_id', $user->id)->get();
            return MiscTransformer::cart($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function addCart(Request $request)
    {
        $user = auth()->user();
        try {
            $data = Cart::where([
                'user_id' => $user->id,
                'ticket_id' => $request->ticket_id
            ])->first();
            if ($data == NULL) {
                $data->update([
                    'qty' => ($data->qty + 1)
                ]);
            } else {
                $data = Cart::create([
                    'user_id' => $user->id,
                    'ticket_id' => $request->ticket_id,
                    'qty' => 1
                ]);
            }
            return $this->cart($request);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function checkout(Request $request)
    {
        dd('ini checkout');
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
