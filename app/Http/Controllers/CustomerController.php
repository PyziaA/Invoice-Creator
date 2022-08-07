<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Customer;
use Validator;
use PDF;



class CustomerController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100',
            'address' => 'required|string|between:2,100',
            'postcode' => 'required|string|between:5,6',
            'city'=> 'required|string|between:2,100',
            'nip' => 'required|string|size:10',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $customer = Customer::create(array_merge(
                    $validator->validated(),
                    ['user_id' => auth()->user()->id,]
                ));
        return response()->json([
            'message' => 'Customer successfully registered',
            'customer' => $customer
        ], 201);
    }

    public function index(){
        $id=auth()->user()->id;
        $customers= Customer::where('user_id', $id)->get();
        return response()->json($customers);
    }

    public function show($id){
        $customer= Customer::find($id);
        if (is_null($customer)) {
            return response()->json([
                'message' =>'Customer not found']
                , 404);
        }
        if($customer->user_id !== auth()->user()->id){
            return response()->json([
                'message' =>'Customer cannot be show']
                , 403);
        }
        return response()->json($customer);
    }

    public function update(Request $request, $id) {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100',
            'address' => 'required|string|between:2,100',
            'postcode' => 'required|string|between:5,6',
            'city'=> 'required|string|between:2,100',
            'nip' => 'required|string|size:10',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        
        $customer= Customer::find($id);
        if (is_null($customer)) {
            return response()->json([
                'message' =>'Customer not found']
                , 404);
        }
        if($customer->user_id !== auth()->user()->id){
            return response()->json('Customer cannot be updated', 403);
        }

        $customer->name= $input['name'];
        $customer->email= $input['email'];
        $customer->address= $input['address'];
        $customer->postcode= $input['postcode'];
        $customer->city= $input['city'];
        $customer->nip= $input['nip'];
        $customer->save();
        
        return response()->json([
            'message' => 'Customer successfully updated',
            'customer' => $customer
        ], 201);
    }


    public function destroy($id) {
        $customer= Customer::find($id);
        if (is_null($customer)) {
            return response()->json([
                'message' =>'Customer not found']
                , 404);
        }

        if($customer->user_id !== auth()->user()->id){
            return response()->json([
                'message' =>'Customer cannot be deleted']
                , 403);
        }

        $customer->delete();
        return response()->json([
            'message' =>'Customer deleted successfully']
            , 201);
    }

}
