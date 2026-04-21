<?php
/**
 * Base Controller
 * Description: Shared controller utilities and common traits for all HTTP controllers.
 * This header documents the purpose of the base class.
 */
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
