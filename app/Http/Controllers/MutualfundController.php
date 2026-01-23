<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;

use App\Models\Funds;
use App\Models\Branches;

use App\Pagination\SemanticUIPresenter;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Auth;
use DB;

class MutualfundController extends Controller {

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('localize_auth');
    }

    public function add(Request $request) {
        $company = null;

        $this->validate($request, [
            'amount'        => 'required|string',
            'client'        => 'required|string',
            'branch'        => 'required|string',
            'notes'         => 'required|string',
            'date'          => 'required',
            'product'       => 'required',
            'mack_no'       => 'required',
            'mack_date'     => 'required'

        ]);
        $products_id = DB::table('mutual_funds')->insertGetId([
            'amount'              => $request->amount ?: null,
            'client_id'           => $request->client ?: null,
            'branch_id'           => $request->branch ?: null,
            'notes'               => $request->notes,
            'date'                => date("Y-m-d", strtotime($request->date)),
            'm_product'           => $request->product,
            'mack_no'             => $request->mack_no,
            'mack_date'           => date("Y-m-d", strtotime($request->mack_date)),

        ]);
        return redirect()->back()->with('success', trans('funds.message.success.added'));
    }

    public function delete(Request $request, Funds $funds) {

        DB::table('mutual_funds')->where('id', '=',$request->fund)->delete();
        return redirect()->back()->with('status','Deleted');
    }

    public function edit(Request $request, Funds $funds) {
        // try{
        //     $funds = Funds::findOrFail($request->fund_id);
        // }catch(ModelNotFoundException $e) {
        //     $funds = $funds->id;
        // }

        $this->validate($request, [

            'amount'        => 'required|string',
            'branch'        => 'required|string',
            'notes'         => 'required|string',
            'date'          => 'required',
            'client'        => 'required|string',
            'product'       => 'required',
            'mack_no'       => 'required',
            'mack_date'     => 'required'
        ]);

        $credentials = [

            'amount'        => $request->amount,
            'branch_id'     => $request->branch,
            'notes'         => $request->notes,
            'date'          => date("Y-m-d", strtotime($request->date)),
            'client_id'     => $request->client,
            'm_product'     => $request->product,
            'mack_no'       => $request->mack_no,
            'mack_date'     => date("Y-m-d", strtotime($request->mack_date)),

        ];

        // $funds->amount          = $request->amount;
        // $funds->branch_id       = $request->branch;
        // $funds->notes           = $request->notes;
        // $funds->date            = $request->date;

        // if((!is_null($request->branch_id) || !empty($request->branch_id)) && $funds->branch->id != $request->branch_id) {
        //     $branch = Branches::findOrFail($request->branch_id);
        //     $funds->branch_id()->associate($branch);
        // }
        DB::table('mutual_funds')->where('id','=',$request->fund_id)->update($credentials);

        return redirect()->back()->with('success', 'updated');
    }

    public function getAll(Request $request) {
        $user = $request->user();
        $filters = $this->getFilters($request);


        //print_r($filters['client']);exit;
        $view_data = array();
        if($user->role === 'super') {
            $view_data['companies']     = Company::where('id', '=', 1)->get();
            $view_data['branches']      = Branches::all();
            $view_data['clients']       = DB::table('users')->where('role','=','client')->get();
            $view_data['products']      = DB::table('mproduct')->where('status','=','A')->get();


            $view_data['funds']         = DB::table('mutual_funds')
                            ->join('branches', 'branches.id', '=', 'mutual_funds.branch_id')
                            ->leftjoin('users','users.id','=','mutual_funds.client_id')
                            ->leftjoin('mproduct','mproduct.id','=','mutual_funds.m_product')
                            ->select('*','mutual_funds.id as mid','users.first_name','users.last_name');
            if(!empty($filters['client'])){

                $view_data['funds']  = $view_data['funds']->where('client_id','=',$filters['client']);
            }

            if(!empty($filters['branch'])){

                $view_data['funds']  = $view_data['funds']->where('mutual_funds.branch_id','=',$filters['branch']);
            }
            if(!empty($filters['product'])){

                $view_data['funds']  = $view_data['funds']->where('mutual_funds.m_product','=',$filters['branch']);
            }
                            $view_data['funds']  = $view_data['funds']->orderBy('mutual_funds.id','DESC')

                            ->paginate(15);






            $view_data['companies']->transform(function($company) {
                $company->product_categories = collect(explode(',', str_replace(', ', ',', $company->product_categories)))->reject(function($c) {
                    return empty($c);
                });
                $company->product_sub_categories = collect(explode(',', str_replace(', ', ',', $company->product_sub_categories)))->reject(function($sc) {
                    return empty($sc);
                });
                return $company;
            });
        } else {
            $view_data['companies']     = Company::where('id', '=', 1)->get();
            $view_data['branches']      = DB::table('branches')->where('id','=',Auth::user()->branch_id)->get();
            $view_data['clients']       = DB::table('users')->where('role','=','client')->where('branch_id','=',Auth::user()->branch_id)->get();
            $view_data['products']      = DB::table('mproduct')->where('status','=','A')->get();
            $view_data['funds']         = DB::table('mutual_funds')
                                            ->join('branches', 'branches.id', '=', 'mutual_funds.branch_id')
                                            ->leftjoin('users','users.id','=','mutual_funds.client_id')
                                            ->leftjoin('mproduct','mproduct.id','=','mutual_funds.m_product')
                                            ->where('mutual_funds.branch_id', '=', $user->branch_id)
                                            ->select('*','mutual_funds.id as mid','users.first_name','users.last_name');
            if(!empty($filters['client'])){

                $view_data['funds']  = $view_data['funds']->where('client_id','=',$filters['client']);
            }

            if(!empty($filters['branch'])){

                $view_data['funds']  = $view_data['funds']->where('mutual_funds.branch_id','=',$filters['branch']);
            }
            if(!empty($filters['product'])){

                $view_data['funds']  = $view_data['funds']->where('mutual_funds.m_product','=',$filters['branch']);
            }


                $view_data['funds']=$view_data['funds']->orderBy('mutual_funds.id','DESC')
                ->paginate(15);


            $view_data['companies']->transform(function($company) {
                $company->product_categories = collect(explode(',', str_replace(', ', ',', $company->product_categories)))->reject(function($c) {
                    return empty($c);
                });
                $company->product_sub_categories = collect(explode(',', str_replace(', ', ',', $company->product_sub_categories)))->reject(function($sc) {
                    return empty($sc);
                });
                return $company;
            });
        }

        $view_data['funds']->lastOnPreviousPage = ($view_data['funds']->currentPage() - 1) * $view_data['funds']->perPage();
        $view_data['filter'] = count($filters) > 0;
        if($view_data['filter']) {
            $view_data['filters'] = $filters;
        }
        $view_data['funds']->appends($filters);
        $view_data['presenter'] = new SemanticUIPresenter($view_data['funds']);


        return view('super.funds', $view_data);
    }

    protected function getFilters(Request $request) {
        $master = array('client','branch');
        $filters = collect($request->only($master))->reject(function($val) {
            return is_null($val) || empty($val);
        })->toArray();
        return $filters;
    }
}
