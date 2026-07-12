<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function __call(string $method, array $parameters): mixed
    {
        $view = $this->viewFor($method);

        if (view()->exists($view)) {
            return view($view);
        }

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Action unavailable.'], 501);
        }

        return redirect()->route('home');
    }

    protected function viewFor(string $method): string
    {
        return 'content.index';
    }
}
