
<?php $cid = company_id();  ?>
@extends('layouts.admin')
@section('title', "Stok Takip Sistemi")
@section('content')
<?php 
$items = db("document_items")
->where("company_id",$cid)
->whereNull("deleted_at");

$urunler = db("items")
                ->where("company_id",$cid)
                ->where("enabled","1")
                ->whereNull("deleted_at")
                ->orderBy("name","ASC");
if(getisset("d2")) {
    $items = $items->whereDate("created_at","<=",get("d2"));
}
if(getisset("d1")) {
    $items = $items->whereDate("created_at",">=",get("d1"));
}
$items = $items->get();
$urunler = $urunler->get();

$alis = [];
$satis = [];

foreach($items AS $i) {
    if(!isset($alis[$i->item_id])) $alis[$i->item_id] = 0;
    if(!isset($satis[$i->item_id])) $satis[$i->item_id] = 0;
    if($i->type=="bill") {
        $alis[$i->item_id] += $i->quantity;
    } else {
        $satis[$i->item_id] += $i->quantity;
    }
    
    
}

//print2($alis);
//print2($satis);
?>
        <div class="alert alert-info">
            Parabol'de toplam {{$urunler->count()}} ürün / hizmet kaydınız bulunmaktadır.
            Bu ekrandaki sayımlar alış ve satış faturalarınızdaki toplam {{$items->count()}} işlem sonuçlarından yola çıkılarak hesaplanmaktadır.

        </div>

<div class="card">
    <div class="card-body">
    <script>
$(document).ready(function(){
  $("#ara").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#stok tbody tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
  $(".stok-filter").on("click",function(){
    var value = $(this).attr("type");
    $("#stok tbody tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>
        <div class="btn btn-success stok-filter" type="success">{{e2("Yeterli Stok")}}</div>
        <div class="btn btn-danger stok-filter" type="danger">{{e2("Eksi Stok")}}</div>
        <div class="btn btn-warning stok-filter" type="warning">{{e2("Kritik Stok")}}</div>
        <div class="btn btn-primary stok-filter" type="">{{e2("Tümü")}}</div>
        <div style="    margin: 10px 0;">
            <div class="input-group">
                <input type="text" placeholder="Ürün Ara..." name="" id="ara" class="form-control ">
                
                
            </div>
            <form action="" method="get">
            <div class="input-group">
           
                    <input type="date" value="{{get("d1")}}"  name="d1" class="form-control ">
                    <input type="date" value="{{get("d2")}}"  name="d2" class="form-control ">
                    <button class="btn btn-primary">Ara</button>
                
            </div>
            </form>
            
        </div>
        <div class="table-responsive">
            <table class="table" id="stok">
                <thead>
                    <tr>
                        <th>Ürün/Hizmet Adı</th>
                        <th>Alış</th>
                        <th>Satış</th>
                        <th>Stok</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                foreach($urunler AS $u)  { 
                    $alinan = 0;
                    $satilan = 0;
                    if(isset($alis[$u->id])) {
                        $alinan = $alis[$u->id];
                    }
                    if(isset($satis[$u->id])) {
                        $satilan = $satis[$u->id];
                    }
                    $stok = $alinan - $satilan;
                    if($stok<0) {
                        $stok_type = "danger";
                    } elseif($stok<10) {
                        $stok_type = "warning";
                    } else {
                        $stok_type = "success";
                    }


                 ?>
                 <tr 
                    
                 >
                     <td>{{$u->name}}</td>
                     <td><div class="btn btn-primary">{{$alinan}}</div>  </td>
                     <td><div class="btn btn-primary">{{$satilan}}</div>  </td>
                     <td>
                         <div class="d-none ">{{$stok_type}}</div>
                        <div class="btn btn-{{$stok_type}}"> {{$stok}}</div>    
                    </td>
                     <td></td>
                 </tr>  
                 <?php } ?>
                 </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(function(){
        window.setTimeout(function(){
            /*
            $("[data-class='danger']").addClass("table-danger");
            $("[data-class='warning']").addClass("table-warning");
            $("[data-class='success']").addClass("table-success");
            */
        },500);
    });
</script>

@endsection