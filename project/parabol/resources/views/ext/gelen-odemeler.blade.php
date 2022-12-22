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
                </tr>
                <?php $odemeler = db("planlar")
                        ->join('user_companies','planlar.company_id','user_companies.company_id')
                        ->join('users','user_companies.user_id','users.id')
                        ->select(
                            "users.name",
                            "users.email",
                            "planlar.id",
                            "planlar.company_id",
                            "planlar.date",
                            "planlar.tutar",
                            "planlar.json",
                            "planlar.durum",
                        )

                        ->where("planlar.durum","<>","Ödeme Bekliyor")
                        ->where("planlar.json","not like","%test_mode%")
                        ->orderBy("planlar.id","DESC")

                        ->get(); 
                        
                ?>
                <?php 
                $tutar = 0;
                foreach($odemeler AS $o)  { 
                   
                     $json = json_decode($o->json,true);
                   ?>
                        <tr>
                            <td>{{@$o->name}} <br>
                                <small> {{@$o->email}}</small>
                                    ({{$o->company_id}})
                            </td>
                            <td>{{$o->date}}</td>
                            <td>{{$o->tutar}}</td>
                            <td><?php //dump($json) ?></td>
                        </tr>  
                 <?php $tutar += $o->tutar; } ?>
                 <tr>
                    <th></th>
                    <th></th>
                    <th>{{price($tutar, "TRY")}}</th>
                    <th></th>
                 </tr>
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