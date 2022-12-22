

@extends('layouts.admin')
@section('title', "Duyurular ve Yenilikler")
@section('content')
<?php $cid = company_id(); ?>
<?php $url = "$cid/ext/duyurular"; ?>

<div class="card">
    <div class="card-body">
        
        <p>Parabol'ü sizinle beraber en hızlı şekilde geliştirmeye devam ediyoruz. Gelecek ve gelmekte olan yeniliklerimizi bu sayfadan takip edebilirsiniz.</p>
        <div class="card-content">
            <?php if($cid==1) {
                if(getisset("sil")) {
                    db("duyurular")->where("id",get("sil"))
                    ->delete();
                    bilgi("Silme işlemi başarılı");
                }
                if(getisset("ekle")) {
                    ekle([
                        "aciklama" =>post("aciklama"),
                        "date" => post("date")
                    ],"duyurular");
                    bilgi("Ekleme işlemi başarılı");
                }
                 ?>
                 <form action="{{$url}}?ekle" method="post">
                     {{csrf_field()}}
                     Açıklama:
                    <textarea name="aciklama" id="" cols="30" rows="10" class="form-control"></textarea>
                    Tarih:
                    <input type="date" name="date" id="" value="{{date("Y-m-d")}}" class="form-control">
                    <button class="btn btn-primary">Ekle</button>

                 </form>
                 <?php 
            } ?>
            
            <?php $duyurular = db("duyurular")->orderBy("date","DESC")->get(); 
                ?>
              
        </div>

        <div class="container py-2 mt-4 mb-4">
            <?php 
            $k=0; 
            foreach($duyurular AS $d)   { 
                if($k%2!=0)  { 
                 
               ?>
              <!-- timeline item 1 -->
              <div class="row no-gutters">
                <div class="col-sm"> <!--spacer--> </div>
                <!-- timeline item 1 center dot -->
                <div class="col-sm-1 text-center flex-column d-none d-sm-flex">
                  <div class="row h-50">
                    <div class="col">&nbsp;</div>
                    <div class="col">&nbsp;</div>
                  </div>
                  <h5 class="m-2">
                    <span class="badge badge-pill bg-light border">&nbsp;</span>
                  </h5>
                  <div class="row h-50">
                    <div class="col border-right">&nbsp;</div>
                    <div class="col">&nbsp;</div>
                  </div>
                </div>
                <!-- timeline item 1 event content -->
                <div class="col-sm py-2">
                  <div class="card text-white bg-success">
                    <div class="card-body">
                      <div class="float-right text-white small">{{date("d.m.Y ",strtotime($d->date))}}</div>
                      <h4 class="card-title text-white">{{substr($d->aciklama,0,50)}}...</h4>
                      <p class="card-text">{{$d->aciklama}}</p>
                      <?php if($cid==1)  { 
                        ?>
                                  <a href="{{$url}}?sil={{$d->id}}" onclick="return confirm('OK?')" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                  <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
              <!--/row-->
              <!-- timeline item 2 --> 
              
                 <?php } else  { 
                   ?>
             <div class="row no-gutters">
               <div class="col-sm py-2">
                 <div class="card text-white bg-success">
                   <div class="card-body">
                     <div class="float-right text-white small">{{date("d.m.Y",strtotime($d->date))}}</div>
                     <h4 class="card-title text-white">{{substr($d->aciklama,0,50)}}...</h4>
                     <p class="card-text">{{$d->aciklama}}</p>
                     <?php if($cid==1)  { 
                        ?>
                                  <a href="{{$url}}?sil={{$d->id}}"  onclick="return confirm('OK?')" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                  <?php } ?>
                   </div>
                 </div>
               </div>
               <div class="col-sm-1 text-center flex-column d-none d-sm-flex">
                 <div class="row h-50">
                   <div class="col border-right">&nbsp;</div>
                   <div class="col">&nbsp;</div>
                 </div>
                 <h5 class="m-2">
                   <span class="badge badge-pill bg-light border">&nbsp;</span>
                 </h5>
                 <div class="row h-50">
                   <div class="col border-right">&nbsp;</div>
                   <div class="col">&nbsp;</div>
                 </div>
               </div>
               <div class="col-sm"> <!--spacer--> </div>
             </div> 
                  <?php } ?>
            <!--/row-->
            <!-- timeline item 3 -->
            <?php $k++; } ?>
            
          </div>

    </div>
</div>
<style>
  
</style>

@endsection