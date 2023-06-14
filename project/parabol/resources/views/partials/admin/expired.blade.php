<?php $cid = company_id(); 
    $u = u();
    $goster = false;
    $exp = $u->exp_date;
    $plan = db("planlar")->where("uid",$u->id)
    ->where("durum","Ödeme Yapıldı")
    ->first();
    $sure_bitti=false;
    if($plan) {
        $simdi = new DateTime();
       
        $exp2 = new DateTime($u->exp_date);
       
        $fark = $simdi->diff($exp2)->days;
        
        $sure_bitti=false;

        $baslangicTarihi = strtotime(date("Y-m-d")); 
        //baslangicTarihi => o zamana kadar geçen saniyesini buluyoruz.

        $bitisTarihi = strtotime($u->exp_date);
        //bitisTarihi => o zamana kadar geçen saniyesini buluyoruz.

        $fark = round(($bitisTarihi - $baslangicTarihi) / 86400,0);
        if($fark<30) {
            $goster = true;
        } else {
            $goster = false;
        }
        if($fark<=0) {
            $sure_bitti = true;
        }
    }
    if($exp=="") {
        db("users")
        ->where("id",$u->id)
        ->update([
            "exp_date"=> date("Y-m-d H:i:s",strtotime($u->created_at." +365 days"))
        ]);
        $u = u2($u->id);
        $exp = $u->exp_date;
    }
    $exp_date = date("d.m.Y",strtotime($exp));
    if($goster) {
         ?>
         <style>
             .exp  {
                 width:100%;
                 
               
                 position:fixed;
                 top:0px;
                 z-index:10000;
             }
             .navbar.navbar-top.navbar-expand.navbar-dark.border-bottom {
             
                top: 43px;
              
            }
            #main-body {
                margin-top: 103px;
            }
            
         </style>
          <?php if(strpos($_SERVER['REQUEST_URI'],"plan-satin-al")!==false) {
                     ?>
         
         <?php } else {
             ?>
             <script>
                console.log("{{$fark}}");
                <?php if($fark<0)  { 
                  ?>
                    $(function(){
                        location.href='{{url("$cid/ext/plan-satin-al")}}';
                    }); 
                 <?php } ?>
         </script>
             <?php 
         } ?>
         <div class="exp text-center">
            <div class="row">
                <div class="col-12 ">
                    <div class="btn-group">
                        <div class="btn btn-danger">Planınız {{$exp_date}} tarihinde doluyor.</div>
                        <a href="{{url("$cid/ext/plan-satin-al")}}" class="btn btn-success">Süre uzat</a>
                    </div>
                </div>
                
            </div>

         </div>
        
         <?php 
    }
    ?>

    <style>
        .sure_bitti {
                position:fixed;
                top:0px;
                left:0px;
                width:100%;
                height:100%;
                overflow:hidden;
                z-index:1000000;
                background:white;
                <?php if(strpos($_SERVER['REQUEST_URI'],"plan-satin-al")!==false) {
                     ?>
                     display:none;
                     <?php 
                } else {
                    if($sure_bitti)  { 
                     
                      ?>
                      display:block; 
                     <?php } else {
                          ?>
                          display:none;
                          <?php 
                     } ?>
                     <?php 
                } ?>
            }
    </style>
    <?php $harici = "plan-satin-al"; ?>
     <div class="sure_bitti m-auto text-center">
     <div class="container h-100">
    <div class="row align-items-center h-100">
        <div class="col-12 mx-auto">
            <div class="jumbotron">
                <?php //print2($_SERVER); ?>
                Parabol kullanım süreniz dolmuştur. Devam etmek istiyorsanız lütfen planınızı yükseltiniz. 
                Anlayışınız için teşekkürler. 
                <br>
                <a href="{{url("$cid/ext/plan-satin-al")}}" class="btn btn-success">Süre uzat</a>

            </div>
        </div>
    </div>
</div>

</div>