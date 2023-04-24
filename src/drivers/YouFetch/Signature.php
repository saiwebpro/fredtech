<?php

namespace MirazMac\YouFetch;

use MirazMac\YouFetch\Exceptions\SignatureException;
use \Requests_Cookie_Jar;
use \Requests_Session;

/**
* YouTube Signature Decryption Library
*
* Parses and decrypts YouTube player's cipher decryption algorithm. 
* The class is mean to be used by the core library, thus it should not be used directly.
*
* @author MirazMac <mirazmac@gmail.com>
* @version 0.1 Early Access
* @package MirazMac\YouFetch
*/
class Signature
{
    /**
     * The player ID for the video
     *
     * @var string
     */
    protected $playerID;

    /**
     * The player URI of the video
     *
     * @var string
     */
    protected $playerUri;

    /**
     * The player sts value of the video
     *
     * @var string
     */
    protected $playerSts;

    /**
     * The ciphered signature
     *
     * @var string
     */
    protected $cipheredSignature;

    /**
     * The parsed signature decryption key
     *
     * @var string
     */
    protected $decryptionKey;

    /**
     * Create a new instance
     *
     * @param string $cipheredSignature The ciphered signature
     * @param string $playerUri         The player URI of the video
     */
    public function __construct($cipheredSignature, $playerUri)
    {
        $playerID = $this->extractPlayerID($playerUri);
        if (!$playerID) {
            throw new \InvalidArgumentException("Invalid player URI provided!");
        }

        $this->cipheredSignature = trim($cipheredSignature);
        $this->playerID = $playerID;
        $this->playerUri = $playerUri;

        Cache::setStoragePath(__DIR__ . '/Signatures');
    }

    /**
     * Decipher the signature
     *
     * @throws SignatureException If decryption key format is not valid
     * @return string Possibly the deciphered signature
     */
    public function decrypt()
    {
        // Parse JavaScript First
        $this->parseJavaScript();

        $patterns = $this->decryptionKey['decipherPatterns'];
        $deciphers = $this->decryptionKey['deciphers'];

        // The following lines are taken from:
        // https://github.com/jeckman/YouTube-Downloader
        /*
        * PHP script for downloading videos from youtube
        * Copyright (C) 2012-2018  John Eckman
        *
        * This program is free software; you can redistribute it and/or modify
        * it under the terms of the GNU General Public License as published by
        * the Free Software Foundation; either version 2 of the License, or
        * (at your option) any later version.
        *
        * This program is distributed in the hope that it will be useful,
        * but WITHOUT ANY WARRANTY; without even the implied warranty of
        * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        * GNU General Public License for more details.
        *
        * You should have received a copy of the GNU General Public License along
        * with this program; if not, see <http://www.gnu.org/licenses/>.
        */

        // Execute every $patterns with $deciphers dictionary
        $processSignature = str_split($this->cipheredSignature);

        for ($i=0; $i < count($patterns); $i++) {
            // This is the deciphers dictionary, and should be updated if there are different pattern
            // as PHP can't execute javascript

            //Separate commands
            $executes = explode('->', $patterns[$i]);

            // This is parameter b value for 'function(a,b){}'
            $number = intval(str_replace(['(', ')'], '', $executes[1]));
            // Parameter a = $processSignature

            $execute = $deciphers[$executes[0]];

            switch ($execute) {
                case 'a.reverse()':
                    $processSignature = array_reverse($processSignature);
                break;
                case 'var c=a[0];a[0]=a[b%a.length];a[b]=c':
                    $c = $processSignature[0];
                    $processSignature[0] = $processSignature[$number%count($processSignature)];
                    $processSignature[$number] = $c;
                break;
                case 'var c=a[0];a[0]=a[b%a.length];a[b%a.length]=c':
                    $c = $processSignature[0];
                    $processSignature[0] = $processSignature[$number%count($processSignature)];
                    $processSignature[$number%count($processSignature)] = $c;
                break;
                case 'a.splice(0,b)':
                    $processSignature = array_slice($processSignature, $number);
                break;
                default:
                    throw new \LogicException("Decipher dictionary was not found");
                break;
            }
        }

        return implode('', $processSignature);
    }

