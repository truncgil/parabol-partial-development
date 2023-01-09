<?php 
if(company_id()==1) {
    if(getisset("gonder")) {
        $mails = explode("\n",post("mails"));
        foreach($mails AS $m) {
           $m = trim($m);
           try {
             mailsend($m,post("subject"),post("html"));
           } catch (\Throwable $th) {
             echo "$m mail adresine mail gönderilemedi";
           }
           
          
        }
        
    //     bilgi("Mailler Gönderildi");
      } 
}

 ?>