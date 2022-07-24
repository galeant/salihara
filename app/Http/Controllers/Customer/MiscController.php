<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Image;
use App\Http\Response\Admin\MiscTransformer;
use App\Cart;
use App\Misc;
use App\FAQ;

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
            $data = FAQ::get();
            return MiscTransformer::faqGrouping($data);
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
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
