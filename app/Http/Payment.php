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
            'desc_id' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.',
            'desc_en' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.'
        ],
        [
            'id' => 26,
            'name' => 'BNI VA',
            'logo' => '',
            'desc_id' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.',
            'desc_en' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.'
        ],
        [
            'id' => 9,
            'name' => 'Maybank VA',
            'logo' => '',
            'desc_id' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.',
            'desc_en' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.'
        ],
        [
            'id' => 31,
            'name' => 'Permata VA',
            'logo' => '',
            'desc_id' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.',
            'desc_en' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.'
        ],
        [
            'id' => 17,
            'name' => 'Mandiri VA',
            'logo' => '',
            'desc_id' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.',
            'desc_en' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.'
        ],
        [
            'id' => 25,
            'name' => 'BCA VA',
            'logo' => '',
            'desc_id' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.',
            'desc_en' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.'
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
            'desc_id' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.',
            'desc_en' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.'
        ],
        [
            'id' => 77,
            'name' => 'DANA',
            'logo' => '',
            'desc_id' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.',
            'desc_en' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.'
        ],
        [
            'id' => 75,
            'name' => ' ShopeePay QRIS',
            'logo' => '',
            'desc_id' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.',
            'desc_en' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.'
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
                'Name' => $v['ticket_name_id'],
                'Quantity' => $v['qty'],
                'Amount' => $v['total_price_idr'],
                'ParentType' => 'ITEM',
            ];
        })->toArray();
        $request = [
            'ApiVersion' => '2.0',
            'MerchantCode' => ENV('MerchantCode'),
            'PaymentId' => ''/*(string)$trans['payment_method_id']*/,
            'Currency' => $currency,
            'RefNo' => $ref_no,
            'Amount' => (string)$amount,
            'ProdDesc' => 'Ticket',

            'UserName' => '',
            'UserEmail' => '',
            'UserContact' => '',

            'RequestType' => 'Redirect',
            'Remark' => $remark,
            'Lang' => 'UTF-8',

            'ResponseURL' => ENV('TRANSACTION_DETAIL_URL') . $ref_no,
            // 'ResponseURL' => ENV('PAYMENT_RESPONSE_URL'),
            // 'BackendURL' => ENV('TRANSACTION_DETAIL_URL') . $ref_no,
            'BackendURL' => ENV('PAYMENT_BACKEND_URL'),

            'Signature' => $this->getSignature([
                'RefNo' => $ref_no,
                'Amount' => (string)$amount,
                'Currency' => $currency
            ]),

            'ItemTransactions' => $trans_detail_payload,

            'ShippingAddress' => [
                // 'FirstName' => $trans['user_name'],
                // 'LastName' => '',
                // 'Address' => $trans['user_address'],
                // 'City' => $trans['city_name'],
                // 'State' => '',
                // 'PostalCode' => $trans['postal'],
                // 'Phone' => $trans['user_phone'],
                // 'CountryCode' => 'ID',

                'FirstName' => '',
                'LastName' => '',
                'Address' => '',
                'City' => '',
                'State' => '',
                'PostalCode' => '',
                'Phone' => '',
                'CountryCode' => '',

            ],

            'BillingAddress' => [
                // 'FirstName' => $trans['user_name'],
                // 'LastName' => '',
                // 'Address' => $trans['user_address'],
                // 'City' => $trans['city_name'],
                // 'State' => '',
                // 'PostalCode' => $trans['postal'],
                // 'Phone' => $trans['user_phone'],
                // 'CountryCode' => 'ID',

                'FirstName' => '',
                'LastName' => '',
                'Address' => '',
                'City' => '',
                'State' => '',
                'PostalCode' => '',
                'Phone' => '',
                'CountryCode' => '',
            ],

            'Sellers' => [
                'Id' => '',
                'Name' => '',
                'SelleridNumber' => '',
                'Email' => '',
                'Address' => [
                    'FirstName' => '',
                    'LastName' => '',
                    'Address' => '',
                    'City' => '',
                    'State' => '',
                    'PostalCode' => '',
                    'Phone' => '',
                    'CountryCode' => '',
                ]

            ],
        ];

        $apiCall = $this->apiPaymentCall($request, $this->paymentUrl);
        // return $apiCall;
        // if ($request['PaymentId'] == 35 || $request['PaymentId'] == 63 || $request['PaymentId'] == 77) {
        //     $redirect = $this->redirectRequest([
        //         'CheckoutID' => $apiCall->CheckoutID,
        //         'Signature' =>  $apiCall->Signature,
        //     ]);
        // }

        // if ($request['PaymentId'] == 75) { //ini qris, vartual number response dari apicall bentuknnya url untuk di download qrnya
        //     $url = $apiCall->VirtualAccountAssigned;
        //     $file_name = 'qr-' . $request['RefNo'] . '.png';
        //     if (!file_put_contents($file_name, file_get_contents($url))) {
        //         Log::error('qris qr ' . $file_name . ' error');
        //         throw new \Exception('error on qr generator');
        //     }
        //     $apiCall->VirtualAccountAssigned = asset($file_name);
        // }

        // $non_va = [35, 63, 77];
        // if (in_array($request['PaymentId'], $non_va)) {
        //     $apiCall->VirtualAccountAssigned = NULL;
        // }

        // if (!isset($apiCall->TransactionExpiryDate)) {
        //     $apiCall->TransactionExpiryDate = Carbon::parse(time())->addDays(1)->format('d-m-Y H:i');
        // }
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
            // dd(json_encode($payload));
            $client = new Client([
                'base_uri' => ENV('IPAY88SANDBOX'),
            ]);
            $response = $client->post($url, $data_payload);
            $body = $response->getBody();
            $content = $body->getContents();

            if ($url !== $this->redirectUrl) {
                $content = json_decode($content);
                if ($content->Code != 1) {
                    Log::error('Payment error:' . $content->Message);
                    throw new \Exception('Error on Call payment');
                }
            }
            return $content;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
