<?php

namespace App\Http;

class Payment
{

    private $paymentUrl = '/ePayment/WebService/PaymentAPI/Checkout';
    private $redirectUrl = '/PG';
    private $requeryUrl = '/epayment/enquiry.asp';

    const VA = [
        [
            'id' => 9,
            'name' => 'Maybank',
            'logo' => '',
        ],
        [
            'id' => 31,
            'name' => 'Permata',
            'logo' => '',
        ],
        [
            'id' => 17,
            'name' => 'Mandiri',
            'logo' => '',
        ],
        [
            'id' => 25,
            'name' => 'BCA',
            'logo' => '',
        ],
        [
            'id' => 61,
            'name' => 'BRI',
            'logo' => '',
        ],
        [
            'id' => 26,
            'name' => 'BNI',
            'logo' => '',
        ],
        [
            'id' => 86,
            'name' => 'CIMB',
            'logo' => '',
        ],
    ];

    const E_WALLET = [
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

    public function paymentRequest($payload)
    {
        // $ref_no = $payload['RefNo'];
        $ref_no = '10182019103003AM';

        // $amount = $payload['amount'];
        $amount = '500000';
        // $currency = $payload['currency'];
        $currency = 'IDR';

        $request = [
            'ApiVersion' => '2.0',
            'MerchantCode' => ENV('MerchantCode'),
            'PaymentId' => '25',
            'Currency' => $currency,
            'RefNo' => $ref_no,
            'Amount' => $amount,
            'ProdDesc' => 'TIcket',

            'UserName' => 'saliharatest',
            'UserEmail' => 'saliharatest@mail.com',
            'UserContact' => '1233321123321',

            'RequestType' => 'Seamless',
            'Remark' => '',
            'Lang' => 'UTF-8',

            'ResponseURL' => ENV('PAYMENT_RECEIVE_URL'),
            'BackendURL' => ENV('PAYMENT_RECEIVE_URL'),

            'Signature' => $this->getSignature([
                'RefNo' => $ref_no,
                'Amount' => $amount,
                'Currency' => $currency
            ]),

            'ItemTransactions' => [
                [
                    'Id' => '1',
                    'Name' => 'tiket test',
                    'Quantity' => '1',
                    'Amount' => $amount,
                    'ParentType' => 'ITEM',
                ]
            ],

            'ShippingAddress' => [
                'FirstName' => 'test',
                'LastName' => 'test',
                'Address' => 'test',
                'City' => 'test',
                'State' => 'test',
                'PostalCode' => '15810',
                'Phone' => '1233321123321',
                'CountryCode' => 'ID',

            ],

            'BillingAddress' => [
                'FirstName' => 'test',
                'LastName' => 'test',
                'Address' => 'test',
                'City' => 'test',
                'State' => 'test',
                'PostalCode' => '15810',
                'Phone' => '12333211123321',
                'CountryCode' => 'ID',

            ],

            'Sellers' => [
                'Id' => 'test',
                'Name' => 'test',
                'SelleridNumber' => '123332111233321',
                'Email' => 'test@mail.com',
                'Address' => [
                    'FirstName' => 'test',
                    'LastName' => 'test',
                    'Address' => 'test',
                    'City' => 'test',
                    'State' => 'test',
                    'PostalCode' => '15810',
                    'Phone' => '1233321123321',
                    'CountryCode' => 'ID',
                ]

            ],
        ];
        return $request;
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
}
