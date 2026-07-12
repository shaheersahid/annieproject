<?php

namespace App\Enums;

enum ProductTypeEnum: string
{
    case Frame = 'frame';
    case Lens = 'lens';
    case Accessory = 'accessory';
    case Service = 'service';
}
