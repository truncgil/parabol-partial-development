
@extends('layouts.admin')
@section('title', "Aktif Kullanıcı Listesi")
@section('content')
<?php $id = company_id(); 
if($id!=1) exit();
?>
<?php 

$db = db("users")
//->whereBetween("last_logged_in_at",[date("Y-m-d",strtotime("-365 days")),date("Y-m-d")])
//->whereDate("last_logged_in_at",">",date("Y-m-d",strtotime("-30 days")))
->orderBy("id","DESC")
->get();

?>
<div class="card">
    <div class="card-body">
<h1>{{$db->count()}}</h1>
<div class="table-responsive">
    <table class="table">
        <tr>
            <td>Sıra</td>
            <td>Company ID</td>
            <td>İsim</td>
            <td>E-Mail</td>
            <td>Üyelik Tarihi</td>
            <td>Son Giriş Tarihi</td> 
        </tr>
        <?php 
        $k=0;
        foreach($db AS $u) { 
        $uyelik_tarihi = date("dmY",strtotime($u->first_date));
        $son_giris_tarihi = date("dmY",strtotime($u->last_logged_in_at));
        ?>
        <?php if($uyelik_tarihi!=$son_giris_tarihi) { 
            $k++; 
            ?>
        <tr >
            <td>{{$k}}</td>
            <td>{{$u->id}}</td>
            <td>{{$u->name}}</td>
            <td>{{$u->email}}</td>
            <td>{{date("d.m.Y H:i",strtotime($u->first_date))}}</td>
            <td>{{date("d.m.Y H:i",strtotime($u->last_logged_in_at))}}</td>
        </tr>
        <?php } ?>
        <?php } ?>
    </table>    
    </div>
</div>
</div>
<script>
    $(function(){
        window.setTimeout(function(){
            $("[data-class='used']").addClass("table-success");
        },500);
    });
</script>
<style>
    .used td {
        background:green !important;
    }
</style>
@endsection