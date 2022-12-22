<h2>Toplu SMS Gönder</h2>
<?php 
$musteri = db("contacts")
->where("company_id",company_id())
->where("type","customer")
->whereNotNull("phone");
$tedarikci = db("contacts")
->where("company_id",company_id())
->where("type","vendor")
->whereNotNull("phone");
$toplam_musteri = $musteri->count(); 
$toplam_tedarikci = $musteri->count(); 
$musteriler = $musteri->get();
if(getisset("sms-gonder")) {
    print2($_POST);
    $telefonlar = explode(",",post("telefonlar"));
    $text = explode(",",post("text"));
    exit();
}
$baslik = "PARABOL";
$son_sms = db("sms")->where("company_id",company_id())->orderBy("id","DESC")->first();
if($son_sms) {
    $baslik = $son_sms->title;
}
?>
<p>Listenizde toplam {{$toplam_musteri}} müşteri telefonu kayıtlıdır. Aşağıdaki mesaj kutusu ile tümüne sms gönderebilirsiniz.</p>
SMS Başlığı (Kendinize özel bir SMS başlığı belirtebilirsiniz. Türkçe karakter kullanmayınız):
<input type="text" name="" id="baslik" maxlength="8" value="{{$baslik}}" class="form-control">

Telefon numaraları (her bir telefon numarasını virgüllerle ayırınız): 
<textarea name="" id="" cols="30" rows="5" class="form-control telefonlar"><?php foreach($musteriler AS $m) {
    echo $m->phone . ",";
} ?></textarea>
Mesaj İçeriği (Maksimum 160 karakter. Türkçe karakter kullanmayınız): 
<textarea name="" maxlength="160" id="" cols="30" rows="10" class="form-control text" placeholder=""></textarea>
<button class="btn btn-primary gonder">Müşterilere Toplu SMS Gönder</button>
<pre class="sonuc"></pre>
<script>
    $(function(){
        $(".gonder").on("click",function(){
            if(confirm("Belirtmiş olduğunuz telefonlara SMS gönderilecektir. Onaylıyor musunuz?")) {
                var bu = $(this);
                $(this).html("İşlem yapılıyor...");
                $.post("/{{company_id()}}/ext/toplu-sms-gonder",{
                    "telefonlar" : $(".telefonlar").val(),
                    "text" : $(".text").val(),
                    "baslik" : $("#baslik").val(),
                    "_token" : "{{csrf_token()}}"
                },function(d){
                    $(".sonuc").html(d);
                    bu.html("İşlem tamamlandı");

                })
            }
        })
    });
</script>
