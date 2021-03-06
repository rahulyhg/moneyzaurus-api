<?php

namespace Api\Service\Authorization;

use Zend\Crypt\Password\Bcrypt;
use Api\Service\AccessorTrait;

/**
 * Class Crypt
 *
 * @package Api\Service\Authorization
 *
 * @method Crypt  setCrypt(Bcrypt $crypt)
 * @method Bcrypt getCrypt()
 */
class Crypt
{
    use AccessorTrait;

    /**
     * @param string $password
     *
     * @return string
     */
    public function create($password)
    {
        return $this->getCrypt()->create($password);
    }

    /**
     * @param string $password
     * @param string $securePass
     *
     * @return bool
     */
    public function verify($password, $securePass)
    {
        $success = $this->getCrypt()->verify($password, $securePass);

        // backwards compatible md5 password validation
        if (!$success) {
            $success = md5($password) == $securePass;
        }

        return $success;
    }

    /**
     * @return string
     */
    public function getRandomPassword()
    {
        $length = 10;
        $chars  = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        return substr(str_shuffle($chars), 0, $length);
    }
}
