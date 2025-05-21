<?php

namespace App;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\ServiceAccount;
use Firebase\Auth\Token\Exception\InvalidToken;

class FirebaseAuth
{
    private Auth $auth;

    public function __construct(string $jsonCredentials)
    {
        $serviceAccount = ServiceAccount::fromValue(json_decode($jsonCredentials, true));
        $factory = (new Factory)->withServiceAccount($serviceAccount);
        $this->auth = $factory->createAuth();
    }

    public function verifyToken(string $idToken): array
    {
        try {
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
            $uid = $verifiedIdToken->claims()->get('sub');
            $user = $this->auth->getUser($uid);

            return [
                'uid' => $user->uid,
                'email' => $user->email,
                'displayName' => $user->displayName,
                'photoURL' => $user->photoUrl,
            ];
        } catch (InvalidToken $e) {
            throw new \Exception("Token inválido");
        }
    }
}
