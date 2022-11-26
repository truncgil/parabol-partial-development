
@extends('layouts.admin')
@section('title', "")
@section('content')

<div class="card">
    <div class="card-body">
        <h1>Yapılan İşlemler İstatistikleri</h1>
        <div class="table-responsive">
            <?php $db = db("transactions")
                            ->select("user_companies.company_id AS companyId","users.name","users.email")
                            ->selectRaw("COUNT(*) AS total")
                            ->selectRaw("(
                                select created_at from 
                                prb_transactions 
                                    where 
                                        company_id = companyId
                                    ORDER BY id DESC
                                    limit 1
                                ) AS last_transaction")
                            ->join("user_companies","transactions.company_id","user_companies.company_id")
                            ->join("users","users.id","user_companies.user_id")
                            ->groupBy("transactions.company_id")
                           // ->orderBy("total","DESC")
                            ->orderBy("last_transaction","DESC")
                            ->get();
             ?>
            <table class="table table-striped table-hover">
                <tr>
                    <th>Sıra</th>
                    <th>ID</th>
                    <th>Firma</th>
                    <th>E-Mail</th>
                    <th>Toplam İşlem</th>
                    <th>Son İşlem</th>
                </tr>
                <?php 
                $k=0;
                foreach($db AS $d)  { 
                    $k++;
                  ?>
                 <tr>
                    <td>{{$k}}</td>
                     <td>{{$d->companyId}}</td>
                     <td><span>{{$d->name}}</span></td>
                     <td><span>{{$d->email}}</span></td>
                     <td>{{$d->total}}</td>
                     <td>{{df($d->last_transaction)}}</td>
                 </tr> 
                 <?php } ?>
            </table>
        </div>

    </div>
</div>
<style>
    td span {
        max-width:300px;
        overflow:hidden;
        display:block;

    }
</style>
@endsection