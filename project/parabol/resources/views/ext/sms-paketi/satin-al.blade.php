<h2>Yeni SMS Paketi Satın Al</h2>
        <p>Aşağıdaki SMS paketlerimizden satın alarak müşterilerinize bakiye bildirimini SMS ile bildirebilirsiniz.</p>
        
            <?php foreach($paketler AS $ucret => $adet2)  { 
              ?>
             <a href="/{{$cid}}/ext/sms-paketi-al?price={{$ucret}}" class="btn m-1 btn-<?php if($price==$ucret) echo "success"; else echo "primary"; ?>">{{$adet2}} Adet SMS ({{price($ucret,"₺")}})</a> 
             <?php } ?>
           


<?php

$merchant_id='261280'; // Mağaza numarası
$merchant_key='tZCpqizR86gZd59T'; // Mağaza Parolası - Mağaza paneline giriş yaparak BİLGİ sayfasından alabilirsiniz.
$merchant_salt='GUC399Hp7iKQhK9s'; // Mağaza Gizli Anahtarı - Mağaza paneline giriş yaparak BİLGİ sayfasından alabilirsiniz.
//$price =$price*100;
## Kullanıcının IP adresi
if( isset( $_SERVER["HTTP_CLIENT_IP"] ) ) {
    $ip = $_SERVER["HTTP_CLIENT_IP"];
} elseif( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {
    $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
} else {
    $ip = $_SERVER["REMOTE_ADDR"];
}
$user_ip= $ip; //!!!Eğer bu kodu sunucuda değil local makinanızda çalıştırıyorsanız buraya dış ip adresinizi(https://www.whatismyip.com/) yazmalısınız. 
$merchant_oid=time();//sipariş numarası: her işlemde benzersiz olmalıdır! Bu bilgi bildirim sayfanıza yapılacak bildirimde gönderilir.
if(!getisset("paytr")) {
    ekle([
        'oid'=>$merchant_oid,
        "company_id" => $cid,
        "sayi" => $adet,
        "date" => date("Y-m-d H:i:s"),
        "price" => $price
    ],"sms_paket");
}

$email="umit.tunc@truncgil.com";//"{$_POST['email']}"; // Müşterinizin sitenizde kayıtlı eposta adresi
$payment_amount=$price*100;//9.99 TL

$no_installment=0; // Taksit yapılmasını istemiyorsanız (Örn cep telefonu satışı) 1 yapın
$max_installment=9; // Sayfada görüntülenecek taksit adedini sınırlamak istiyorsanız (Örn altın vb kuyum satışı max 4 taksittir) uygun şekilde değiştirin.
$user_name = "{$_POST['name']} {$_POST['surname']}"; // Müşterinizin sitenizde kayıtlı ad soyad bilgisi
$user_address = "{$_POST['address']}"; // Müşterinizin sitenizde kayıtlı adres bilgisi
$user_phone = "{$_POST['phone']}"; // // Müşterinizin sitenizde kayıtlı telefon bilgisi
$test_mode=0;
$timeout_limit = "";
$merchant_ok_url = "https://app.parabol.truncgil.com/{$cid}/ext/sms-paketi-al?paytr=ok";//"https://www.truncgil.com.tr/pay.php?ok";//"http://www.eu-jer.com/pay.php?success"; // Başarılı ödeme sonrası müşterinizin yönlendirileceği sayfa
$merchant_fail_url ="https://app.parabol.truncgil.com/{$cid}/ext/sms-paketi-al?paytr=false";//"https://www.truncgil.com.tr/pay.php?false"; //"http://www.eu-jer.com/pay.php?incorrect"; // Ödeme sürecinde beklenmedik bir hata oluşması durumunda müşterinizin yönlendirileceği sayfa

// Müşterinin sepet içeriği - Ürün adedine göre çoğaltabilirsiniz
$user_basket = base64_encode(json_encode(array(
    array("SMS Paketi", "$price", 1) // 1. ürün (Adı - Birim Fiyatı - Adeti )
    )));
$currency = "{$_POST['cur']}";
$debug_on=1;//hata mesajlarının ekrana basılması için entegrasyon sürecinde 1 olarak bırakın. Daha sonra 0 yapabilirsiniz.


####### Bu kısımda herhangi bir değişiklik yapmanıza gerek yoktur. #######
$hash_str = $merchant_id .$user_ip .$merchant_oid .$email .$payment_amount .$user_basket.$no_installment.$max_installment.$currency.$test_mode;
$paytr_token=base64_encode(hash_hmac('sha256',$hash_str.$merchant_salt,$merchant_key,true));
$post_vals=array(
        'merchant_id'=>$merchant_id,
        'user_ip'=>$user_ip,
        'merchant_oid'=>$merchant_oid,
        'email'=>$email,
        'lang' => "tr",
        'payment_amount'=>$payment_amount,
        'paytr_token'=>$paytr_token,
        'user_basket'=>$user_basket,
        'debug_on'=>$debug_on,
        'no_installment'=>$no_installment,
        'max_installment'=>$max_installment,
        'user_name'=>$user_name,
        'user_address'=>$user_address,
        'user_phone'=>$user_phone,
        'merchant_ok_url'=>$merchant_ok_url,
        'merchant_fail_url'=>$merchant_fail_url,
        'timeout_limit'=>$timeout_limit,
        'currency'=>$currency,
        'test_mode'=>$test_mode
    );

$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1) ;
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
$result = @curl_exec($ch);
 
