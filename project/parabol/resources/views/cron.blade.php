<?php 
use App\Utilities\Custom;
$finans = file_get_contents("https://finans.truncgil.com/v4/today.json");
$finans = json_decode($finans,true);
print2($finans); 
$guncelle = 0;
foreach($finans AS $alan => $deger) {
    if(isset($deger['Buying'])) {
      //  print2($deger);
      /*
        $alan2 = strtoupper(str_replace("-","",$alan));
        if($alan=="22-ayar-bilezik") $alan2 = "BILEZIK";
        if($alan=="14-ayar-altin") $alan2 = "ONDORTAYARALTIN";
        if($alan=="18-ayar-altin") $alan2 = "ONSEKIZAYARALTIN";
        */
       // if($alan=="cumhuriyet-altini") $alan2 = strtoupper($alan);

        $rate = ($deger['Buying']);
         db("currencies")->where("code",$alan)
         ->update(['rate'=>$rate]);
         $guncelle++;
    }
   
} 
echo "ok";
$sorgu = db("currencies")->get();

?>