    /**
     * Extract the player ID from the player URI
     *
     * @param  string $playerUri The player Js URI
     * @return string|boolean
     */
    protected function extractPlayerID($playerUri)
    {
        preg_match('%player(_|-)(.*?)/%', $playerUri, $matches);

        if (empty($matches[2]) || !is_string($matches[2])) {
            return false;
        }

        return $matches[2];
    }

    /**
     * Parses the player JavaScript and extracts the decipher key
     *
     * @throws SignatureException If failed to fetch the player JS file
     * @return boolean
     */
    protected function parseJavaScript()
    {
        // Try fetching from the cache first
        $cachedData = Cache::get($this->playerID);

        // To delete the old datas
        if (is_array($cachedData)) {
            $this->decryptionKey = $cachedData;
            return true;
        } else {
            Cache::delete($this->playerID);
        }

        // If its not present in cache, lets go get it from the player URI
        $decipherScript = Http::getSession()->get($this->playerUri)->body;
        if (empty($decipherScript)) {
            throw new SignatureException("Failed to fetch player javascript file!");
        }

        // The following lines are taken from:
        // https://github.com/jeckman/YouTube-Downloader
        /*
        * PHP script for downloading videos from youtube
        * Copyright (C) 2012-2018  John Eckman
        *
        * This program is free software; you can redistribute it and/or modify
        * it under the terms of the GNU General Public License as published by
        * the Free Software Foundation; either version 2 of the License, or
        * (at your option) any later version.
        *
        * This program is distributed in the hope that it will be useful,
        * but WITHOUT ANY WARRANTY; without even the implied warranty of
        * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        * GNU General Public License for more details.
        *
        * You should have received a copy of the GNU General Public License along
        * with this program; if not, see <http://www.gnu.org/licenses/>.
        */

        $decipherPatterns = explode('.split("")', $decipherScript);
        unset($decipherPatterns[0]);

        foreach ($decipherPatterns as $value) {
            // Make sure it's inside a function and also have join
            $value = explode('.join("")', explode('}', $value)[0]);
            if (count($value) === 2) {
                $value = explode(';', $value[0]);

                // Remove first and last index
                array_pop($value);
                unset($value[0]);

                $decipherPatterns = implode(';', $value);

                break;
            }
        }


        preg_match_all('/(?<=;).*?(?=\[|\.)/', $decipherPatterns, $deciphers);
        if ($deciphers && count($deciphers[0]) >= 2) {
            $deciphers = $deciphers[0][0];
        } else {
            throw new \LogicException('Failed to get deciphers function');
            return false;
        }

        $deciphersObjectVar = $deciphers;
        $decipher = explode($deciphers . '={', $decipherScript)[1];
        $decipher = str_replace(["\n", "\r"], '', $decipher);
        $decipher = explode('}};', $decipher)[0];
        $decipher = explode('},', $decipher);


        // Convert deciphers to object
        $deciphers = [];

        foreach ($decipher as &$function) {
            $deciphers[explode(':function', $function)[0]] = explode('){', $function)[1];
        }

        // Convert pattern to array
        $decipherPatterns = str_replace($deciphersObjectVar . '.', '', $decipherPatterns);
        $decipherPatterns = str_replace($deciphersObjectVar . '[', '', $decipherPatterns);
        $decipherPatterns = str_replace(['](a,', '(a,'], '->(', $decipherPatterns);
        $decipherPatterns = explode(';', $decipherPatterns);

        $parsed = [
            'decipherPatterns' => $decipherPatterns,
            'deciphers' => $deciphers,
        ];

        $this->decryptionKey = $parsed;

        // Store the decryption key for future use
        Cache::save($this->playerID, $parsed);
        return true;
    }
}
