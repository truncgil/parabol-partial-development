
 <?php 
$global_notes = get_settings("ext.e-fatura.notes");

if(getisset("pdf_save")) {
    //@mkdir("storage/app/uploads/efatura");
    /*
    $pdfpath = "storage/app/uploads/efatura/$cid";
    @mkdir($pdfpath,0777,true);
    @unlink($pdfpath."/{$_POST['id']}.html");
    $html = post("pdf");
    $html = base64_decode($html);
    $link = @file_put_contents($pdfpath."/{$_POST['id']}.html",$html);
    */
    $html = post("html");
    $html = base64_decode($html);
    $json = json_decode(post("json"),true);
    $uuid = $json['faturaUuid'];
    db("documents")
    ->where("id",post("id"))
    ->update([
        "html" => $html,
        "efatura_json" => post("json"), 
        "uuid" => $uuid,
        "efatura" => 1 
    ]);
    echo "pdf ok";
    exit();
 
}
if(getisset("fatura_durum_guncelle")) {
    $j = j(post("json"));
    //print2($j);
    foreach($j AS $alan) {
     //   print2($alan);
        $sonuc  = db("documents")
        ->where("uuid",trim($alan['ettn']))
        ->where("company_id",$cid)
        ->update([
            "efatura_durum"=>$alan['onayDurumu']
        ]);
        echo $sonuc . "<br>";
    }
    
    exit();
}
$faturalar = db("documents")->where("type","invoice")
->where("company_id",$cid)
->whereNull("deleted_at")
->orderBy("id","DESC");
if(getisset("q")) {
    $q = "%{$_GET['q']}%";
    $faturalar = $faturalar->where(function($query) use($q) {
        $query = $query->orWhere("contact_name","like",$q);
        $query = $query->orWhere("document_number","like",$q);
        $query = $query->orWhere("amount","like",$q);
    });
    
}

$faturalar = $faturalar->simplePaginate(20);

/*
 [13] => stdClass Object
                (
                    [id] => 42
                    [company_id] => 1
                    [type] => invoice
                    [document_number] => FAT-00023
                    [order_number] => 
                    [status] => draft
                    [issued_at] => 2021-09-01 21:47:10
                    [due_at] => 2021-09-01 21:47:10
                    [amount] => 1008.51
                    [currency_code] => TRY
                    [currency_rate] => 1
                    [category_id] => 2
                    [contact_id] => 161
                    [contact_name] => ALPİN HALI SAN. VE TİC. LTD. ŞTİ.
                    [contact_email] => 
                    [contact_tax_number] => 
                    [contact_phone] => 
                    [contact_address] => 
                    [notes] => 
                    [footer] => 
                    [parent_id] => 0
                    [created_by] => 1
                    [created_at] => 2021-09-01 21:47:10
                    [updated_at] => 2021-09-01 21:47:17
                    [deleted_at] => 2021-09-01 21:47:17
                    [efatura] => 
                )

*/
?>
<?php 
$logo = setting('company.logo');
$logo = db("media")->where("company_id",$cid)
->where("id",$logo)
->first();
$logo_data = "";
if($logo) {
    $url = "storage/app/{$logo->disk}/{$logo->directory}/{$logo->filename}.{$logo->extension}";
    $url = file_get_contents($url);
    $url = base64_encode($url);
    $logo_data = "data:image/{$logo->extension};base64,".$url;
}
 ?>
 <div class="d-none" id="logo_data">{{$logo_data}}</div>
<form action="{{$path}}" style="    margin-top: 15px;" method="get">
   <div class="input-group ">
        <input type="text" class="form-control" name="q" value="{{get("q")}}" placeholder="Ara..." id="">
        <button class="btn btn-primary"><i class="fa fa-search"></i></button>
   </div>
</form>
<div class="float-right">
        <div class="btn btn-danger btn-sm fatura-durum-guncelle"><i class="fa fa-sync"></i> Fatura Durumlarını Güncelle</div>
       
</div>
<style>
    .ettn {
        width:100px;
        overflow:hidden;
        text-overflow:ellipsis;
    }
    .ettn:hover {
        width:auto;
        position:absolute;
        background:white;
        z-index:1000;
    }
