<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Image;
use App\Http\Response\Admin\MiscTransformer;
use App\Cart;
use App\Misc;
use App\FAQ;
use App\Committee;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\HelpMail;

class MiscController extends Controller
{

    public function banner(Request $request)
    {
        try {
            $data = Misc::with('mainImageBanner')->mainBanner()->get();
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

    public function faq(Request $request)
    {
        try {
            $order_by = $request->input('order_by', ['id']);
            $sort = $request->input('sort', ['asc']);

            $search_by = $request->search_by;
            $keyword = $request->keyword;

            $data = FAQ::order($order_by, $sort)
                ->search($search_by, $keyword)
                ->get();
            return MiscTransformer::faqGrouping($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function committee(Request $request)
    {
        try {
            // $order_by = $request->input('order_by', ['id']);
            // $sort = $request->input('sort', ['asc']);

            // $search_by = $request->search_by;
            // $keyword = $request->keyword;

            // $data = Committee::order($order_by, $sort)
            //     ->search($search_by, $keyword)
            //     ->get();
            $data = Misc::committee()->first();
            return MiscTransformer::committee($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function partner(Request $request, $type)
    {
        $avail_type = Misc::PARTNER_TYPE;
        if (!in_array($type, $avail_type)) {
            throw new HttpException(404);
        }
        try {
            $data = Image::where('function_type', $type)->get();
            return MiscTransformer::partner($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function helpMail(Request $request, $segment = 'Lainnya')
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            // 'subject' => 'required',
            'message' => 'required',
        ]);
        try {
            $email = 'tiket@salihara.org';
            if ($segment != 'pembayaran') {
                $email = 'cs@salihara.org';
            }
            $subject = 'Bantuan ' . ucwords($segment);
            Mail::to($email)
                ->queue(new HelpMail($request, $subject));
            return response()->json([
                'message' => 'Success',
                'result' => NULL
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