if(curl_errno($ch))
{
    die("PAYTR IFRAME connection error. err:".curl_error($ch));
}
curl_close($ch);

$result=json_decode($result,1);

if($result['status']=='success')
{
    $token=$result['token'];
}
else
{
    die("PAYTR IFRAME failed. reason:".$result[reason]);
}

?>
<?php if(getisset("paytr")) {
    if(getesit("paytr","ok")) {
        bilgi("Ödeme işlemi başarılı. Ödeme için teşekkürler");
    } else {
         bilgi("Ödeme işlemi başarısız. Lütfen tekrar deneyiniz.");
         ?>

         <?php 
    }
} else { ?>
<script src="https://www.paytr.com/js/iframeResizer.min.js"></script>
<iframe src="https://www.paytr.com/odeme/guvenli/<?php echo $token;?>" id="paytriframe" frameborder="0" scrolling="no" style="width: 100%;"></iframe>
<script>iFrameResize({},'#paytriframe');</script>
<br><br>
<style>
#paytr_taksit_tablosu{clear: both;font-size: 12px;max-width: 1200px;text-align: center;font-family: Arial, sans-serif;}
#paytr_taksit_tablosu::before {display: table;content: " ";}
#paytr_taksit_tablosu::after {content: "";clear: both;display: table;}
.taksit-tablosu-wrapper{margin: 5px;width: 280px;padding: 12px;cursor: default;text-align: center;display: inline-block;border: 1px solid #e1e1e1;}
.taksit-logo img{max-height: 28px;padding-bottom: 10px;}
.taksit-tutari-text{float: left;width: 126px;color: #a2a2a2;margin-bottom: 5px;}
.taksit-tutar-wrapper{display: inline-block;background-color: #f7f7f7;}
.taksit-tutar-wrapper:hover{background-color: #e8e8e8;}
.taksit-tutari{float: left;width: 126px;padding: 6px 0;color: #474747;border: 2px solid #ffffff;}
.taksit-tutari-bold{font-weight: bold;}
@media all and (max-width: 600px) {.taksit-tablosu-wrapper {margin: 5px 0;}}
</style>
<div id="paytr_taksit_tablosu"></div>
<script src="https://www.paytr.com/odeme/taksit-tablosu/v2?token=c329bbf9a381001d274d4028a0bd1b8f0f6082be7158971b5c8bdddf65a42249&merchant_id=127117&amount=<?php echo $price; ?>&taksit=0&tumu=0"></script>

   
    <?php } ?>
