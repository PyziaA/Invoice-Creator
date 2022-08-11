<!DOCTYPE html>
<html>
  <head>
  <meta charset="utf-8">
    <title>FAKTURA</title>
    <style>
       html {
        font-family: DejaVu Sans, sans-serif; 
       }
        .invoice{
            text-align: center;
        }
        .date{
            padding-bottom: 20px;
            padding-top: 30px;
        }
        .personalData{
            display: flex; 
            justify-content: center;
            flex-direction: unset;
            width: 100%;
            align-items: stretch;
            
        }
        .seller{
           
            display: inline-block;
            text-align: left;
            width: 49%;
        }
        .buyer{
            
            display: inline-block;
            width: 49%;
        }
        .border{
            margin-top: 10px;
            border-top: 2px solid #808080;
            border-bottom: 2px solid #808080;
            padding-top: 5px;
            padding-bottom: 5px;
            font-weight: bold;
            font-size: 18px;
        }
        span{
            padding-right: 5px;
        }
        td{
        padding: 2px;
        margin: 0px;}

        table{
            padding-top: 30px;
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
        border: 1px solid;
        }
        .nameWidth{
            width: 20%;
        }
        .lpWidth{
            width: 5%;
        }
        .borderNone{
            border-bottom-style: hidden;
            border-left-style: hidden;
        }
        .resumeStyle{
            font-weight: bold;
            text-align: right;
        }
        .textAlign{
            text-align: center;
        }
        .resume{
            line-height: 20px;
            font-size: 20px;
            text-align: center;
           
        }
        .sum{
            font-weight: bold;
        }
        .tableSum{
            width: 100%;
            margin-top: 25px;
            font-size: 18px;
        }
        .borderHiddden{
            border-bottom-style: hidden;
            border-left-style: hidden;
            border-top-style: hidden;
            border-right-style: hidden;
        }
        .signature{
            font-size: 10px;
            border-top: 2px solid #808080;
        }
        .signaturPlace{
            padding-top: 100px;
        }
    </style>
  </head>
  <body>
  <h1 class="invoice">Faktura numer: {{$invoice->invoice_number}}</h1>
  <h4 class="date">Data wystawienia faktury: {{$invoice->created_at->format('d-m-Y')}}</h4>
  <div class="personalData">
    <div class="seller">
        <div class="border">SPRZEDAWCA:</div>
            </br>{{$invoice->user->name}}
            </br>{{$invoice->user->address}}
            </br>{{$invoice->user->postcode}} {{$invoice->user->city}}
            </br><span>NIP:</span>{{$invoice->user->nip}}
    </div>

    <div class="buyer">
        <div class="border">NABYWCA:</div>
            </br>{{$invoice->customer->name}}
            </br>{{$invoice->customer->address}}
            </br>{{$invoice->customer->postcode}} {{$invoice->customer->city}}
            </br><span>NIP:</span>{{$invoice->customer->nip}}
    </div>
  </div>
    <table>
    <thead>
      <tr>
        <td class="lpWidth"><b>Lp.</b></td>
        <td class="nameWidth"><b>Nazwa</b></td>
        <td class="lpWidth"><b>j.m.</b></td>
        <td><b>Ilość</b></td>
        <td><b>Cena netto</b></td>
        <td><b>Stawka VAT</b></td>
        <td><b>Wartość netto</b></td>
        <td><b>VAT</b></td>
        <td><b>Wartość brutto</b></td>
      </tr>
      </thead>
      
      <tbody>
        <?php $Lp=1;?>
        @foreach ($invoice->positions as $position)
            <tr>
                <td>
                {{$Lp++}}
                </td>
                <td>
                {{$position->name}}
                </td>
                <td>
                {{$position->measure}} 
                </td>
                <td class="textAlign">
                {{$position->amount}} 
                </td>
                <td class="textAlign">
                {{$position->price_netto}} 
                </td>
                <td class="textAlign">
                {{$position->tax}}
                </td>
                <td class="textAlign">
                {{$position->value_netto}} 
                </td>
                <td class="textAlign">
                {{$position->tax_value}} 
                </td>
                <td class="textAlign">
                {{$position->price}}
                </td>
            </tr>
            @endforeach
            <tr>
            <td colspan="4" class="borderNone"></td>
            <td colspan="2" class="resumeStyle">Podsumowanie:</td>
            <td class="textAlign">{{$invoice->sum_netto}}</td>
            <td class="textAlign">{{$invoice->sum_tax}}</td>
            <td class="textAlign">{{$invoice->sum_brutto}}</td>
            </tr>
            <tr class="resume" >
            <td colspan="4" class="borderNone"></td>
            <td colspan="3" class="resumeStyle">Razem do zapłaty:</td>
            <td colspan="2" class="textAlign" class="sum">{{$invoice->sum_brutto}}</td>
            </tr>

      </tbody>
    </table>

    <div>
        <table class="tableSum ">
            <tr>
                <td class="sum borderHiddden">Do zapłaty:</td>
                <td class="borderHiddden">{{$invoice->sum_brutto}} PLN<</td>
            </tr>
            <tr>
                <td class="sum borderHiddden">Słownie:</td>
                <td class="borderHiddden">{{$invoice->in_words}}<</td>
            </tr>
            <tr>
                <td class="sum borderHiddden">Metoda płatności:</td>
                @if ($invoice->payment_method == 'bank_transfer')
                <td class="borderHiddden">przelew</td>
                @endif
                @if ($invoice->payment_method == 'cash')
                <td class="borderHiddden">gotówka</td>
                @endif
            </tr>
            @if ($invoice->payment_method == 'bank_transfer')
            <tr>
                <td class="sum borderHiddden">Numer konta bankowego:</td>
                <td class="borderHiddden">{{$invoice->account_number}}</td>
            </tr>
            @else
            <tr>
                <td class="sum borderHiddden"></td>
                <td class="borderHiddden"></td>
            </tr>
            @endif

        </table>
    </div>

    <div class="personalData signaturPlace">
    <div class="seller">
        <div class="signature">imię, nazwisko i podpis osoby upoważnionej do wystawienia dokumentu</div>
    </div>
    <div class="buyer">
        <div class="signature">imię, nazwisko i podpis osoby upoważnionej do odebrania dokumentu</div>
    </div>
  </div>
  </body>
</html>