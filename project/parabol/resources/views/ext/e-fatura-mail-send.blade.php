<?php 
$cid = company_id();
$fatura = db("documents")->where("id",get("id"))
->where("company_id",$cid)
->first();
if($fatura) {
   // print2($fatura);
    /*
             stdClass Object
(
    [id] => 1031
    [company_id] => 1
    [type] => invoice
    [document_number] => FAT-00039
    [order_number] => 
    [status] => draft
    [issued_at] => 2022-01-08 08:56:46
    [due_at] => 2022-01-08 08:56:46
    [amount] => 177
    [currency_code] => TRY
    [currency_rate] => 1
    [category_id] => 2
    [contact_id] => 488
    [contact_name] => Ümit Tunç Test
    [contact_email] => umit.tunc@truncgil.com
    [contact_tax_number] => 37306391754 / Şahinbey
    [contact_phone] => 
    [contact_address] => Mimar Sinan Mh. 120 Nolu Sk. Villa Nova Sitesi H Blok No:7 8/51 Oğuzeli/Gaziantep
    [notes] => 
    [footer] => 
    [parent_id] => 0
    [created_by] => 1
    [created_at] => 2022-01-08 08:56:46
    [updated_at] => 2022-01-08 08:56:46
    [deleted_at] => 
    [efatura] => 1
    [pdf] => storage/app/uploads/efatura/1/1031.html
)
    */
    $imzasiz = "";
    if($fatura->efatura_durum!="Onaylandı") {
        $imzasiz = '<div class="imzasiz">İmzasız</div>';
    }
    $amount = money($fatura->amount,$fatura->currency_code,true);
    $fatura_icerik = $fatura->html;//file_get_contents($fatura->pdf);
    $html = "Sayın {$fatura->contact_name}, <br>

    <strong>$amount</strong> tutarında oluşturulan e-Arşiv Fatura'nız ektedir. <br>
    İyi çalışmalar dileriz. 
    

    ";
    
    //$rand = rand();
   
  //  $fatura_icerik = mb_convert_encoding($fatura_icerik, 'HTML-ENTITIES', 'UTF-8');
    $logo_data = logo_data();
   // $fatura_icerik .= "<style>*{ font-family: DejaVu Sans !important; font-size: 12px; !important}</style>";
   $fatura_icerik = "
   <!DOCTYPE html>
   <html lang='tr'>
   <head>
      <meta charset='UTF-8'>
      <meta http-equiv='X-UA-Compatible' content='IE=edge'>
      <meta name='viewport' content='width=device-width, initial-scale=1.0'>
      <title>{$fatura->document_number} - Parabol E-Arşiv Fatura</title>
   </head>
   <body>
   <style>
        * {
            font-family:arial,sans-serif;
            font-size:12px;
        }
        .imzasiz {
            position: absolute;
            font-size: 150px;
            z-index: 100;
            transform: rotate(316deg);
            top: 40%;
            left: 145px;
            color: #ff00007d;
           }
        .imza {
            font-size:10px;
            float:right;
        }
        .imza img {
          
            width: 50px;
            display: inline;
            padding: 2px 0;
            position: relative;
            top: 10px;
        }
        
        .logo {
            width: 200px;
            position: absolute;
            left: calc(50% - 100px);
            top: 180px;
        }
        body {
            position:relative;
            width:800px !important;
            margin:0 auto;
        }
    </style>
    $imzasiz
    <img src='$logo_data' class='logo' alt=''>
   $fatura_icerik</body></html>";
    
    @mkdir("storage/app/pdf/$cid",0777,true);
    $pdf_path = "storage/app/pdf/$cid/{$fatura->document_number}.html";
    
    file_put_contents($pdf_path,$fatura_icerik);
    echo $fatura_icerik;
   // $pdf = $pdf->stream();
   //echo $pdf;
    mailsend($fatura->contact_email,"{$fatura->document_number} için e-Arşiv Fatura'nız Oluşturulmuştur",$html,"https://parabol.truncgil.com","Parabol'e Geçin",$pdf_path);
    @unlink($pdf_path);
}

?>
