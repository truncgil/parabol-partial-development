@extends('layouts.admin')

@section('title', setting('invoice.title', trans_choice('general.invoices', 1)) . ': ' . $invoice->document_number)

@section('new_button')

    <x-documents.show.top-buttons type="invoice" :document="$invoice" />
@endsection

@section('content')
<?php $i = $invoice;
    //print2($i);
    
    ?>
    <?php if($i->efatura=="") { ?>
        <div class="btn btn-danger efatura d-none">E-Fatura'ya Gönder</div>
    <?php } else { ?>
    <div class="btn btn-success efatura-print d-none">E-Fatura'yı Yazdır</div>
    <?php } ?>
    <?php 
    
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
   $data = [
    "belgeNumarasi" => "",
    "faturaTarihi" => date("d/m/Y",strtotime($i->issued_at)),
    "saat" => date("H:i:s",strtotime($i->issued_at)),
    "paraBirimi" => $i->currency_code,
    "dovzTLkur" => "0",
    "faturaTipi" => "SATIS",
    "vknTckn" =>  ed($i->contact_tax_number,"11111111111"),
    "aliciUnvan" => $i->contact_name,
    "aliciAdi" => $i->contact_name,
    "aliciSoyadi" => $i->contact_name,
    "binaAdi" => "",
    "binaNo" => "",
    "kapiNo" => "",
    "kasabaKoy" => "",
    "vergiDairesi" => "SAHINBEY",
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
    "not" => "xxx",
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
$items = db("document_items")->where("document_id",$i->id)->get();
foreach($items AS $pi) {
    $data["malHizmetTable"][] = [
        "malHizmet" => $pi->name,
        "miktar" => $pi->quantity,
        "birim" => "C62",
        "birimFiyat" => $pi->price,
        "fiyat" => $pi->total,
        "iskontoNedeni" => "İskonto",
        "iskontoOrani" => 0,
        "iskontoTutari" => "0",
        "iskontoNedeni" => "",
        "malHizmetTutari" => $pi->price + $pi->tax,
        "kdvOrani" => 18,
        "kdvTutari" => $pi->tax,
        "vergininKdvTutari" => "0"
    ];
    $data['matrah'] += $pi->price;
    $data['hesaplanankdv'] += $pi->tax;
    $data['malhizmetToplamTutari'] += $pi->price;
    $data['vergilerToplami'] += $pi->tax;
    $data['vergilerDahilToplamTutar'] += $pi->price + $pi->tax;
    $data['odenecekTutar'] += $pi->price + $pi->tax;
}



// print2($company_id);

$json = base64_encode(json_encode_tr($data));

?>
    <x-documents.show.content type="invoice" :document="$invoice" hide-button-received />
@endsection

@push('scripts_start')
    <link rel="stylesheet" href="{{ asset('public/css/print.css?v=' . version('short')) }}" type="text/css">

    <x-documents.script type="invoice" />
@endpush

