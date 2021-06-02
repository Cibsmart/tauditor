<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Http\Request;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'auth'  => function () use ($request) {
                return [
                    'user' => $request->user() ? [
                        'id'         => $request->user()->id,
                        'first_name' => $request->user()->first_name,
                        'last_name'  => $request->user()->last_name,
                        'email'      => $request->user()->email,
                        'domain'    => [
                            'id'   => $request->user()->domain->id,
                            'name' => $request->user()->domain->name,
                        ],
                    ] : null,
                ];
            },
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error'   => $request->session()->get('error'),
                ];
            },
            'permissions' => function () use ($request) {
                $user = $request->user();
                return $user
                    ? [
                        'canViewDashboard'         => $user->can('view_dashboard'),
                        'canViewMfbSchedule'       => $user->can('view_mfb_schedule'),
                        'canViewAnalysis'          => $user->can('view_analysis'),
                        'canViewAutopay'           => $user->can('view_autopay'),
                        'canViewReports'           => $user->can('view_report'),
                        'canViewSchedule'          => $user->can('view_schedule'),
                        'canViewPaymentSummary'    => $user->can('view_payment_summary'),
                        'canViewCategoryReport'    => $user->can('view_category_report'),
                        'canViewMdaReport'         => $user->can('view_mda_report'),
                        'canViewBeneficiaryReport' => $user->can('view_beneficiary_report'),
                        'canViewUsers'             => $user->can('view_users'),
                        'canCreateUsers'           => $user->can('create_users'),
                    ] : null;
            },
        ]);
    }
}
