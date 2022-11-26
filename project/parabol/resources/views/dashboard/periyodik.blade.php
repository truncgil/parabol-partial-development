<div class="col-12">
            <div class="card">
            <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-12 text-nowrap">
                            <h4 title="Periyodik Tahsilat ve Ödeme Grafiği" class="mb-0 long-texts">Periyodik Tahsilat ve Ödeme Grafiği
                            </h4>
                        </div>
                        
                    </div>
                </div>
                <div class="card-body">
                
                    <div class="float-left">
                        <form action="" method="get">
                            <input type="hidden" name="filtre" value="ok">
                            <div class="input-group">
                                <select name="periyod" id="period" class="form-control">
                                    <option value="d.m.y">Günlük</option>
                                    <option value="W/y">Haftalık</option>
                                    <option value="M/y">Aylık</option>
                                    <option value="Y">Yıllık</option>
                                </select> 
                                <select name="account_id" id="account_id" class="form-control">
                                    <option value="">Tüm Bankalar</option>
                                    <?php $hesaplar = db("accounts")->orderBy("name","ASC")
                                    ->where("company_id",company_id())
                                    ->whereNull("deleted_at")
                                    ->get();
                                    $opening = [];
                                    foreach($hesaplar AS $h)  { 
                                        $opening[$h->id] = $h->opening_balance;
                                     ?>
                                     <option value="{{$h->id}}">{{$h->name}} ({{$h->currency_code}})</option> 
                                     <?php }
                                     $total_opening = array_sum($opening);

                                     ?>
                                </select>
                                
                                <select name="currency_rate" id="currency_rate" class="form-control">
                                    <option value="">₺ Olarak Göster</option>
                                    <option value="no">Kendi Para Biriminde Göster</option>
                                </select>
                                <input type="date" name="bas" value="{{get("start")}}" class="form-control" id="bas">
                                <input type="date" name="son" value="{{get("end")}}" class="form-control" id="son">
                                <button class="btn btn-primary">Filtrele</button>
                            </div>
                        </form>
                    </div>
                <?php $dizi = db("transactions")->where("company_id",company_id())
                ->whereNull("deleted_at")
                ->orderBy("id","ASC");
                if(!getisset("filtre")) {
                    $dizi = $dizi->whereDate("paid_at",">=",date("Y-m-d",strtotime("-15 days")));
                    $dizi = $dizi->whereDate("paid_at","<=",date("Y-m-d H:i:s"));
                } else {
                    if(!getesit("bas","")) {
                        $dizi = $dizi->whereDate("paid_at",">=",get("bas"));
                    } else {
                        $dizi = $dizi->whereDate("paid_at",">=",date("Y-01-01"));
                    }
                    if(!getesit("son","")) {
                        $dizi = $dizi->whereDate("paid_at","<=",get("son"));
                    } else {
                        $dizi = $dizi->whereDate("paid_at","<=",date("Y-m-d H:i:s"));
                    }
                }
                $start_balance = $total_opening; 
                if(!getesit("account_id","")) {
                    $dizi = $dizi->where("account_id",get("account_id"));
                    $start_balance = @$opening[get("account_id")]; 
                }
                
                if(getisset("periyod")) {
                    $periyod = get("periyod");
                } else {
                    $periyod = "d.m.Y";
                }
    

    $dizi = $dizi
    ->select("type","paid_at","amount","currency_rate")
    ->orderBy("paid_at","DESC")
    ->get(); 
    $rapor = [];
  //  dd($dizi);
    $say = 0;
    foreach($dizi AS $d) {
        $tarih_format = "'".  date($periyod,strtotime($d->paid_at)) . "'";
        if(!isset($rapor['tahsilat'][$tarih_format])) $rapor['tahsilat'][$tarih_format] = 0;
        if(!isset($rapor['odeme'][$tarih_format])) $rapor['odeme'][$tarih_format] = 0;
        if(!isset($rapor['bakiye'][$tarih_format])) {
            if($say==0) {
                $rapor['bakiye'][$tarih_format] = 0; //$start_balance;
            } else {
                $rapor['bakiye'][$tarih_format] = 0;
            }
        }
        $rate = 1;
        if(!getesit("currency_rate","no")) {
            $rate = $d->currency_rate;
        }
      // print2($rate);
        //print2($d->amount * $rate);
        if($d->type=="income") {
            $rapor['tahsilat'][$tarih_format] += $d->amount * $rate;
            $rapor['bakiye'][$tarih_format] +=   $d->amount * $rate;
        } else {
            $rapor['odeme'][$tarih_format] +=  $d->amount * $rate;
            $rapor['bakiye'][$tarih_format] -=  $d->amount * $rate;
        }
        $say++;
    }
   // dd($rapor)
   
    ?>
    
                    <canvas id="myChart" width="400" style="height:300px;" height="200"></canvas>
                </div>
            </div>
            <?php if($rapor!=null)  { 
              ?>
 <script>
     
     window.setTimeout(function(){
         <?php if(getisset("periyod"))  { 
           ?>
             $("#period").val("{{get("periyod")}}"); 
          <?php } ?>
         <?php if(getisset("currency_rate"))  { 
           ?>
             $("#currency_rate").val("{{get("currency_rate")}}"); 
          <?php } ?>
         <?php if(getisset("account_id"))  { 
           ?>
             $("#account_id").val("{{get("account_id")}}"); 
          <?php } ?>
         <?php if(getisset("bas"))  { 
           ?>
             $("#bas").val("{{get("bas")}}"); 
          <?php } ?>
         <?php if(getisset("bas"))  { 
           ?>
             $("#son").val("{{get("son")}}"); 
          <?php } ?>
         const ctx = document.getElementById('myChart').getContext('2d');
 const myChart = new Chart(ctx, {
     type: 'line',
     data: {
         labels: [<?php echo implode(', ',array_keys($rapor['odeme'])) ?>],
         datasets: [
            {
             label: 'Bakiye',
             data: [<?php echo implode(', ',$rapor['bakiye']) ?>],
             backgroundColor: [
                'rgba(255, 206, 86, 0.2)',
             ],
             borderColor: [
                'rgba(255, 206, 86, 0.2)',
             ],
             borderWidth: 1
         },
            {
             label: 'Tahsilat',
             data: [<?php echo implode(', ',$rapor['tahsilat']) ?>],
             backgroundColor: [
                 'rgba(75, 192, 192, 0.2)',
                 'rgba(255, 99, 132, 0.2)',
                 'rgba(54, 162, 235, 0.2)',
                 'rgba(255, 206, 86, 0.2)',
                 'rgba(153, 102, 255, 0.2)',
                 'rgba(255, 159, 64, 0.2)'
             ],
             borderColor: [
                 'rgba(75, 192, 192, 1)',
                 'rgba(255, 99, 132, 1)',
                 'rgba(54, 162, 235, 1)',
                 'rgba(255, 206, 86, 1)',
                 
                 'rgba(153, 102, 255, 1)',
                 'rgba(255, 159, 64, 1)'
             ],
             borderWidth: 1
         },
         {
             label: 'Ödeme',
             data: [<?php echo implode(', ',$rapor['odeme']) ?>],
             backgroundColor: [
                 'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                 'rgba(255, 206, 86, 0.2)',
                 'rgba(75, 192, 192, 0.2)',
                 'rgba(153, 102, 255, 0.2)',
                 'rgba(255, 159, 64, 0.2)'
             ],
             borderColor: [
                 'rgba(255, 99, 132, 1)',
                 'rgba(54, 162, 235, 1)',
                 'rgba(255, 206, 86, 1)',
               'rgba(75, 192, 192, 1)',
                 'rgba(153, 102, 255, 1)',
                 'rgba(255, 159, 64, 1)'
             ],
             borderWidth: 1
         }
     ]
     },
     options: {
         scales: {
             y: {
                 beginAtZero: true
             }
         }
     }
 });
     },1000);
 
 </script> 
             <?php } ?>
        </div>