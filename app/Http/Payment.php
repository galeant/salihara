<?php

namespace App\Http;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class Payment
{

    private $paymentUrl = '/ePayment/WebService/PaymentAPI/Checkout';
    private $paymentUrlBeta = '/epayment/entry.asp';
    private $redirectUrl = '/PG';
    private $requeryUrl = '/epayment/enquiry.asp';

    const PAYMENT_STATUS = [
        'paid', 'pending', 'fail', 'cancel'
    ];

    // 1 =>  success, 6 => pending, 0 => fail
    const PAYMENT_STATUS_GATEWAY = [
        1, 6, 0
    ];


    const PAYMENT_METHOD = [
        [
            'id' => 35,
            'name' => 'Credit Card',
            'logo' => '',
        ],
        [
            'id' => 26,
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
        [
            'id' => 75,
            'name' => ' ShopeePay QRIS '
        ]
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

            'ResponseURL' => ENV('PAYMENT_RESPONSE_URL'),
            'BackendURL' => ENV('PAYMENT_BACKEND_URL'),

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
        if($request['PaymentId'] == 35 || $request['PaymentId'] == 63 || $request['PaymentId'] == 77){
            $redirect = $this->redirectRequest([
                'CheckoutID' => $apiCall->CheckoutID,
                'Signature' =>  $apiCall->Signature,
            ]);
        }

        if ($request['PaymentId'] == 75) { //ini qris, vartual number response dari apicall bentuknnya url untuk di download qrnya
            $url = $apiCall->VirtualAccountAssigned;
            $file_name = 'qr-' . $request['RefNo'] . '.png';
            if (!file_put_contents($file_name, file_get_contents($url))) {
                Log::error('qris qr ' . $file_name . ' error');
                throw new \Exception('error on qr generator');
            }
            $apiCall->VirtualAccountAssigned = asset($file_name);
        }

        $non_va = [35, 63, 77];
        if (in_array($request['PaymentId'], $non_va)) {
            $apiCall->VirtualAccountAssigned = NULL;
        }

        if (!isset($apiCall->TransactionExpiryDate)) {
            $apiCall->TransactionExpiryDate = Carbon::parse(time())->addDays(1)->format('d-m-Y H:i');
        }
        return $apiCall;
    }

    public function paymentRequestBeta($trans, $trans_detail)
    {
        $ref_no = $this->reffIdGenerator($trans['user_id']);
        $amount = $trans['net_value_idr'];

        $currency = 'IDR';
        $remark = $trans['discount_value'] !== NULL ? 'Discount: ' . $trans['discount_value'] : '';
        // $trans_detail_payload = collect($trans_detail)->transform(function ($v) {
        //     return [
        //         'Id' => $v['ticket_id'],
        //         'Name' => $v['ticket_name'],
        //         'Quantity' => $v['qty'],
        //         'Amount' => $v['total_price_idr'],
        //         'ParentType' => 'ITEM',
        //     ];
        // })->toArray();
        $request = [
            'MerchantCode' => ENV('MerchantCode'),
            'PaymentId' => (string)$trans['payment_method_id'],
            'Currency' => $currency,
            'RefNo' => $ref_no,
            'Amount' => (string)$amount,
            'ProdDesc' => 'TIcket',

            'UserName' => 'salihara',
            'UserEmail' => 'tiket@salihara.org',
            'UserContact' => '+628170771913',

            // 'RequestType' => 'Seamless',
            'Remark' => $remark,
            // 'Lang' => 'UTF-8',

            'ResponseURL' => ENV('PAYMENT_RESPONSE_URL'),
            'BackendURL' => ENV('PAYMENT_BACKEND_URL'),

            'Signature' => $this->getSignatureBeta([
                'RefNo' => $ref_no,
                'Amount' => (string)$amount,
                'Currency' => $currency
            ]),

            // 'ItemTransactions' => $trans_detail_payload,

            // 'ShippingAddress' => [
            //     'FirstName' => $trans['user_name'],
            //     'LastName' => '',
            //     'Address' => $trans['user_address'],
            //     'City' => $trans['city_name'],
            //     'State' => '',
            //     'PostalCode' => $trans['postal'],
            //     'Phone' => $trans['user_phone'],
            //     'CountryCode' => 'ID',

            // ],

            // 'BillingAddress' => [
            //     'FirstName' => $trans['user_name'],
            //     'LastName' => '',
            //     'Address' => $trans['user_address'],
            //     'City' => $trans['city_name'],
            //     'State' => '',
            //     'PostalCode' => $trans['postal'],
            //     'Phone' => $trans['user_phone'],
            //     'CountryCode' => 'ID',
            // ],

            // 'Sellers' => [
            //     'Id' => 'salihara',
            //     'Name' => 'salihara',
            //     'SelleridNumber' => 'salihara',
            //     'Email' => 'tiket@salihara.org',
            //     'Address' => [
            //         'FirstName' => 'salihara',
            //         'LastName' => 'salihara',
            //         'Address' => 'address',
            //         'City' => 'test',
            //         'State' => 'test',
            //         'PostalCode' => '15810',
            //         'Phone' => '1233321123321',
            //         'CountryCode' => 'ID',
            //     ]

            // ],
        ];

        $apiCall = $this->apiPaymentCall($request, $this->paymentUrlBeta, 'form');

        dd($apiCall);
        return $apiCall;
    }

    public function redirectRequest($payload)
    {
        $apiCall = $this->apiPaymentCall($payload, $this->redirectUrl);
        Log::info($apiCall);
        return true;
    }

    public function requeryRequest($payload)
    {
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

    private function getSignatureBeta($signer)
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

        $str_sign = implode('', $sign_payload);
        $signature = sha1($str_sign);

        $bin = '';
        for ($i = 0; $i < strlen($signature); $i = $i + 2) {
            $bin .= chr(hexdec(substr($signature, $i, 2)));
        }
        $aa  = base64_encode($bin);
        return $aa;
        return $signature;
    }

    private function apiPaymentCall($payload, $url, $type = 'json')
    {
        try {
            $data_payload = [
                RequestOptions::JSON => $payload
            ];
            if ($type == 'form') {
                $data_payload = [
                    'form_params' => $payload
                ];
            }
            $client = new Client([
                'base_uri' => ENV('IPAY88SANDBOX'),
            ]);
            $response = $client->post($url, $data_payload);
            $body = $response->getBody();
            $content = $body->getContents();
            if(!is_object($content)){
                $content = json_decode($content);
            }
            return $content;
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
