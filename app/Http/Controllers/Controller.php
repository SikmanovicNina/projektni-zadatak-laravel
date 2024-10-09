<?php

namespace App\Http\Controllers;

abstract class Controller extends \Illuminate\Routing\Controller
{
    public const PER_PAGE_OPTIONS = [20, 50, 100];
}
