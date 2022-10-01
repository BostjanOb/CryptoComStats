<?php

namespace App;

class Platform
{
    const CDC = 'cdc';
    const NEXO = 'nexo';
    const BINANCE_EARN = 'binance_earn';
    const BINANCE_CARD = 'binance_card';
    const YOUHODLER = 'youhodler';

    const PLATFORMS = [
        self::CDC          => 'Crypto.com',
        self::NEXO         => 'Nexo',
        self::BINANCE_EARN => 'Binance Earn',
        self::BINANCE_CARD => 'Binance Card',
        self::YOUHODLER    => 'YouHodler',
    ];
}
