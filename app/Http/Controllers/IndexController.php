<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Policy;
use Illuminate\Http\Request;

class IndexController extends Controller {

    /**
     * Create a new index controller instance.
     * 
     * @return void
     */
    public function __construct() {
        $this->middleware('auth', array(
            'except'    => 'javascript'
        ));
        $this->middleware('localize_auth', array(
            'except'    => 'javascript'
        ));
    }

    /**
     * Get the user's dashboard.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getDashboard(Request $request) {
        $user = $request->user();
        $view_data = array();
        $view_data['company'] = $user->company;
        $view_data['company']->currency_symbol = collect(config('insura.currencies.list'))->keyBy('code')->get($view_data['company']->currency_code)['symbol'];
        switch($user->role) {
            case 'broker':
            case 'staff':
                // Use withSum to avoid N+1 queries
                $view_data['latest_policies'] = $user->inviteePolicies()
                    ->withSum('payments', 'amount')
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get();
                break;
            case 'client':
                // Use withSum to avoid N+1 queries
                $view_data['latest_policies'] = $user->policies()
                    ->withSum('payments', 'amount')
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get();
                break;
            case 'admin':
                // Use withSum to avoid N+1 queries
                $view_data['latest_policies'] = $view_data['company']->policies()
                    ->withSum('payments', 'amount')
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get();
                break;
            case 'super':
                // Use withSum to avoid N+1 queries
                $view_data['latest_policies'] = Policy::withSum('payments', 'amount')
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get();
                break;
        }
        $view_data['latest_policies']->transform(function($policy) {
            $policy->paid = $policy->payments_sum_amount ?? 0;
            $policy->due = $policy->premium - $policy->paid;
            $time_to_expiry = strtotime(date('Y-m-d')) - strtotime($policy->expiry);
            $policy->statusClass = $policy->due > 0 ? ($time_to_expiry < 1 ? 'warning' : 'negative') : 'positive';
            return $policy;
        });
        
        return view($user->role . '.dashboard', $view_data);
    }

    function get() {
        $response = redirect()->route('login'); // Redirect to the named login route
        if(auth()->check()) {
            $response = redirect()->route('dashboard');
        }
        return $response;
    }

    /**
     * Get Insura's dynamic javascript.
     * 
     * @return \Illuminate\Http\Response
     */
    public function javascript() {
        session()->reflash();
        return response(view('global.javascript'), 200)->header('Content-Type', 'text/javascript');
    }
}
