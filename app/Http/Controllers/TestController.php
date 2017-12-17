<?php
namespace Larabase54\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Larabase54\Constants;
use Larabase54\Logger;
use Larabase54\Repository\Repository;

use Validator;

class TestController extends Controller
{
    const TAG = "[TestController]";
    
    private $repository;
    
    public function __construct(Repository $repository)
    {
        //$this->middleware('auth');
        $this->repository = $repository;
        Logger::debug(self::TAG, "construct");
    }
}