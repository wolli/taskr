<?php
/**
 * @package Taskr
 * @subpackage Util
 * @author Peeter P. Mõtsküla <peeterpaul@motskula.net>
 * @todo copyright & license
 * @version 0.1.0
 */
/**
 * Class Taskr_Util contains various static utility functions
 */
class Taskr_Util
{
    /**
     * Converts date string to Unix timestamp
     *
     * Supported $dateString formats: YYYY-MM-DD YYMMDD DDMMMYY DDMMM
     * Returns Unix timestamp, or NULL if $dateString was empty,
     * or FALSE if $dateString was invalid
     *
     * @param string $dateString
     * @param int $tzDiff seconds OPTIONAL (defaults to 0 = UTC)
     * @return mixed int Unix timestamp|NULL|bool FALSE
     */
    public static function dateToTs($dateString, $tzDiff = 0)
    {
        // trim $dateString, bail out if result is empty string;
        if ('' == $dateString = trim($dateString)) {
            return NULL;
        }

        // define search patterns
        $months = 'jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec';
        $formats = array(
            'YYYY-MM-DD'    => '/^(\d{4})-(\d{2})-(\d{2})$/',
            'YYMMDD'        => '/^(\d{2})(\d{2})(\d{2})$/',
            'DDMMMYY'       => "/^(\d{2})($months)(\d{2})$/i",
            'DDMMM'         => "/^(\d{2})($months)$/i",
        );
        $matches = array();

        // do matches one by one
        if (preg_match($formats['YYYY-MM-DD'], $dateString, $matches)) {
            $year = $matches[1];
            $month = $matches[2];
            $day = $matches[3];
        } elseif (preg_match($formats['YYMMDD'], $dateString, $matches)) {
            $year = $matches[1] + 2000;
            $month = $matches[2];
            $day = $matches[3];
        } elseif (preg_match($formats['DDMMMYY'], $dateString, $matches)) {
            $day = $matches[1];
            $month = stripos($months, $matches[2]) / 4 + 1;
            $year = $matches[3] + 2000;
        } elseif (preg_match($formats['DDMMM'], $dateString, $matches)) {
            $day = $matches[1];
            $month = stripos($months, $matches[2]) / 4 + 1;
            $now = getdate();
            $year = $now['year'];

            // use next year if DDMMM refers to a date that has already passed
            if ($month < $now['mon']
                || ($month == $now['mon'] && $day < $now['mday'])
            ) {
                $year++;
            }
        } else {
            // none of the formats matched
            return FALSE;
        }

        // construct result value
        $result = gmmktime(0, 0, 0, $month, $day, $year);

        // test parameters for validity
        $date = getdate($result);
        if ($date['year'] != $year || $date['mon'] != $month
                || $date['mday'] != $day) {
            return FALSE;
        }

        return $result-$tzDiff;
    }

}