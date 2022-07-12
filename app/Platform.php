<?php

namespace App;

class Platform
{
    const CDC = 'cdc';
    const NEXO = 'nexo';

    const PLATFORMS = [
        self::CDC  => 'Crypto.com',
        self::NEXO => 'Nexo',
    ];
}
