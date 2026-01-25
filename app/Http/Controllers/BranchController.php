<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Product;
use App\Models\Branches;
use App\Pagination\SemanticUIPresenter;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use DB;

class BranchController extends Controller {

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('localize_auth');
    }

    public function add(Request $request) {
        $company = null;

        $this->validate($request, [
            'branch_name'        => 'required|string',
            'branch_location'    => 'required|string',
            'branch_email'       => 'required|string',
            'branch_phone'       => 'required',
            'branch_code'        => 'required',
            'branch_address'     => 'required'

        ]);
        $products_id = DB::table('branches')->insertGetId([
            'branch_name'              => $request->branch_name ?: null,
            'branch_location'          => $request->branch_location ?: null,
            'branch_email'             => $request->branch_email,
            'branch_phone'             => $request->branch_phone,
            'branch_code'              => $request->branch_code,
            'branch_address'           => $request->branch_address,

        ]);
        return redirect()->back()->with('success', trans('branches.message.success.added'));
    }

    public function delete(Request $request,Branches $branches) {
        //print_r($request->branch);exit;
        DB::table('branches')->where('id', '=',$request->branch)->delete();
        //$branches->delete();
        return redirect()->back()->with('status', trans('branches.message.info.deleted'));
    }

    public function edit(Request $request,Branches $branches) {

        // var_dump($request->branch_name);die;

        // $branches = null;
        // try{
        //     $company = Company::findOrFail($request->company_id);
        // }catch(ModelNotFoundException $e) {
        //     $company = $product->company;
        // }
        // $this->validate($request, [
        //     'branch_name'      => 'in:' . str_replace(', ', ',', $product->branch_name) . '|required|string',
        //     'branch_location'    => 'exists:companies,id|integer|sometimes',
        //     'branch_email'       => 'max:128|required|string',
        //     'branch_phone'          => 'max:64|min:3|required',

        // ]);
        $this->validate($request, [
            'branch_name'        => 'required|string',
            'branch_location'    => 'required|string',
            'branch_email'       => 'required|string',
            'branch_phone'       => 'required',
            'branch_code'        => 'required',
            'branch_address'        => 'required'

        ]);
         try{
            $branches = Branches::findOrFail($request->branch_id);
        }catch(ModelNotFoundException $e) {
            $branches = $branches->id;
        }
        $branches->branch_name           = $request->branch_name;
        $branches->branch_location       = $request->branch_location;
        $branches->branch_email          = $request->branch_email;
        $branches->branch_phone          = $request->branch_phone;
        $branches->branch_code           = $request->branch_code;
        $branches->branch_address        = $request->branch_address;
        // if((!is_null($request->id) || !empty($request->id)) && $product->company->id != $request->company_id) {
        //     $new_company = Company::findOrFail($request->id);
        //     $product->company()->associate($company);
        // }

	        $branches->save();

        return redirect()->back()->with('success', trans('branches.message.success.edited'));
    }

    // public function getAll(Request $request) {
    //     $user = $request->user();
    //     $filters = $this->getFilters($request);
    //     //print_r($filters);
    //     //exit;
    //     $view_data = array();
    //     if($user->role === 'super') {
    //         $view_data['companies'] = Company::where('id', '=', 1)->get();
    //         $view_data['companies1'] = Company::where('id', '!=', 1)->get();
    //         $view_data['products'] = Branches::paginate(15);
    //         $view_data['companies']->transform(function($company) {
    //             $company->product_categories = collect(explode(',', str_replace(', ', ',', $company->product_categories)))->reject(function($c) {
    //                 return empty($c);
    //             });
    //             $company->product_sub_categories = collect(explode(',', str_replace(', ', ',', $company->product_sub_categories)))->reject(function($sc) {
    //                 return empty($sc);
    //             });
    //             return $company;
    //         });
    //     }else {
    //         $view_data['companies'] = Company::where('id', '=', 1)->get();
    //         $view_data['companies1'] = Company::where('id', '!=', 1)->get();
    //         $view_data['products'] = Branches::paginate(15);
    //         $view_data['companies']->transform(function($company) {
    //             $company->product_categories = collect(explode(',', str_replace(', ', ',', $company->product_categories)))->reject(function($c) {
    //                 return empty($c);
    //             });
    //             $company->product_sub_categories = collect(explode(',', str_replace(', ', ',', $company->product_sub_categories)))->reject(function($sc) {
    //                 return empty($sc);
    //             });
    //             return $company;
    //         });
    //     }
    //     $view_data['products']->lastOnPreviousPage = ($view_data['products']->currentPage() - 1) * $view_data['products']->perPage();
    //     $view_data['filter'] = count($filters) > 0;
    //     if($view_data['filter']) {
    //         $view_data['filters'] = $filters;
    //     }
    //     //echo '<pre>';print_r($view_data['filters']);echo'</pre>';exit;
    //     $view_data['presenter'] = new SemanticUIPresenter($view_data['products']);


    //     return view('super.branches', $view_data);
    // }


    public function getAll(Request $request) {
        $user = $request->user();
        $filters = $this->getFilters($request) ?? [];

        // Initialize all filter keys with empty strings to prevent undefined array key errors
        $view_data['filters'] = [
            'branch_name' => $filters['branch_name'] ?? '',
            'branch_code' => $filters['branch_code'] ?? '',
            'branch_phone' => $filters['branch_phone'] ?? ''
        ];

        // Build query and apply filters
        $query = Branches::query();
        
        if (!empty($view_data['filters']['branch_name'])) {
            $query->where('branch_name', 'LIKE', '%' . $view_data['filters']['branch_name'] . '%');
        }
        if (!empty($view_data['filters']['branch_code'])) {
            $query->where('branch_code', 'LIKE', '%' . $view_data['filters']['branch_code'] . '%');
        }
        if (!empty($view_data['filters']['branch_phone'])) {
            $query->where('branch_phone', 'LIKE', '%' . $view_data['filters']['branch_phone'] . '%');
        }

        // Paginate with filter parameters preserved
        $view_data['products'] = $query->paginate(15)
            ->appends($request->only(['branch_name', 'branch_code', 'branch_phone']));

        if($user->role === 'super') {
            $view_data['companies']  = Company::where('id', '=', 1)->get();
            $view_data['companies1'] = Company::where('id', '!=', 1)->get();

            $view_data['companies']->transform(function($company) {
                $company->product_categories = collect(explode(',', str_replace(', ', ',', $company->product_categories)))
                    ->reject(fn($c) => empty($c));
                $company->product_sub_categories = collect(explode(',', str_replace(', ', ',', $company->product_sub_categories)))
                    ->reject(fn($sc) => empty($sc));
                return $company;
            });
        } else {
            $view_data['companies']  = Company::where('id', '=', 1)->get();
            $view_data['companies1'] = Company::where('id', '!=', 1)->get();

            $view_data['companies']->transform(function($company) {
                $company->product_categories = collect(explode(',', str_replace(', ', ',', $company->product_categories)))
                    ->reject(fn($c) => empty($c));
                $company->product_sub_categories = collect(explode(',', str_replace(', ', ',', $company->product_sub_categories)))
                    ->reject(fn($sc) => empty($sc));
                return $company;
            });
        }

        $view_data['products']->lastOnPreviousPage = ($view_data['products']->currentPage() - 1) * $view_data['products']->perPage();
        $view_data['filter'] = !empty($view_data['filters']['branch_name']) || !empty($view_data['filters']['branch_code']) || !empty($view_data['filters']['branch_phone']);
        $view_data['presenter'] = new SemanticUIPresenter($view_data['products']);

        return view('super.branches', $view_data);
    }

    protected function getFilters(Request $request) {
        $master = array('branch_name','branch_code','branch_phone');
        $filters = collect($request->only($master))->reject(function($val) {
            return is_null($val) || empty($val);
        })->toArray();
        return $filters;
    }

    public function getOne(Request $request, Branches $branch) {
        $user = $request->user();
        $branch_id = $branch->id;
        
        // Return branch as array with single element to match view expectations ($branch[0])
        // Convert Eloquent model to stdClass object to match DB query result format
        $branch_obj = (object)[
            'id' => $branch->id,
            'branch_code' => $branch->branch_code,
            'branch_name' => $branch->branch_name,
            'branch_email' => $branch->branch_email,
            'branch_phone' => $branch->branch_phone,
            'branch_address' => $branch->branch_address,
            'branch_location' => $branch->branch_location,
        ];
        $view_data['branch'] = [$branch_obj];
        
        $view_data['clients']   = DB::table('users')->where('branch_id','=',$branch_id)->where('role','=','client')->get();
        $view_data['policies']   = DB::table('policies')->where('branch_id','=',$branch_id)->get();
        
        // Calculate total sales from all policies for this branch
        $total_sales_result = DB::table("payments")
            ->join('policies', 'payments.policy_id', '=', 'policies.id')
            ->where('policies.branch_id', '=', $branch_id)
            ->select(DB::raw("COALESCE(SUM(payments.amount), 0) as total_sales"))
            ->first();
        
        // Return amount as array with single element to match view expectations ($amount[0])
        $view_data['amount'] = [(object)['total_sales' => $total_sales_result->total_sales ?? 0]];
        
        $view_data['products']     = DB::table("products")->get();
        $view_data['branches']     = DB::table("branches")->get();

        return view($user->role . '.branchone',$view_data);
    }
}
