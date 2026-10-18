<?php

namespace App\Helpers;

trait Helper
{
    public function ddA($arrayOrObject)
    {
        dd(collect($arrayOrObject)->toArray());
    }
}
