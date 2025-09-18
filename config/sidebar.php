<?php

return [

  'SIDEBAR' => [
    'Dashboard' => [
      'route' => '/admin/dashboard',
      'iconClass' => 'fas fa-tachometer-alt',
      'segment' => 'dashboard'
    ],
    'Channel Category' => [
      'route' => '/admin/category',
      'iconClass' => 'fas fa-list-alt',
      'segment' => 'category'
    ],

    'Subscriber' => [
      'route' => '/admin/subscriber',
      'iconClass' => 'fas fa-user-check',
      'segment' => 'subscriber'
    ],

    'User' => [
      'iconClass' => 'fas fa-users',
      'Influencer' => [
        'route' => '/admin/influencer',
        'iconClass' => 'fas fa-user',
        'segment' => 'influencer'
      ],
      'Business' => [
        'route' =>  '/admin/business',
        'iconClass' => 'fas fa-briefcase',
        'segment' => 'business'
      ],
    ],

    'Static Pages' => [
      'iconClass' => 'fas fa-book',
      'Influencer' => [
        'segment' => 'influencer',
        'iconClass' => 'far fa-circle text-warning',
        'Terms & Condition' => [
          'route' => '/admin/static-page/influencer/terms-condition',
          'iconClass' => 'fa fa-file-contract',
          'segment' => 'influencer/terms-condition'
        ],
        'Privacy policy' => [
          'route' => '/admin/static-page/influencer/privacy-policy',
          'iconClass' => 'fa fa-lock',
          'segment' => 'influencer/privacy-policy'
        ],
        'Data safety' => [
          'route' => '/admin/static-page/influencer/data-safety',
          'iconClass' => 'fas fa-hard-hat',
          'segment' => 'influencer/data-safety'
        ],
        'Refund policy' => [
          'route' => '/admin/static-page/influencer/refund-policy',
          'iconClass' => 'fas fa-exchange-alt',
          'segment' => 'influencer/refund-policy'
        ],
        'Disclaimer' => [
          'route' => '/admin/static-page/influencer/disclaimer',
          'iconClass' => 'fas fa-copyright',
          'segment' => 'influencer/disclaimer'
        ],
        'DMCA policy' => [
          'route' => '/admin/static-page/influencer/dmca-policy',
          'iconClass' => 'fas fa-lock',
          'segment' => 'influencer/dmca-policy'
        ],
        'Cookie Consent' => [
          'route' => '/admin/static-page/influencer/cookie-consent',
          'iconClass' => 'fas fa-cookie',
          'segment' => 'influencer/cookie-consent'
        ],
        'About us' => [
          'route' => '/admin/static-page/influencer/about-us',
          'iconClass' => 'fas fa-info',
          'segment' => 'influencer/about-us'
        ],
      ],
      'Business' => [
        'segment' => 'business',
        'iconClass' => 'far fa-circle text-info',
        'Terms & Condition' => [
          'route' => '/admin/static-page/business/terms-condition',
          'iconClass' => 'fa fa-file-contract',
          'segment' => 'business/terms-condition'
        ],
        'Privacy policy' => [
          'route' => '/admin/static-page/business/privacy-policy',
          'iconClass' => 'fa fa-lock',
          'segment' => 'business/privacy-policy'
        ],
        'Data safety' => [
          'route' => '/admin/static-page/business/data-safety',
          'iconClass' => 'fas fa-hard-hat',
          'segment' => 'business/data-safety'
        ],
        'Refund policy' => [
          'route' => '/admin/static-page/business/refund-policy',
          'iconClass' => 'fas fa-exchange-alt',
          'segment' => 'business/refund-policy'
        ],
        'Disclaimer' => [
          'route' => '/admin/static-page/business/disclaimer',
          'iconClass' => 'fas fa-copyright',
          'segment' => 'business/disclaimer'
        ],
        'DMCA policy' => [
          'route' => '/admin/static-page/business/dmca-policy',
          'iconClass' => 'fas fa-lock',
          'segment' => 'business/dmca-policy'
        ],
        'Cookie Consent' => [
          'route' => '/admin/static-page/business/cookie-consent',
          'iconClass' => 'fas fa-cookie',
          'segment' => 'business/cookie-consent'
        ],
        'About us' => [
          'route' => '/admin/static-page/business/about-us',
          'iconClass' => 'fas fa-info',
          'segment' => 'business/about-us'
        ],
      ],
    ],

    'Contact Us' => [
      'route' => '/admin/contact-us',
      'iconClass' => 'fas fa-phone',
      'segment' => 'contact'
    ],
  ],
];
