<?php

namespace Elegant\Utils\Authorization\Http\Middleware;

use Elegant\Utils\Facades\Admin;
use Elegant\Utils\Http\Middleware\Pjax;
use Elegant\Utils\Layout\Content;
use Illuminate\Http\Request;

class AuthorizeMiddleware
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        if (!Admin::user() || $this->shouldPassThrough($request)) {
            return $next($request);
        }

        if (Admin::user()->canRoute($request->route())) {
            return $next($request);
        }

        if (!$request->pjax() && $request->ajax()) {
            abort(403, trans('admin.deny'));
            exit;
        }

        Pjax::respond(response(new Content(function (Content $content) {
            $content->title(trans('admin.deny'))->view('admin::pages.deny');
        })));
    }

    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param Request $request
     *
     * @return bool
     */
    protected function shouldPassThrough($request): bool
    {
        return collect(config('elegant-utils.authorization.route.excepts', []))
            ->map('admin_base_path')
            ->contains(function ($except) use ($request) {
                if ($except !== '/') {
                    $except = trim($except, '/');
                }

                return $request->is($except);
            });
    }
}
