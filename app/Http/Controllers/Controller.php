<?php

namespace App\Http\Controllers;

use App\Traits\AjaxResponse;
use App\Traits\FlashMessages;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    use FlashMessages, AjaxResponse, ValidatesRequests;
}
