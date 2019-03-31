<?php

namespace Woody\Http\Message;

use Woody\Http\Message\Cookie;

/**
 * Class CookieFactory
 *
 * @package Woody\Http\Message
 */
class CookieFactory
{

    /**
     * @param string $header
     *
     * @return \Woody\Http\Message\Cookie|null
     *
     * @throws \Exception
     */
    public function create(string $header): ?Cookie
    {
        $result = [];

        foreach (preg_split('/(\s*;\s*)/', trim($header)) as $part) {
            $match = [];

            if (preg_match('/^\s*(?P<name>.*?)(=(?P<quote>"?)(?P<value>.*)(?P=quote))?$/', $part, $match)) {
                switch (mb_strtolower($match['name'])) {
                    case 'path':
                    case 'domain':
                    case 'secure':
                    case 'httponly':
                    case 'samesite':
                    case 'max-age':
                        $result[mb_strtolower($match['name'])] = $match['value'] ?? true;
                        break;

                    case 'expires':
                        $expires = new \DateTime($match['value']);
                        $result[mb_strtolower($match['name'])] = $expires->getTimestamp();
                        break;

                    default:
                        $result['cookies'][$match['name']] = urldecode($match['value']);
                }

                // If both (Expires and Max-Age) are set, Max-Age will have precedence.
                if (!empty($result['max-age'])) {
                    $expires = new \DateTime('+'.$result['max-age'].' seconds');
                    $result['expires'] = $expires->getTimestamp();
                }
            }
        }

        if (!empty($result['cookies'])) {
            $cookie = new Cookie(
                $result['cookies'],
                $result['expires'] ?? null,
                $result['path'] ?? null,
                $result['domain'] ?? null,
                $result['secure'] ?? null,
                $result['httponly'] ?? null,
                $result['samesite'] ?? null
            );

            return $cookie;
        }

        return null;
    }
}
