<?

namespace Local\Core\Inner;


class XorCipher
{
    /**
     * Шифрует
     *
     * @param $plaintext
     * @param $key
     *
     * @return string
     */
    public static function encrypt($plaintext)
    {
        $key = self::text2ascii( \Bitrix\Main\Config\Configuration::getInstance()->get('local.core')['xor_key'] );
        $plaintext = self::text2ascii($plaintext);
        $keysize = count($key);
        $input_size = count($plaintext);
        $cipher = "";

        for ($i = 0; $i < $input_size; $i++) {
            $cipher .= chr($plaintext[$i] ^ $key[$i % $keysize]);
        }
        return $cipher;
    }

    /**
     * Дешифрует
     *
     * @param $cipher
     * @param $key
     *
     * @return string
     */
    public static function decrypt($cipher)
    {
        $key = self::text2ascii( \Bitrix\Main\Config\Configuration::getInstance()->get('local.core')['xor_key'] );
        $cipher = self::text2ascii($cipher);
        $keysize = count($key);
        $input_size = count($cipher);
        $plaintext = "";

        for ($i = 0; $i < $input_size; $i++) {
            $plaintext .= chr($cipher[$i] ^ $key[$i % $keysize]);
        }
        return $plaintext;
    }

    private static function text2ascii($text)
    {
        return array_map('ord', str_split($text));
    }

    private static function ascii2text($ascii)
    {
        $text = "";
        foreach ($ascii as $char) {
            $text .= chr($char);
        }
        return $text;
    }
}