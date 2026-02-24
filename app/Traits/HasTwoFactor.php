<?php

namespace App\Traits;

use PragmaRX\Google2FALaravel\Support\Authenticator;

trait HasTwoFactor
{
    public function isTwoFactorEnabled(): bool
    {
        return $this->two_factor_secret !== null;
    }

    public function enableTwoFactor()
    {
        $google2fa = app('pragmarx.google2fa');
        $this->two_factor_secret = $google2fa->generateSecretKey();
        $this->save();
    }
}
