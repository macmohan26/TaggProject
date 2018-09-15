<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Stripe\Error as Stripe;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof Stripe\InvalidRequest) {

            return redirect()->back()->withErrors([' The coupon code entered is not valid, please enter a valid code']);

        }

        elseif ($exception instanceof ErrorException ) {

            // flash your message

            \Session::flash('flash_message_important', 'Sorry, your session seems to have expired. Please try again.');

            return redirect('login');
        }

        elseif ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            return redirect()->back()->withInput()->with('token', csrf_token());
        }
        elseif ($exception instanceof QueryException) {
            //\Session::flash('flash_message_important', 'Bad Data. Use different email address');
            return back()->withErrors('Email ID already taken');
        }

        return parent::render($request, $exception);
    }
}
