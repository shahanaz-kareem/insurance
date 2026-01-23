<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
        // InsuraModelNotFoundException::class, // uncomment only if class exists
    ];

    public function report(Throwable $e)
    {
        parent::report($e);
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }

        // Uncomment if your custom exception exists
        /*
        if ($e instanceof InsuraModelNotFoundException) {
            if ($e->getBack()) {
                return response($e->getMessage(), 404);
            } else {
                return redirect()->back()->withErrors([
                    $e->getMessage()
                ])->withInput();
            }
        }
        */

        return parent::render($request, $e);
    }
}
