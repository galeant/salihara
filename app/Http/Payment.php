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
        $amount = $payload['amount'];
        $currency = $payload['currency'];
        $request = [
            'ApiVersion' => '2.0',
            'MerchantCode' => '2',
            'PaymentId' => '2',
            'Currency' => 'IDR',
            'RefNo' => '2',
            'Amount' => '2',
            'ProdDesc' => 'TIcket',

            'UserName' => '2',
            'UserEmail' => '2',
            'UserContact' => '2',

            'RequestType' => '2',
            'Remark' => '2',
            'Lang' => 'UTF-8',

            'ResponseURL' => '2',
            'BackendURL' => '2',

            'Signature' => $this->getSignature([
                'RefNo' => $payload['RefNo'],
                'Amount' => $amount,
                'Currency' => $currency
            ]),

            'ItemTransactions' => [
                [
                    'Id' => '2',
                    'Name' => '2',
                    'Quantity' => '2',
                    'Amount' => '2',
                    'ParentType' => 'ITEM',
                ]
            ],

            'ShippingAddress' => [
                'FirstName' => 'as',
                'LastName' => 'as',
                'Address' => 'as',
                'City' => 'as',
                'State' => 'as',
                'PostalCode' => 'as',
                'Phone' => 'as',
                'CountryCode' => 'ID',

            ],

            'BillingAddress' => [
                'FirstName' => 'as',
                'LastName' => 'as',
                'Address' => 'as',
                'City' => 'as',
                'State' => 'as',
                'PostalCode' => 'as',
                'Phone' => 'as',
                'CountryCode' => 'ID',

            ],

            'Sellers' => [
                'Id' => 'as',
                'Name' => 'as',
                'SelleridNumber' => 'as',
                'Email' => 'as',
                'Address' => [
                    'FirstName' => 'wd',
                    'LastName' => 'wd',
                    'Address' => 'wd',
                    'City' => 'wd',
                    'State' => 'wd',
                    'PostalCode' => 'wd',
                    'Phone' => 'wd',
                    'CountryCode' => 'ID',
                ]

            ],
        ];
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

        $str_sign = '||' . implode('||', $signer) . '||';
        $signature = hash('sha256', $str_sign);
        return $signature;
    }
}
