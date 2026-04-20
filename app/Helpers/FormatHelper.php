<?php

namespace App\Helpers;

class FormatHelper
{
    public static function onlyNumbers(string $value): string
    {
        return preg_replace("/\D/", '', $value);
    }

    public static function completeWithZeros(string $value, int $size): string
    {
        return str_pad($value, $size, '0', STR_PAD_LEFT);
    }

    public static function formatCnpjCpf(string $value): string
    {
        $CPF_LENGTH = 11;
        $cnpj_cpf   = FormatHelper::onlyNumbers($value); //preg_replace("/\D/", '', $value);

        if (strlen($cnpj_cpf) === $CPF_LENGTH) {
            $cnpj_cpf = FormatHelper::completeWithZeros($value, 11);

            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", '$1.$2.$3-$4', $cnpj_cpf);
        }

        $cnpj_cpf = FormatHelper::completeWithZeros($value, 14);

        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", '$1.$2.$3/$4-$5', $cnpj_cpf);
    }

    public static function formatFone(string $value): string
    {
        $FONE_LENGTH = 11;
        $fone        = FormatHelper::onlyNumbers($value); //preg_replace("/\D/", '', $value);

        if (strlen($fone) === $FONE_LENGTH) {
            return preg_replace("/(\d{2})(\d{5})(\d{4})/", '($1) $2-$3', $fone);
        }

        return preg_replace("/(\d{2})(\d{4})(\d{4})/", '($1) $2-$3', $fone);
    }

    public static function brToUS(string $value): float
    {
        $br = ['.', ',']; // substitui esse
        $us = ['', '.'];  // por esse

        return floatval(str_replace($br, $us, $value));
    }

    public static function validateCpf(string $value): bool
    {
        if (strlen($value) > 11) {
            return false;
        }
        $cpf = FormatHelper::onlyNumbers($value);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        for ($t = 9; $t < 11; $t++) {
            $d = 0;

            for ($c = 0; $c < $t; $c++) {
                $d += ((int) $cpf[$c]) * (($t + 1) - $c);
            }

            $d = ((10 * $d) % 11) % 10;

            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }
}
