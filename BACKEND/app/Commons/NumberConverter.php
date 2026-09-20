<?php

namespace App\Commons;

class NumberConverter
{
    public static function convertNumber($num)
    {

        $num    = (string) ((int) $num);

        if ((int) ($num) && ctype_digit($num)) {
            $words  = array();

            $num    = str_replace(array(',', ' '), '', trim($num));

            $list1  = array(
                '',
                'one',
                'two',
                'three',
                'four',
                'five',
                'six',
                'seven',
                'eight',
                'nine',
                'ten',
                'eleven',
                'twelve',
                'thirteen',
                'fourteen',
                'fifteen',
                'sixteen',
                'seventeen',
                'eighteen',
                'nineteen'
            );

            $list2  = array(
                '',
                'ten',
                'twenty',
                'thirty',
                'forty',
                'fifty',
                'sixty',
                'seventy',
                'eighty',
                'ninety',
                'hundred'
            );

            $list3  = array(
                '',
                'thousand',
                'million',
                'billion',
                'trillion',
                'quadrillion',
                'quintillion',
                'sextillion',
                'septillion',
                'octillion',
                'nonillion',
                'decillion',
                'undecillion',
                'duodecillion',
                'tredecillion',
                'quattuordecillion',
                'quindecillion',
                'sexdecillion',
                'septendecillion',
                'octodecillion',
                'novemdecillion',
                'vigintillion'
            );

            $num_length = strlen($num);
            $levels = (int) (($num_length + 2) / 3);
            $max_length = $levels * 3;
            $num    = substr('00' . $num, -$max_length);
            $num_levels = str_split($num, 3);

            foreach ($num_levels as $num_part) {
                $levels--;
                $hundreds   = (int) ($num_part / 100);
                $hundreds   = ($hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ($hundreds == 1 ? '' : '') . ' ' : '');
                $tens       = (int) ($num_part % 100);
                $singles    = '';

                if ($tens < 20) {
                    $tens   = ($tens ? ' ' . $list1[$tens] . ' ' : '');
                } else {
                    $tens   = (int) ($tens / 10);
                    $tens   = ' ' . $list2[$tens] . ' ';
                    $singles    = (int) ($num_part % 10);
                    $singles    = ' ' . $list1[$singles] . ' ';
                }
                $words[]    = $hundreds . $tens . $singles . (($levels && (int) ($num_part)) ? ' ' . $list3[$levels] . ' ' : '');
            }

            $commas = count($words);

            if ($commas > 1) {
                $commas = $commas - 1;
            }

            $words  = implode(', ', $words);

            //Some Finishing Touch
            //Replacing multiples of spaces with one space
            $words  = trim(str_replace(' ,', ',', static::trim_all(ucwords($words))), ', ');
            if ($commas) {
                $words  = static::str_replace_last(',', ' ', $words);
            }

            return $words;
        } else if (! ((int) $num)) {
            return 'Zero';
        }
        return '';
    }


    protected static function trim_all($str, $what = NULL, $with = ' ')
    {
        if ($what === NULL) {
            //  Character      Decimal      Use
            //  "\0"            0           Null Character
            //  "\t"            9           Tab
            //  "\n"           10           New line
            //  "\x0B"         11           Vertical Tab
            //  "\r"           13           New Line in Mac
            //  " "            32           Space

            $what   = "\\x00-\\x20";    //all white-spaces and control chars
        }

        return trim(preg_replace("/[" . $what . "]+/", $with, $str), $what);
    }

    protected static function str_replace_last($search, $replace, $str)
    {
        if (($pos = strrpos($str, $search)) !== false) {
            $search_length  = strlen($search);
            $str    = substr_replace($str, $replace, $pos, $search_length);
        }
        return $str;
    }



    public static function convertDecimals($amount)
    {
        $explodedInvoiceAmount = explode(".", $amount);
        $num = 0;
        if (count($explodedInvoiceAmount) > 1) {
            $num = last($explodedInvoiceAmount);
            $num  = last($explodedInvoiceAmount);
            $num = strlen($num) > 2 ? substr($num, 0, 2) : $num;
            $num = (strlen($num) == 1 && (int)$num < 10) ? number_format($num . '0') : number_format($num);
            //dd($num);
            $ones = array(
                1 => "one",
                2 => "two",
                3 => "three",
                4 => "four",
                5 => "five",
                6 => "six",
                7 => "seven",
                8 => "eight",
                9 => "nine",
                10 => "ten",
                11 => "eleven",
                12 => "twelve",
                13 => "thirteen",
                14 => "fourteen",
                15 => "fifteen",
                16 => "sixteen",
                17 => "seventeen",
                18 => "eighteen",
                19 => "nineteen"
            );
            $tens = array(
                1 => "ten",
                2 => "twenty",
                3 => "thirty",
                4 => "forty",
                5 => "fifty",
                6 => "sixty",
                7 => "seventy",
                8 => "eighty",
                9 => "ninety"
            );

            $rettxt = "";
            if ($num < 20) {
                $rettxt .= $ones[$num];
            } elseif ($num < 100) {
                $rettxt .= $tens[substr($num, 0, 1)];
                $rettxt .=  isset($ones[substr($num, 1, 1)]) ? " " . $ones[substr($num, 1, 1)] : '';
            }
            return $rettxt;
        }

        return 'Zero';
    }


    public static function toWords($amount, $currency)
    {

        //$currency = $usd ? 'dollars' : 'shillings';


        $wholeNumber = self::convertNumber(head(explode('.', $amount)));
        $decimal =  self::convertDecimals($amount);
        return $wholeNumber . ' ' . $currency . ' And ' . $decimal . ' Cents Only';
    }


    public static function ltrsToWords($amount, $units)
    {

        //$currency = $usd ? 'dollars' : 'shillings';
        $amount = round($amount, 2);

        $wholeNumber = self::convertNumber(head(explode('.', $amount)));
        $decimal =  self::convertDecimals($amount);
        return $wholeNumber . ' ' . $units . ' And ' . $decimal . ' points Only';
    }
}
