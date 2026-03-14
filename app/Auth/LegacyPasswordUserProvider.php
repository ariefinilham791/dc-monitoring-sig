<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Support\Facades\Hash;

class LegacyPasswordUserProvider extends EloquentUserProvider
{
    /**
     * Validate credentials, supporting legacy (non-Bcrypt) hashes.
     * Tries: Bcrypt → MD5 → SHA256 → plain. On legacy match, upgrades to Bcrypt.
     */
    public function validateCredentials(UserContract $user, array $credentials): bool
    {
        $plain = $credentials['password'] ?? null;
        $hashed = $user->getAuthPassword();

        if ($plain === null || $hashed === null || $hashed === '') {
            return false;
        }

        // Only use Laravel's check when PHP recognizes the hash as real Bcrypt (avoids throw on invalid/corrupt $2y$ strings)
        $info = password_get_info($hashed);
        if (($info['algo'] ?? null) !== null && ($info['algoName'] ?? '') === 'bcrypt') {
            return parent::validateCredentials($user, $credentials);
        }

        // Legacy: MD5, SHA256, or plain
        $valid = false;
        if (hash_equals($hashed, md5($plain))) {
            $valid = true;
        } elseif (strlen($hashed) === 64 && hash_equals($hashed, hash('sha256', $plain))) {
            $valid = true;
        } elseif (hash_equals($hashed, $plain)) {
            $valid = true;
        }

        if ($valid && $user instanceof \Illuminate\Database\Eloquent\Model) {
            $user->forceFill([
                $user->getAuthPasswordName() => Hash::make($plain),
            ])->save();
        }

        return $valid;
    }
}
