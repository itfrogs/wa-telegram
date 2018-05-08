<?php

return array(
    'settings/payment/?'                                => 'settings/payment',
    'settings/payment/add/<company_id>/<plugin_id>/?'   => 'settings/paymentEdit',
    'settings/payment/<instance_id>/?'                  => 'settings/paymentEdit',
    'settings/sms/?'                                    => 'settings/sms',
    'settings/'                                         => 'settings/general',
    'plugins/?'                                         => 'plugins/',
    'plugins/<plugin_id>/*'                             => 'plugins/',
    ''                                                  => 'backend/',
);
