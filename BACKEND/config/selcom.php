<?php
    return [
        "vendor"            => env("SELCOM_VENDOR"),
        "key"               => env("SELCOM_KEY"),
        "secret"            => env("SELCOM_SECRET"),
        "base_url"          => env("SELCOM_BASE_URL"),
        "redirect_url"      => env("SELCOM_REDIRECT_URL"),
        "cancel_url"        => env("SELCOM_CANCEL_URL"),
        "webhook"           => env("SELCOM_WEBHOOK"),
        "order_expiry"      => env("SELCOM_ORDER_EXPIRY", 15),
        "payment_methods"   => env("SELCOM_PAYMENT_METHODS", "ALL")
    ];
?>