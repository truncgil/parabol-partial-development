

<?php 
$cid = company_id();
$logo = setting("company.logo");
$title = setting('company.name');
$logo = url($cid . "/uploads/" . $logo);        
$fatura = db("documents")->where("id", get("id"))
->where("company_id",$cid)
->first();
//dump($fatura);
$currency = $fatura->currency_code;
$faturaIcerik = db("document_items")->where("document_id", $fatura->id)
->whereNull("deleted_at")
->get();
//dump($faturaIcerik);
 ?>
@extends('layouts.print')
@section('title', "")
@section('content')
<style>
    .area {


        width:250px;
        overflow:hidden;

    }
    
    .area img {
        height:100px;
    }
    .area * {
        font-family:'Courier New', Courier, monospace !important;
        font-size:12px;
        color:black;
    }
    .area td {
        white-space:nowrap;
    }
</style>
<div class="area">
    <div class="text-center">
        <?php if($logo!="") {
             ?>
             <img src="{{$logo}}" alt=""> <br>
             <strong>{{$title}}</strong> <br>
             

             <?php 
        } ?>
    </div>
    <p>
        {{$fatura->document_number}}
        <br>
        {{df($fatura->issued_at)}}
    </p> 
    <small>
        <strong>Ünvan: {{$fatura->contact_name}} <br>
            {{$fatura->contact_address}} <br>
        VN / VD: {{$fatura->contact_tax_number!="" ? $fatura->contact_tax_number : "11111111111"}} <br>

        </strong>
    </small>
    <hr>
    <table>
        <?php 
        $totalTax = 0;
        foreach($faturaIcerik AS $f)  { 
           ?>
         <tr>
             <td>{{substr($f->name, 0, 15)}}</td>
             <td>{{$f->quantity}}</td>
             <td>{{price($f->price, $currency)}} </td>
         </tr> 
         <?php $totalTax += $f->tax; } ?>
         <tr>
            <th>KDV</th>
            <th></th>
            <th>{{price($totalTax, $currency)}}</th>
         </tr>
         <tr>
            <th>Toplam</th>
            <th></th>
            <th>{{price($fatura->amount, $currency)}}</th>
         </tr>
    </table>
    <hr>
</div>




@endsection