<?php

namespace Elimuswift\Tenancy\Middleware;

use Closure;
use Elimuswift\Tenancy\Resolver;
use Elimuswift\Tenancy\Contracts\CurrentHostname;
use Elimuswift\Tenancy\Events\Hostnames\NoneFound;
use Elimuswift\Tenancy\Events\Hostnames\Redirected;
use Elimuswift\Tenancy\Events\Hostnames\Secured;
use Elimuswift\Tenancy\Events\Hostnames\UnderMaintenance;
use Elimuswift\Tenancy\Models\Hostname;
use Elimuswift\Tenancy\Contracts\Repositories\HostnameRepository;
use Elimuswift\Tenancy\Traits\DispatchesEvents;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class HostnameActions
{
    use DispatchesEvents;
    /**
     * @var CurrentHostname|Hostname
     */
    protected $hostname;

    /**
     * @var Redirector
     */
    protected $redirect;

    /**
     * @param CurrentHostname $hostname
     * @param Redirector      $redirect
     */
    public function __construct(HostnameRepository $hostname, Redirector $redirect)
    {
        $this->hostname = $hostname;
        $this->redirect = $redirect;
    }

    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        //dd(config('database'));
        if ($this->hostname = app(Resolver::class)->resolvedHost()) {
            if ($this->hostname->under_maintenance_since) {
                return $this->maintenance($this->hostname);
            }

            if ($this->hostname->redirect_to) {
                return $this->redirect($this->hostname);
            }

            if (!$request->secure() && $this->hostname->force_https) {
                return $this->secure($this->hostname, $request);
            }
        } else {
            return $this->abort($request);
        }

        return $next($request);
    }

    /**
     * @param Hostname $hostname
     *
     * @return RedirectResponse
     */
    protected function redirect(Hostname $hostname)
    {
        $this->emitEvent(new Redirected($hostname));

        return $this->redirect->away($hostname->redirect_to);
    }

    /**
     * @param Hostname $hostname
     * @param Request  $request
     *
     * @return RedirectResponse
     */
    protected function secure(Hostname $hostname, Request $request)
    {
        $this->emitEvent(new Secured($hostname));

        return $this->redirect->secure($request->path());
    }

    /**
     * @param Hostname $hostname
     */
    protected function maintenance(Hostname $hostname)
    {
        $this->emitEvent(new UnderMaintenance($hostname));

        throw new MaintenanceModeException($hostname->under_maintenance_since->timestamp);
    }

    /**
     * Aborts the application.
     *
     * @param Request $request
     */
    protected function abort(Request $request)
    {
        if (config('tenancy.hostname.abort-without-identified-hostname')) {
            event(new NoneFound($request));

            return abort(404);
        }
    }
}
