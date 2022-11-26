<?php $cid = company_id(); 
$user = db("users")->get();
$dizi = array();

foreach($user AS $u) {
    $dizi[$u->id] = $u;

}

$user = $dizi;

?>
@extends('layouts.admin')
@section('title', "Gelen Ödemeler")
@section('content')

<div class="card">
    <div class="card-body">
    <?php if($cid==1) {
       // print2($user);
         ?>
         <h2>{{e2("Paket Ödemeleri")}}</h2>
         <div class="table-responsive">
            <table class="table">
                <tr>
                    <th>Üye</th>
                    <th>Tarih</th>
                    <th>Ödeme Tutarı</th>
                    <th>Bilgi</th>
                    <th>Durum</th>
                </tr>
                <?php $odemeler = db("odemeler")
                ->where("durum","<>","Ödeme Bekliyor")
                ->orderBy("id","DESC")
                ->get(); ?>
                <?php foreach($odemeler AS $o)  { 

                    $u = @$user[$o->company_id];
                  ?>
                 <tr>
                     <td>{{@$u->name}} <br>
                        <small> {{@$u->email}}</small>

                     </td>
                     <td>{{$o->date}}</td>
                     <td>{{$o->tutar}}</td>
                     <td>{{$o->json}}</td>
                     <td>{{$o->durum}}</td>
                 </tr> 
                 <?php } ?>
            </table>

         </div>
         
         <h2>{{e2("SMS Ödemeleri")}}</h2>
         <div class="table-responsive">
            <table class="table">
                <tr>
                    <th>Üye</th>
                    <th>Tarih</th>
                    <th>Ödeme Tutarı</th>
                    <th>SMS Adedi</th>
                    <th>Durum</th>
                </tr>
                <?php $odemeler = db("sms_paket")
                ->where("durum","<>","Ödeme Bekliyor")
                ->orderBy("id","DESC")
                ->get(); ?>
                <?php foreach($odemeler AS $o)  { 
                    $u = $user[$o->company_id];
                  ?>
                 <tr>
                     <td>{{$u->name}} <br>
                        <small> {{$u->email}}</small>

                     </td>
                     <td>{{$o->date}}</td>
                     <td>{{$o->price}}</td>
                     <td>{{$o->sayi}}</td>
                     <td>{{$o->durum}}</td>
                 </tr> 
                 <?php } ?>
            </table>

         </div>
         
         <?php 
    } ?>
    </div>
</div>

@endsection