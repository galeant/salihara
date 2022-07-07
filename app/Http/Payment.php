<?php

namespace App\Http;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class Payment
{

    private $paymentUrl = '/ePayment/WebService/PaymentAPI/Checkout';
    private $redirectUrl = '/PG';
    private $requeryUrl = '/epayment/enquiry.asp';

    const PAYMENT_STATUS = [
        'pending', 'paid', 'cancel'
    ];

    const PAYMENT_METHOD = [
        [
            'id' => 35,
            'name' => 'Credit Card',
            'logo' => '',
        ],
        [
            'id' => 83,
            'name' => 'BNI VA',
            'logo' => '',
        ],
        [
            'id' => 9,
            'name' => 'Maybank VA',
            'logo' => '',
        ],
        [
            'id' => 31,
            'name' => 'Permata VA',
            'logo' => '',
        ],
        [
            'id' => 17,
            'name' => 'Mandiri VA',
            'logo' => '',
        ],
        [
            'id' => 25,
            'name' => 'BCA VA',
            'logo' => '',
        ],
        // [
        //     'id' => 61,
        //     'name' => 'BRI VA',
        //     'logo' => '',
        // ],
        // [
        //     'id' => 26,
        //     'name' => 'BNI VA',
        //     'logo' => '',
        // ],
        // [
        //     'id' => 86,
        //     'name' => 'CIMB VA',
        //     'logo' => '',
        // ],
        [
            'id' => 63,
            'name' => 'OVO',
            'logo' => '',
        ],
        [
            'id' => 77,
            'name' => 'DANA',
            'logo' => '',
        ],
    ];



    public function paymentRequest($trans, $trans_detail)
    {
        $ref_no = $this->reffIdGenerator($trans['user_id']);
        $amount = $trans['net_value_idr'];
        $currency = 'IDR';
        $remark = $trans['discount_value'] !== NULL ? 'Discount: ' . $trans['discount_value'] : '';
        $trans_detail_payload = collect($trans_detail)->transform(function ($v) {
            return [
                'Id' => $v['ticket_id'],
                'Name' => $v['ticket_name'],
                'Quantity' => $v['qty'],
                'Amount' => $v['total_price_idr'],
                'ParentType' => 'ITEM',
            ];
        })->toArray();
        $request = [
            'ApiVersion' => '2.0',
            'MerchantCode' => ENV('MerchantCode'),
            'PaymentId' => (string)$trans['payment_method_id'],
            'Currency' => $currency,
            'RefNo' => $ref_no,
            'Amount' => (string)$amount,
            'ProdDesc' => 'TIcket',

            'UserName' => 'salihara',
            'UserEmail' => 'tiket@salihara.org',
            'UserContact' => '+628170771913',

            'RequestType' => 'Seamless',
            'Remark' => $remark,
            'Lang' => 'UTF-8',

            'ResponseURL' => ENV('PAYMENT_RECEIVE_URL'),
            'BackendURL' => ENV('PAYMENT_RECEIVE_URL'),

            'Signature' => $this->getSignature([
                'RefNo' => $ref_no,
                'Amount' => (string)$amount,
                'Currency' => $currency
            ]),

            'ItemTransactions' => $trans_detail_payload,

            'ShippingAddress' => [
                'FirstName' => $trans['user_name'],
                'LastName' => '',
                'Address' => $trans['user_address'],
                'City' => $trans['city_name'],
                'State' => '',
                'PostalCode' => $trans['postal'],
                'Phone' => $trans['user_phone'],
                'CountryCode' => 'ID',

            ],

            'BillingAddress' => [
                'FirstName' => $trans['user_name'],
                'LastName' => '',
                'Address' => $trans['user_address'],
                'City' => $trans['city_name'],
                'State' => '',
                'PostalCode' => $trans['postal'],
                'Phone' => $trans['user_phone'],
                'CountryCode' => 'ID',
            ],

            'Sellers' => [
                'Id' => 'salihara',
                'Name' => 'salihara',
                'SelleridNumber' => 'salihara',
                'Email' => 'tiket@salihara.org',
                'Address' => [
                    'FirstName' => 'salihara',
                    'LastName' => 'salihara',
                    'Address' => 'address',
                    'City' => 'test',
                    'State' => 'test',
                    'PostalCode' => '15810',
                    'Phone' => '1233321123321',
                    'CountryCode' => 'ID',
                ]

            ],
        ];
        $apiCall = $this->apiPaymentCall($request, $this->paymentUrl);

        return $apiCall;
    }

    public function redirectRequest($payload)
    {
    }

    public function requeryRequest($payload)
    {
    }

    public function getPaymentMethod()
    {
        $selection = array_merge(self::VA, self::E_WALLET);
        return $selection;
    }

    private function reffIdGenerator($user_id)
    {
        $param = [
            'SL',
            $user_id,
            Carbon::now()->format('Ymd'),
            time()
        ];
        return implode('-', $param);
    }

    private function getSignature($signer)
    {
        /*
        MerchantKey
        MerchantCode
        RefNo
        Amount
        Currency
        TransactionStatus
        */
        $sign_payload = [
            'MerchantKey' => ENV('MerchantKey'),
            'MerchantCode' => ENV('MerchantCode'),
            'RefNo' => $signer['RefNo'],
            'Amount' => $signer['Amount'],
            'Currency' => $signer['Currency'],
        ];
        if (isset($signer['TransactionStatus'])) {
            $sign_payload['TransactionStatus'] = $signer['TransactionStatus'];
        }

        $str_sign = '||' . implode('||', $sign_payload) . '||';
        $signature = hash('sha256', $str_sign);
        return $signature;
    }

    private function apiPaymentCall($payload, $url)
    {
        try {
            $client = new Client([
                'base_uri' => ENV('IPAY88SANDBOX'),
            ]);
            $response = $client->post($url, [
                RequestOptions::JSON => $payload
            ]);
            $body = $response->getBody();
            $content = $body->getContents();
            $content = json_decode($content);
            if ($content->Code != 1) {
                Log::error('Payment error:' . $content->Message);
                throw new \Exception('Error on Call payment');
            }
            return $content;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
