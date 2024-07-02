<?php

namespace Extension\GiftCard\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class CodeGenerator extends AbstractHelper
{
    /**
     * @param $code_length
     * @return string
     */
    public function generateGiftCode($code_length)
    {
        $characters = 'ABCDEFGHIJKLMLOPQRSTUVXYZ0123456789';
        $code = '';

        $characters_length = strlen($characters);
        for ($i = 0; $i < $code_length; $i++) {
            $random_index = rand(0, $characters_length - 1);
            $code .= $characters[$random_index];
        }

        return $code;
    }
}
