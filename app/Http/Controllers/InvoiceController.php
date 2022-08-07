<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Position;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ValidatedInput;
use PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use NumberToWords\NumberToWords;




class InvoiceController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $id=auth()->user()->id;
        $invoices= Invoice::where('invoices.user_id', $id)->get();
        $invoices->load('user', 'customer','positions'); 
        if (is_null($invoices)) {
            return response()->json([
                'message' =>'Invoice not found']
                , 400);
        }
        return response()->json($invoices);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $validationRules=[
        'positions.*.name' => 'required|string', //* check every json inside 
        'positions.*.tax' => 'in:8,23',
        'positions.*.price' => 'required|string',
        'payment_method' => 'in:cash,bank_transfer',
        'customer_id' => 'required|integer',
        ];
        
        if($request->payment_method == 'bank_transfer'){
            $validationRules=array_merge($validationRules, array("account_number"=>'required|string|between:2,100'));
        }

        $validator = Validator::make($request->all(), $validationRules);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $customer = Customer::find($request->customer_id);

        if (is_null($customer)) {
            return response()->json([
                'message' =>'Customer not found']
                , 404);
        }

        $invoice = Invoice::create(
                    ['payment_method' => $request->payment_method,
                     'customer_id' => $request->customer_id,
                     'user_id' => auth()->user()->id,],
                );

        $invoice->positions()->createMany(
                $request->positions
        );
           
        return response()->json([
            'message' => 'Invoice successfully added',
            'invoice' => $invoice 
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoice= Invoice::find($id);
        $invoice->customer = Customer::where('id', $invoice->customer_id)->get();
        $invoice->user = User::where('id', $invoice->user_id)->get();
        $positions = $invoice->positions = Position::where('invoice_id', $id)->get();

        $results=array();
        foreach($positions as $value){
            $value['price_netto'] = round($value['price']/(1+($value['tax']/100)), 2);
            $value['tax_value'] = round($value['price'] - ($value['price']/(1+($value['tax']/100))), 2);
            array_push($results, $value); 
        }
      
        $invoice->sum_brutto = $invoice->positions->sum('price');
        $invoice->sum_tax = $invoice->positions->sum('tax_value');
        $invoice->sum_netto = $invoice->positions->sum('price_netto');
        $invoice->positions=$results;

        if (is_null($invoice)) {
            return response()->json([
                'message' =>'Invoice not found']
                , 404);
        }
        if($invoice->user_id !== auth()->user()->id){
            return response()->json([
                'message' =>'Customer cannot be show']
                , 403);
        }
        return response()->json($invoice, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validationRules = [
        'positions.*.name' => 'required|string',
        'positions.*.tax' => 'in:8,23',
        'positions.*.price' => 'required|string',
        'payment_method' => 'in:cash,bank_transfer',
        'customer_id' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $validationRules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        
        $invoice= Invoice::find($id);
        if (is_null($invoice)) {
            return response()->json([
                'message' =>'Invoice not found']
                , 404);
        }
        if($invoice->user_id !== auth()->user()->id){
            return response()->json('Invoice cannot be updated', 403);
        }      
        $invoice->payment_method= $input['payment_method'];
        $invoice->customer_id = $input['customer_id'];
        $position = Position::where('invoice_id', $id);
        if($position->exists()){
            $position->delete();
            $invoice->positions()->createMany($request->positions);
            $invoice->save();
            $invoice= Invoice::find($id);
            $invoice->load('user', 'customer','positions');
            return response()->json([
                'message' => 'Invoice successfully updated',
                'invoice' => $invoice
            ], 201);
        }else{
            return response()->json([
                'message' =>'Position not found']
                , 404);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoice= Invoice::find($id);
        if (is_null($invoice)) {
            return response()->json([
                'message' =>'Invoice not found']
                , 404);
        }

        if($invoice->user_id !== auth()->user()->id){
            return response()->json([
                'message' =>'Invoice cannot be deleted']
                , 403);
        }

        $invoice->delete();

        return response()->json([
            'message' =>'Invoice deleted successfully']
            , 201);
    }

    public function downloadPDF($id) { 
        $invoice = Invoice::find($id);
        $invoice->sum_brutto = $invoice->positions->sum('price');
        $invoice->invoice_number = $this->invoiceNumber();
        $invoice->user->name;
        $results=array();
        foreach($invoice->positions as $value){
            $value['price_netto'] = round($value['price']/(1+($value['tax']/100)), 2);
            $value['tax_value'] = round($value['price'] - ($value['price']/(1+($value['tax']/100))), 2);
            $value['value_netto'] = round($value['price_netto'] * $value['amount'],2);
            array_push($results, $value); 
        }
        $invoice->sum_tax = $invoice->positions->sum('tax_value');
        $invoice->sum_netto = $invoice->positions->sum('price_netto');
        $invoice->positions=$results;
        $numberToWords = new NumberToWords();
        $currencyTransformer = $numberToWords->getCurrencyTransformer('pl');
        $invoice->in_words = $currencyTransformer->toWords(100*($invoice->sum_brutto), 'PLN');
        $pdf = PDF::loadView('pdf', compact('invoice'));  
        return $pdf->download('invoice.pdf');
}

private function invoiceNumber() {
    $id = auth()->user()->id;
    $year = Carbon::now()->format('Y');
    $countUserInvoice = DB::table('invoices')->select(DB::raw('count(user_id) as total'))->where('user_id','=', $id)->whereYear('created_at', '=', $year)->first();
    return $countUserInvoice->total.'/'.$year;
}

}
