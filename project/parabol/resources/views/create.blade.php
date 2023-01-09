<?php 

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
?>
@extends('layouts.auth')

@section('title', "Yeni Hesap Oluştur")

@section('message', "Parabol'ü Hemen Kullanmak İçin Aşağıdaki Bilgileri Doldurunuz")

@section('content')
<?php if(getisset("ekle")) {
    //dump(json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', post("credential"))[1])))));
    if(postisset("credential")) {
        $jwt = jwt_decode(post("credential"));
        /*
        ^ {#2458 ▼
  +"iss": "https://accounts.google.com"
  +"nbf": 1673246258
  +"aud": "135274556225-sg54p0a7klonf0crkh90i6ell9elklhi.apps.googleusercontent.com"
  +"sub": "106246376947887911405"
  +"email": "umitreva@gmail.com"
  +"email_verified": true
  +"azp": "135274556225-sg54p0a7klonf0crkh90i6ell9elklhi.apps.googleusercontent.com"
  +"name": "Ümit Tunç"
  +"picture": "https://lh3.googleusercontent.com/a/AEdFTp7vfBFiN0kHSy-_1dc-QIKKUkmhYLSMdweX6_1-kfA=s96-c"
  +"given_name": "Ümit"
  +"family_name": "Tunç"
  +"iat": 1673246558
  +"exp": 1673250158
  +"jti": "7691feb9c5c20e288d8c3c2a521442ae51f82340"
}
        */
     //   dd($jwt->email);
        $_POST['email'] = $jwt->email;
        $_POST['name'] = $jwt->name;

    }
    

    $user = db("users")->where("email",post("email"))->first();
    if($user) {
            ?>
            <?php if(postisset("credential")) {
               // dump($user);
                Auth::loginUsingId($user->id);
                 ?>
                 <div class="text-center">
                    <div class="btn btn-success">Google ile giriş yapılıyor...</div>
                 </div>
                 <script>
                    location.reload();
                 </script>
                 <?php 
            } else  { 
              ?>
             <div class="alert alert-danger">Bu mail adresine daha önce hesap oluşturulmuştur</div> 
             <?php } ?>
            <?php 
    } else {
            // print2($_POST);
            $password = Hash::make(post("password"));
            //  echo $password;
            $user_id = ekle([
                'email'=>post("email"),
                'name'=>post("name"),
                "enabled" => 1,
                "first_date" => simdi(),
                "locale" => "tr-TR",
                "landing_page" => "dashboard",
                'password'=>$password
            ],"users");
            $company_id = ekle([
                'enabled'=> 1,
                "created_by" => 1
            ],"companies");
            ekle([
                'user_id' => $user_id,
                'company_id' => $company_id,
                'user_type' => 'users'
            ],"user_companies");
            $finans = finans_api();
            ekle(['user_id'=>$user_id,"role_id"=> 2,"user_type"=> "users"],"user_roles");
            ekle([
                "company_id" => $company_id,
                "name" => "US Dollar",
                "code" => "USD",
                "rate" => virgul_to_nokta($finans['USD']['Buying']),
                "precision" => "2",
                "symbol" => "$",
                "symbol_first" => "1",
                "decimal_mark" => ",",
                "thousands_separator" => ".",
                "enabled" => "1"

            ],"currencies");
            ekle([
                "company_id" => $company_id,
                "name" => "Euro",
                "code" => "EUR",
                "rate" => virgul_to_nokta($finans['EUR']['Buying']),
                "precision" => "2",
                "symbol" => "€",
                "symbol_first" => "1",
                "decimal_mark" => ",",
                "thousands_separator" => ".",
                "enabled" => "1"
            ],"currencies");
            ekle([
                "company_id" => $company_id,
                "name" => "Türk Lirası",
                "code" => "TRY",
                "rate" => 1,
                "precision" => "2",
                "symbol" => "₺",
                "symbol_first" => "1",
                "decimal_mark" => ",",
                "thousands_separator" => ".",
                "enabled" => "1"
            ],"currencies");
            ekle([
                "company_id" => $company_id,
                "name" => "İngiliz Sterlini",
                "code" => "GBP",
                "rate" =>  virgul_to_nokta($finans['GBP']['Buying']),
                "precision" => "2",
                "symbol" => "₺",
                "symbol_first" => "1",
                "decimal_mark" => ",",
                "thousands_separator" => ".",
                "enabled" => "1"
            ],"currencies");
            $other = ekle([
                "company_id" => $company_id,
                "name" => "Çeşitli",
                "type" => "other",
                "color" => "#BC1AB6",
                "enabled" => 1
            ],"categories");
            $expense = ekle([
                "company_id" => $company_id,
                "name" => "Çeşitli",
                "type" => "expense",
                "color" => "#E28D1F",
                "enabled" => 1
            ],"categories");
            $income = ekle([
                "company_id" => $company_id,
                "name" => "Çeşitli",
                "type" => "income",
                "color" => "#554DA9",
                "enabled" => 1
            ],"categories");
            $item = ekle([
                "company_id" => $company_id,
                "name" => "Çeşitli",
                "type" => "item",
                "color" => "#86BF4E",
                "enabled" => 1
            ],"categories");
            $account_id = ekle([
                "company_id" => $company_id,
                "name" => "Varsayılan Hesap",
                "currency_code" => "TRY",
                "number" => "0",
                "opening_balance" => 0.0000,
                "bank_name" => "Varsayılan Hesap",
                "created_by" => $user_id,
                "enabled" => 1
            ],"accounts");
            settings_add($company_id,"company.email",post("email"));
            settings_add($company_id,"company.name",post("name"));
            settings_add($company_id,"company.account",$user_id);
            settings_add($company_id,"default.account",$account_id);
            settings_add($company_id,"default.expense_category",$expense);
            settings_add($company_id,"default.income_category",$income);
            settings_add($company_id,"default.locale","tr-TR");
            settings_add($company_id,"default.currency","TRY");
            settings_add($company_id,"invoice.title","Fatura");
            settings_add($company_id,"wizard.completed","1");
            settings_add($company_id,"invoice.color","#265AA5");
            settings_add($company_id,"offline-payments.methods",'[{"code":"offline-payments.cash.1","name":"Nakit Ödeme","customer":"0","order":"1","description":null},{"code":"offline-payments.bank_transfer.2","name":"Havale/EFT","customer":"0","order":"2","description":null}]');
            $user = db("users")->where("email",post("email"))->first();
            
            mail_template_upgrade($company_id);
            //Auth::login($user_id,true);
            //auth()->login($user);
            /*
            if (Auth::attempt(['email' => post("email"), 'password' => post("password")])) {
                return $user;
            }
            */
            $sonuc = Auth::loginUsingId($user_id);
            
            $mailHtml = "Merhaba ".post("name").",

Parabol Muhasebe uygulamasına hoş geldiniz! Umarız ki bu uygulama size ve işletmenize yardımcı olur ve işlemlerinizi daha kolay hale getirir.

Eğer herhangi bir sorunuz veya endişeniz olursa, lütfen bizimle iletişim kurun. Bizim müşteri destek ekibimiz sizin için her zaman burada ve yardım etmek için hazırdır.

İyi çalışmalar dileriz,
[Parabol Muhasebe Ekibi]";
        try {
                mailsend(
                    post("email"),
                    post("name").", Parabol Muhasebe uygulamasına hoş geldiniz!",
                    $mailHtml
                );
        } catch (\Throwable $th) {
            
        }
           
            //
            if(Auth::check()) {
                 ?>
                 <div class="alert alert-success">Sayın {{post("name")}}, Parabol'e hoşgeldiniz. Uygulamanız hazırlanıyor. Lütfen bekleyiniz...</div>
                 <div class="progress" style="height:20px">
                    <div class="progress-bar progress-bar-striped bg-warning progress-bar-animated" style="width:0%;height:20px"></div>
                    </div>
                 <script>
                     $(function(){
                        
                        var percent = 0;
                        window.setInterval(function(){
                            percent = percent + 5;
                            if(percent<=100) {
                                $(".progress-bar").css("width",percent + "%");
                            }
                           
                        },100);
                        
                        
                        window.setTimeout(function(){
                            location.href='/{{$company_id}}';
                        },3000);
                     });
                     
                    
                 </script>

                 <?php 
            }
            
    }
   
    
} ?>
        <form action="{{url("auth/create?ekle")}}" class="<?php if(Auth::check()) echo "d-none"; ?>"  method="post">
            {{csrf_field()}}
            <?php $form = array(
                'email' => array("envelope","email","E-Posta","required"),
                'text' => array("user","name","Adınız veya Firma Ünvanınız","required"),
                'password' => array("key","password","Parola","required")
              
                ) ?>
        <?php foreach($form AS $alan => $deger) { ?>
        <div class="form-group has-feedback">
            <div class="input-group input-group-merge input-group-alternative">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fa fa-{{$deger[0]}}"></i>
                    </span>
                </div>
                <input class="form-control" autocomplete="off" data-name="{{$deger[2]}}" {{$deger[3]}} placeholder="{{$deger[2]}}" name="{{$deger[1]}}" type="{{$alan}}">
            </div>
        </div>
        <?php } ?>
        <div class="row align-items-center">
          
            <div class="col-xs-12 col-sm-12">
                {!! Form::button(
                '<div class="aka-loader"></div> <span>' . "Hemen Kullanmaya Başla" . '</span>',
                [ 'type' => 'submit', 'class' => 'btn btn-success button-custom', 'data-loading-text' => "Hemen Kullanmaya Başla"]) !!}
            </div>
            </form>
        </div>
        <style>
            .button-custom {
                display:block;
                width:100%;
                margin:0 auto;
            }
        </style>

            <div class="mt-5 mb--4 <?php if(Auth::check()) echo "d-none"; ?>">
                <a href="{{ route('login') }}" class="text-white"><small>Giriş Yap</small></a>
            </div>
   
@endsection

