<?php

namespace App\Providers;

use App\Models\AuditMdaSchedule;
use App\Models\AuditPayroll;
use App\Models\AuditPaySchedule;
use App\Models\AuditSubMdaSchedule;
use App\Models\Bank;
use App\Models\BeneficiaryType;
use App\Models\Domain;
use App\Models\MicroFinanceBank;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerLengthAwarePaginator();
    }

    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('super_admin')) {
                return true;
            }
        });

        Event::listen(Registered::class, SendEmailVerificationNotification::class);

        Relation::$morphMap = [
            'domain'                 => Domain::class,
            'commercial'             => Bank::class,
            'user'                   => User::class,
            'audit_payroll'          => AuditPayroll::class,
            'micro_finance'          => MicroFinanceBank::class,
            'beneficiary_type'       => BeneficiaryType::class,
            'audit_pay_schedule'     => AuditPaySchedule::class,
            'audit_mda_schedule'     => AuditMdaSchedule::class,
            'audit_sub_mda_schedule' => AuditSubMdaSchedule::class,
        ];

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });
    }

    protected function registerLengthAwarePaginator(): void
    {
        $this->app->bind(LengthAwarePaginator::class, function ($app, $values) {
            return new class(...array_values($values)) extends LengthAwarePaginator
            {
                public function only(...$attributes)
                {
                    return $this->transform(function ($item) use ($attributes) {
                        return $item->only($attributes);
                    });
                }

                public function transform($callback)
                {
                    $this->items->transform($callback);

                    return $this;
                }

                public function toArray()
                {
                    return [
                        'data'  => $this->items->toArray(),
                        'links' => $this->links(),
                    ];
                }

                public function links($view = null, $data = [])
                {
                    $this->appends(RequestFacade::all());

                    $window = UrlWindow::make($this);

                    $elements = array_filter([
                        $window['first'],
                        is_array($window['slider']) ? '...' : null,
                        $window['slider'],
                        is_array($window['last']) ? '...' : null,
                        $window['last'],
                    ]);

                    return Collection::make($elements)->flatMap(function ($item) {
                        if (is_array($item)) {
                            return Collection::make($item)->map(function ($url, $page) {
                                return [
                                    'url'    => $url,
                                    'label'  => $page,
                                    'active' => $this->currentPage() === $page,
                                ];
                            });
                        } else {
                            return [
                                [
                                    'url'    => null,
                                    'label'  => '...',
                                    'active' => false,
                                ],
                            ];
                        }
                    })->prepend([
                        'url'    => $this->previousPageUrl(),
                        'label'  => 'Previous',
                        'active' => false,
                    ])->push([
                        'url'    => $this->nextPageUrl(),
                        'label'  => 'Next',
                        'active' => false,
                    ]);
                }
            };
        });
    }
}
