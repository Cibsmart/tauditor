<?php

namespace App\Providers;

use App\Bank;
use App\Cadre;
use App\Domain;
use App\Structure;
use App\CadreStep;
use App\FixedValue;
use Inertia\Inertia;
use App\Compute\Tax;
use App\MdaStructure;
use App\AuditPayroll;
use App\ComputedValue;
use App\PercentageValue;
use App\BeneficiaryType;
use App\Compute\Prorate;
use App\MicroFinanceBank;
use App\StructuredSalary;
use App\AuditPaySchedule;
use App\AuditMdaSchedule;
use App\PersonalizedSalary;
use App\AuditSubMdaSchedule;
use Illuminate\Support\Collection;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\Relation;
use function in_array;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerInertia();
        $this->registerBindings();
        $this->registerLengthAwarePaginator();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::$morphMap = [
            'cadre'                  => Cadre::class,
            'domain'                 => Domain::class,
            'commercial'             => Bank::class,
            'cadre_step'             => CadreStep::class,
            'fixed_value'            => FixedValue::class,
            'mda_structure'          => MdaStructure::class,
            'salary_structure'       => Structure::class,
            'audit_payroll'          => AuditPayroll::class,
            'structured'             => StructuredSalary::class,
            'computed_value'         => ComputedValue::class,
            'micro_finance'          => MicroFinanceBank::class,
            'personalized'           => PersonalizedSalary::class,
            'percentage_value'       => PercentageValue::class,
            'beneficiary_type'       => BeneficiaryType::class,
            'audit_pay_schedule'     => AuditPaySchedule::class,
            'audit_mda_schedule'     => AuditMdaSchedule::class,
            'audit_sub_mda_schedule' => AuditSubMdaSchedule::class,
        ];

        Validator::extend('positive', function ($attribute, $value, $parameters, $validator) {
            return $value > 0;
        });
    }

    public function registerBindings()
    {
        $this->app->bind('compute_tax', Tax::class);
        $this->app->bind('compute_prorate', Prorate::class);
    }

    public function registerInertia()
    {
        Inertia::version(function () {
            return md5_file(public_path('mix-manifest.json'));
        });

        Inertia::share([
            'auth'        => function () {
                return [
                    'user' => Auth::user() ? [
                        'id'         => Auth::user()->id,
                        'first_name' => Auth::user()->first_name,
                        'last_name'  => Auth::user()->last_name,
                        'email'      => Auth::user()->email,
                        'domain'     => [
                            'id'   => Auth::user()->domain->id,
                            'name' => Auth::user()->domain->name,
                        ],
                    ] : null,
                ];
            },
            'flash'       => function () {
                return [
                    'success' => Session::get('success'),
                    'error'   => Session::get('error'),
                ];
            },
            'errors'      => function () {
                return Session::get('errors')
                    ? Session::get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
            'permissions' => function () {
                return Auth::user()
                    ? [
                        'canViewDashboard' => Auth::user()->can('view_dashboard'),
                        'canViewMfbSchedule' => Auth::user()->can('view_mfb_schedule'),
                        'canViewAnalysis' => Auth::user()->can('view_analysis'),
                        'canViewAutopay' => Auth::user()->can('view_autopay'),
                        'canViewReports' => Auth::user()->can('view_report'),
                        'canViewSchedule' => Auth::user()->can('view_schedule'),
                        'canViewPaymentSummary' => Auth::user()->can('view_payment_summary'),
                        'canViewCategoryReport' => Auth::user()->can('view_category_report'),
                        'canViewMdaReport' => Auth::user()->can('view_mda_report'),
                        'canViewBeneficiaryReport' => Auth::user()->can('view_beneficiary_report'),
                        'canViewUsers' => Auth::user()->can('view_users'),
                        'canCreateUsers' => Auth::user()->can('create_users'),
                    ] : null;
            },
        ]);
    }

    protected function registerLengthAwarePaginator()
    {
        $this->app->bind(LengthAwarePaginator::class, function ($app, $values) {
            return new class(...array_values($values)) extends LengthAwarePaginator {
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
                    $this->appends(Request::all());

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