</style>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Fatura No</th>
               
                <th>Müşteri Adı</th>
                <th>Tutar</th>
                <th>Durum</th>
                <th>E-Arşiv</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($faturalar AS $f)  { 
              ?>
             <tr>
                 <td>
                     {{$f->document_number}}

                 </td>
               
                 <td>{{$f->contact_name}}</td>
                 <td>{{money($f->amount,$f->currency_code,true)}}</td>
                 <td>{{ trans("documents.statuses.". $f->status) }}</td>
                 <td class="text-center">
                    <div class="badge badge-<?php 
                    if($f->efatura_durum=="İşlem Bekliyor")
                        echo "primary";
                    elseif($f->efatura_durum=="Onaylandı") 
                        echo "success"; 
                    else 
                        echo "warning"; 
                        ?>">{{$f->efatura_durum}}</div>
                    <div id="ettn{{$f->id}}" class="ettn">{{$f->uuid}}</div>
                 </td>
                 <?php 
    $i = $f;
    /*
    [id] => 9
            [company_id] => 1
            [type] => invoice
            [document_number] => INV-00006
            [order_number] => 
            [status] => sent
            [issued_at] => 2021-08-21 12:03:52
            [due_at] => 2021-08-21 12:03:52
            [amount] => 10159.8
            [currency_code] => TRY
            [currency_rate] => 1
            [category_id] => 2
            [contact_id] => 241
            [contact_name] => HALITLAR GIDA VE SAĞLIK ÜRÜNLERİ
            [contact_email] => 
            [contact_tax_number] => 
            [contact_phone] => 
            [contact_address] => 
            [notes] => 
            [footer] => 
            [parent_id] => 0
            [created_by] => 1
            [created_at] => 2021-08-21 12:03:53
            [updated_at] => 2021-08-21 12:04:20
            [deleted_at] => 
            [efatura] => 
        )

    [original:protected] => Array
        (
            [id] => 9
            [company_id] => 1
            [type] => invoice
            [document_number] => INV-00006
            [order_number] => 
            [status] => sent
            [issued_at] => 2021-08-21 12:03:52
            [due_at] => 2021-08-21 12:03:52
            [amount] => 10159.8
            [currency_code] => TRY
            [currency_rate] => 1
            [category_id] => 2
            [contact_id] => 241
            [contact_name] => HALITLAR GIDA VE SAĞLIK ÜRÜNLERİ
            [contact_email] => 
            [contact_tax_number] => 
            [contact_phone] => 
            [contact_address] => 
            [notes] => 
            [footer] => 
            [parent_id] => 0
            [created_by] => 1
            [created_at] => 2021-08-21 12:03:53
            [updated_at] => 2021-08-21 12:04:20
            [deleted_at] => 
            [efatura] => 
    */
    
    $vergi_dairesi = "";
    $vd = explode("/",$i->contact_tax_number);
    if(isset($vd[1])) $vergi_dairesi = trim($vd[1]);
            $doviz_turu = $i->currency_code;
   $data = [
    "belgeNumarasi" => "",
    "faturaTarihi" => date("d/m/Y"),
    "saat" => date("H:i:s"),
    "paraBirimi" => $i->currency_code,
    "dovzTLkur" => "0",
    "faturaTipi" => "SATIS",
    "vknTckn" =>  ed(trim($vd[0]),"11111111111"),
    "aliciUnvan" => $i->contact_name,
    "aliciAdi" => $i->contact_name,
    "aliciSoyadi" => "",
    "binaAdi" => "",
    "binaNo" => "",
    "kapiNo" => "",
    "kasabaKoy" => "",
    "vergiDairesi" => $vergi_dairesi,
    "ulke" => "Türkiye",
    "bulvarcaddesokak" => $i->contact_address,
    "mahalleSemtIlce" => "",
    "sehir" => " ",
    "postaKodu" => "",
    "tel" => $i->contact_phone,
    "fax" => "",
    "eposta" => "",
    "websitesi" => "",
    "iadeTable" => [],
    "ozelMatrahTutari" => "0",
    "ozelMatrahOrani" => 0,
    "ozelMatrahVergiTutari" => "0",
    "vergiCesidi" => " ",
    "malHizmetTable" => [],
    "tip" => "İskonto",
    "matrah" => 0,
    "malhizmetToplamTutari" => 0,
    "toplamIskonto" => "0",
    "hesaplanankdv" => 0,
    "vergilerToplami" => 0,
    "vergilerDahilToplamTutar" => 0,
    "odenecekTutar" => 0,
    "not" => "",
    "siparisNumarasi" => "",
    "siparisTarihi" => "",
    "irsaliyeNumarasi" => "",
    "irsaliyeTarihi" => "",
    "fisNo" => "",
    "fisTarihi" => "",
    "fisSaati" => " ",
    "fisTipi" => " ",
    "zRaporNo" => "",
    "okcSeriNo" => ""
];
$items = db("document_items")->where("document_id",$i->id)->whereNull("deleted_at")->get();
$eklenen = [];
//print2($items);
foreach($items AS $pi) {
    
    /*
    [1] => stdClass Object
                (
                    [id] => 3268
                    [company_id] => 1
                    [type] => invoice
                    [document_id] => 971
                    [item_id] => 1001
                    [name] => Kira Bedeli
                    [description] => Ocak, Şubat, Mart
                    [sku] => 
                    [quantity] => 3
                    [price] => 1210
                    [tax] => 0
                    [discount_type] => normal
                    [discount_rate] => 0
                    [total] => 3630
                    [created_at] => 2022-01-04 21:06:42
                    [updated_at] => 2022-01-07 14:28:15
                    [deleted_at] => 2022-01-07 14:28:15
                )
    */
    if(!in_array($pi->item_id,$eklenen)) {
        $data["malHizmetTable"][] = [
            "malHizmet" => $pi->name,
            "miktar" => $pi->quantity,
            "birim" => "C62",
            "birimFiyat" => $pi->price,
            "fiyat" => $pi->price,
            "iskontoNedeni" => "İskonto",
            "iskontoOrani" => 0,
            "iskontoTutari" => "0",
            "iskontoNedeni" => "",
            "malHizmetTutari" => $pi->total,
            "kdvOrani" => round($pi->tax*100/$pi->total,0),
            "kdvTutari" => $pi->tax,
            "vergininKdvTutari" => "0"
        ];
        $data['matrah'] += $pi->total;
        $data['hesaplanankdv'] += $pi->tax;
        $data['malhizmetToplamTutari'] += $pi->total;
        $data['vergilerToplami'] += $pi->tax;
        $data['vergilerDahilToplamTutar'] += $pi->total + $pi->tax;
        $data['odenecekTutar'] += $pi->total + $pi->tax;
        array_push($eklenen,$pi->item_id);
    }
    
}
$rakam2yazi = "";
if($doviz_turu=="TRY") {
    $rakam2yazi = "YALNIZ" . rakam2yazi($data['odenecekTutar']);
}

$notes = $rakam2yazi . "\n" . $i->notes ."\n" . $global_notes  ;
$data['not'] = $notes;

// print2($company_id);

$json = base64_encode(json_encode_tr($data));

?>
                 <td width="500">
                    
                     <div id="efatura{{$f->id}}" class="d-none">{{$json}}</div>
                     
                     <div class="btn-group">
                        
                        
                       
                     </div>
                     <div class="dropdown">
                        <button type="button" class="btn btn-primary dropdown-toggle btn-sm" data-toggle="dropdown">
                            <i class="fa fa-ellipsis-h text-white"></i>
                        </button>
                        <div class="dropdown-menu">
                        <?php if($f->html!="") {
                            
                            ?>
                            <button class="dropdown-item efatura2 " title="" data-id="{{$f->id}}"><i class="fa fa-file-invoice"></i> E-Arşiv'e Tekrar Gönder
                            
                            </button>
                            <?php 
                       } else {
                            ?>
                            <button class="dropdown-item efatura" title="" data-id="{{$f->id}}"><i class="fa fa-file-invoice"></i>  E-Arşiv'e Gönder
                            
                            </button>
                            <?php 
                       } ?>
                       
                      
                       <?php if($f->html!="")  { 
                         ?>
                        <a href="{{$path}}-open?id={{$f->id}}" target="_blank" class="dropdown-item" title=""><i class="fa fa-file-pdf"></i> PDF İndir</a> 
                        <a onclick="$.get('{{$path}}-mail-send?id={{$f->id}}');$(this).html('Mail gönderildi');" class="dropdown-item" title=""><i class="fa fa-envelope"></i> Mail Gönder</a> 
                        <a href="{{$path}}?ettn-duzelt={{$f->id}}" class="dropdown-item"><i class="fa fa-barcode"></i> ETTN Numarası Düzelt</a>
                        <?php } ?>
                           
                        </div>
                        </div>
                 </td>
             </tr> 
             <?php } ?>
        </tbody>
    </table> 
    {{$faturalar->appends($_GET)->links()}}
</div>

@include("ext.e-fatura.script")