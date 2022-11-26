
@extends('layouts.admin')
@section('title', "")
@section('content')

<div class="card">
    <div class="card-body">
        <h1>Cari Hesaplar</h1>
        <p>Aynı isme sahip tedarikçi ve müşteri hesapların bakiyelerini bu modülden takip edebilirsiniz.</p>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <tr>
                    <th>Hesap Adı</th>
                    <th>Borç</th>
                    <th>Alacak</th>
                    <th>Bakiye</th>
                    <th>İşlem</th>
                </tr>
                <?php $contacts = db("contacts")
                                    ->where("company_id",company_id())
                                    ->select("name","id")
                                //    ->selectRaw("COUNT(name) AS toplam")
                                  //  ->groupBy("name")
                                  //  ->havingRaw("toplam=2")
                                    ->get();

                    $transactions = db("transactions")
                                        ->where("company_id",company_id())
                                        ->where("payment_method","offline-payments.cash.1")
                                        ->get();
                    
                    $arrayContacts = [];

                    foreach($contacts AS $c) {
                        $arrayContacts[$c->id] = $c;
                    }


                    $dizi = [];
                 //   dd($contacts);
                    foreach($transactions AS $t) {
                        if(isset($arrayContacts[$t->contact_id])) {
                            $hash = Str::slug($arrayContacts[$t->contact_id]->name);

                            if(!isset($dizi[$hash]['name'])) $dizi[$hash]['name'] = $arrayContacts[$t->contact_id]->name;
                            if(!isset($dizi[$hash]['income'])) $dizi[$hash]['income'] = 0;
                            if(!isset($dizi[$hash]['expense'])) $dizi[$hash]['expense'] = 0;
                            if(!isset($dizi[$hash]['total'])) $dizi[$hash]['total'] = 0;
    
                            if($t->type=='income') {
                                $dizi[$hash]['income'] += $t->amount * $t->currency_rate;
                                $dizi[$hash]['total'] += $t->amount* $t->currency_rate;
                            } else {
                                $dizi[$hash]['expense'] += $t->amount* $t->currency_rate;
                                $dizi[$hash]['total'] -= $t->amount* $t->currency_rate;
                            }
                        }
                        
                    }
    usort($dizi, function($a,$b){
        return $a['total'] <=> $b['total'];
    });
    foreach($dizi AS $c)  { 
        if($c['total']<0) {
            $durum ="(A)";
        } else {
            $durum = "(B)";
        }
        $c['total'] = abs($c['total']);
         
            
   ?>
                  <tr>
                      <td>{{$c['name']}}</td>
                      <td>{{money($c['income'],"TRY",2)}}</td>
                      <td>{{money($c['expense'],"TRY",2)}}</td>
                      <td>{{money($c['total'],"TRY",2)}} {{$durum}}</td>
                      <td></td>
                  </tr> 
  <?php } ?> 
            </table>
        </div>

    </div>
</div>

@endsection