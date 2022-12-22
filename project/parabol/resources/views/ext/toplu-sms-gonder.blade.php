
<?php 
$telefonlar = explode(",",post("telefonlar"));
$text = post("text");
$baslik = post("baslik");
foreach($telefonlar AS $telefon) {
    if(trim($telefon)!="") {
        $gonderilen = db("sms")->where("company_id",company_id())->count();
        $paket = db("sms_paket")->where("company_id",company_id())
        ->whereNotNull("json")
        ->select("sayi")->sum("sayi");
        echo("$gonderilen/$paket ");
        if($paket>$gonderilen) {
            $text = substr($text,0,160);
            sms($text,$telefon,$baslik,company_id());
            echo "SMS gönderilmiştir";
        } else {
            echo "SMS gönderim limitiniz dolmuştur. Lütfen yeni paket satın alınız";
        }
    }
}

 ?>