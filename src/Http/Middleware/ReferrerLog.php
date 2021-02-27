<?php

namespace NormanHuth\ReferrerLogging\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use NormanHuth\ReferrerLogging\Models\ReferrerHost;

class ReferrerLog
{
    protected ?string $referrer;
    protected string $target;
    protected ?string $userIp;
    protected string $userMac;
    protected ?string $userAgent;
    protected string $key;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->referrer = $request->headers->get('referer');

        if ($this->referrer && $this->getHost($this->referrer) != $this->getHost(config('app.url', $_SERVER['SERVER_NAME'])) && $this->getHost($this->referrer) != $_SERVER['SERVER_ADDR']) {
            $this->target = $request->path();
            $this->userIp = $request->ip();
            $this->userMac = $this->getMAcAddressExec();
            $this->userAgent = $request->server('HTTP_USER_AGENT');

            $this->key = md5($this->referrer.$this->target).'-'.md5($this->userIp.$this->userMac.$this->userAgent);

            if (!$this->isLogged()) {
                $host = ReferrerHost::firstOrCreate([
                    'host' => $this->getHost($this->referrer)
                ]);
                $host->update(['count' => DB::raw('count+1')]);
                $host->referrers()->create([
                    'referrer' => $this->referrer,
                    'target'   => $this->target,
                ]);
                $this->setThrottle();
            }
        }

        return $next($request);
    }

    protected function getMAcAddressExec(): string
    {
        return substr(exec('getmac'), 0, 17);
    }

    protected function setThrottle()
    {
        $method = config('referrer-logging.throttle.method');
        if ($method == 'file') {
            Storage::disk('referrer-cache')->put($this->key, now()->toString());
        }

        if ($method == 'cache') {
            $seconds = config('referrer-logging.throttle.seconds', 28800);
            Cache::put('ref-'.$this->key, now()->toString(), $seconds);
        }
    }

    protected function isLogged(): bool
    {
        if (config('referrer-logging.throttle.method') == 'file') {
            return Storage::disk('referrer-cache')->exists($this->key);
        }

        return Cache::has('ref-'.$this->key);
    }

    protected function getHost($url): string
    {
        $parse = parse_url($url, PHP_URL_HOST);
        $parse = str_starts_with($parse, 'www.') ? substr($parse, 4) : $parse;

        return $parse;
    }
}
