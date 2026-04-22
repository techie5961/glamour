<?php
function Banks() {
    $banks = [
        'access_bank' => [
            'name' => 'Access Bank',
            'code' => '044',
        ],
        'gt_bank' => [
            'name' => 'Guaranty Trust Bank',
            'code' => '058',
        ],
        'zenith_bank' => [
            'name' => 'Zenith Bank',
            'code' => '057',
        ],
        'first_bank' => [
            'name' => 'First Bank of Nigeria',
            'code' => '011',
        ],
        'uba' => [
            'name' => 'United Bank for Africa',
            'code' => '033',
        ],
        'fidelity_bank' => [
            'name' => 'Fidelity Bank',
            'code' => '070',
        ],
        'sc_mobile' => [
            'name' => 'Standard Chartered bank',
            'code' => '068',
        ],
        'fcmb' => [
            'name' => 'First City Monument Bank',
            'code' => '214',
        ],
        'union_bank' => [
            'name' => 'Union Bank of Nigeria',
            'code' => '032',
        ],
        'wema_bank' => [
            'name' => 'Wema Bank',
            'code' => '035',
        ],
        'sterling_bank' => [
            'name' => 'Sterling Bank',
            'code' => '232',
        ],
        'keystone_bank' => [
            'name' => 'Keystone Bank',
            'code' => '082',
        ],
        'polaris_bank' => [
            'name' => 'Polaris Bank',
            'code' => '076',
        ],
        'stanbic_ibtc' => [
            'name' => 'Stanbic IBTC Bank',
            'code' => '221',
        ],
        'ecobank' => [
            'name' => 'Ecobank Nigeria',
            'code' => '050',
        ],
        'heritage_bank' => [
            'name' => 'Heritage Bank',
            'code' => '030',
        ],
        'unity_bank' => [
            'name' => 'Unity Bank',
            'code' => '215',
        ],
        'taj_bank' => [
            'name' => 'TAJ Bank',
            'code' => '000026',
        ],
        'suntrust_bank' => [
            'name' => 'SunTrust Bank',
            'code' => '100',
        ],
        'rubies_bank' => [
            'name' => 'Rubies Bank',
            'code' => '090175',
        ],
        'kuda_bank' => [
            'name' => 'Kuda Microfinance Bank',
            'code' => '090267',
        ],
        'vfd_microfinance' => [
            'name' => 'VFD Microfinance Bank',
            'code' => '090110',
        ],
        'opay' => [
            'name' => 'Opay',
            'code' => '100004',
        ],
        'palmpay' => [
            'name' => 'PalmPay',
            'code' => '100033',
        ],
        'moniepoint' => [
            'name' => 'Moniepoint MFB',
            'code' => '090405',
        ],
        'fairmoney' => [
            'name' => 'FairMoney MFB',
            'code' => '090551',
        ],
        'sparkle' => [
            'name' => 'Sparkle Microfinance Bank',
            'code' => '090325',
        ],
        'mint_mfb' => [
            'name' => 'Mint MFB',
            'code' => '090281',
        ],
        'ab_microfinance' => [
            'name' => 'AB Microfinance Bank',
            'code' => '090270',
        ],
        'eye_mfb' => [
            'name' => 'Eye Microfinance Bank',
            'code' => '090115',
        ],
        'carbon' => [
            'name' => 'Carbon',
            'code' => '100026',
        ],
        'paystack' => [
            'name' => 'Paystack Payments Ltd',
            'code' => '110006',
        ],
        'flutterwave' => [
            'name' => 'Flutterwave Tech Solutions Ltd',
            'code' => '110002',
        ],
        'providence_bank' => [
            'name' => 'Providus Bank',
            'code' => '101',
        ],
        'parallex_bank' => [
            'name' => 'Parallex Bank',
            'code' => '000030',
        ],
        'globus_bank' => [
            'name' => 'Globus Bank',
            'code' => '000027',
        ],
        'premium_trust' => [
            'name' => 'PremiumTrust Bank',
            'code' => '000031',
        ],
        'lotus_bank' => [
            'name' => 'Lotus Bank',
            'code' => '000029',
        ],
        'rand_merchant' => [
            'name' => 'Rand Merchant Bank',
            'code' => '502',
        ],
        'greenwich' => [
            'name' => 'Greenwich Merchant Bank',
            'code' => '060004',
        ],
        'citi_bank' => [
            'name' => 'Citibank Nigeria',
            'code' => '023',
        ],
        'jaiz_bank' => [
            'name' => 'Jaiz Bank',
            'code' => '301',
        ],
        'novel_mfb' => [
            'name' => 'NOVEL MFB',
            'code' => '090775',
        ],
        'mkudi' => [
            'name' => 'Mkudi MFB',
            'code' => '100011',
        ],
        'bowen_mfb' => [
            'name' => 'Bowen Microfinance Bank',
            'code' => '090148',
        ],
        'infinity_mfb' => [
            'name' => 'Infinity MFB',
            'code' => '090157',
        ],
        'esso_mfb' => [
            'name' => 'Esso Microfinance Bank',
            'code' => '090189',
        ],
        'safehaven_mfb' => [
            'name' => 'Safe Haven MFB',
            'code' => '090286',
        ],
        'paga' => [
            'name' => 'Paga',
            'code' => '327',
        ],
        'bainescredit' => [
            'name' => 'Bainescredit MFB',
            'code' => '090188',
        ],
        'petra_mfb' => [
            'name' => 'Petra Microfinance Bank',
            'code' => '090165',
        ]
    ];
    
    ksort($banks);
    return json_decode(json_encode($banks));
}