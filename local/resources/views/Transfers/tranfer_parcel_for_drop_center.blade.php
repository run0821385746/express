@extends("welcome")
@section("content")

<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header"> DropCenter List
        </div>
        <div class="table-responsive">
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>
                        <th class="text-center">CourierID</th>
                        <th class="text-center"> ชื่อสาขา</th>
                        <th class="text-center">ที่อยู่</th>
                        <th class="text-center">เบอร์ติดต่อ</th>
                        <th class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($dropcenters))
                        @foreach ($dropcenters as $dropcenter)
                            <tr>  
                                <td class="text-center text-muted">{{$dropcenter->id}}</td>
                                <td class="text-center">{{$dropcenter->drop_center_name}}</td>
                                <td class="text-center">
                                    {{$dropcenter->drop_center_address}} 
                                    {{$dropcenter->District['name_th']}} 
                                    {{$dropcenter->amphure['name_th']}} 
                                    {{$dropcenter->province['name_th']}}  
                                    {{$dropcenter->drop_center_postcode}}</td>
                                <td class="text-center">{{$dropcenter->drop_center_phone}}</td>
                                <td class="text-center">
                                    @php
                                        $user = Auth::user();
                                    @endphp
                                    @if ($user->emp_branch_id == $dropcenter->id)
                                        <button type="submit" id="PopoverCustomT-2" class="btn btn-light btn-sm">สาขาต้นทาง</button>
                                    @else
                                        <a href="/getTransferByDropCenter/{{$dropcenter->id}}">
                                            <button type="submit" id="PopoverCustomT-2" class="btn btn-primary btn-sm">ทำจ่ายพัสดุ</button>
                                        </a>
                                        <button type="button" data-toggle="modal" data-target="#exampleModalCenter" class="btn btn-success btn-sm pull-right" onclick="linehallList('{{$dropcenter->id}}')">พิมพ์ใบ Line Hall</button>
                                    @endif
                                   
                                </td>
                            </tr>  
                        @endforeach
                    @endif
                </tbody>
            </table><br><br>
<!--             <div class="row">
                <div class="card-body">
                    <h5 class="card-title"></h5>
                    <nav class="" aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item"><a href="javascript:void(0);" class="page-link"
                                    aria-label="Previous"><span aria-hidden="true">«</span><span
                                        class="sr-only">Previous</span></a></li>
                            <li class="page-item"><a href="javascript:void(0);" class="page-link">1</a></li>
                            <li class="page-item active"><a href="javascript:void(0);" class="page-link">2</a></li>
                            <li class="page-item"><a href="javascript:void(0);" class="page-link">3</a></li>
                            <li class="page-item"><a href="javascript:void(0);" class="page-link">4</a></li>
                            <li class="page-item"><a href="javascript:void(0);" class="page-link">5</a></li>
                            <li class="page-item"><a href="javascript:void(0);" class="page-link"
                                    aria-label="Next"><span aria-hidden="true">»</span><span
                                        class="sr-only">Next</span></a></li>
                        </ul>
                    </nav>
                </div>
            </div> -->
        </div>
    </div>
</div>

@endsection
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header text-white bg-success">
          <h5 class="modal-title" id="exampleModalLongTitle">LH</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
                <table width='100%'>
                    <thead>
                        <tr>
                            <th>ที่</th>
                            <th>รหัส</th>
                            <th>เวลา</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id='linehallList'>
                
                    </tbody>
                </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-dismiss="modal">ปิด</button>
        </div>
      </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    function linehallList(dcId){
        // alert(dcId);
        $.ajax({
            method:"POST",
            url:"{{url('find_linehallList')}}",
            dataType: 'json',
            data:{"dcId":dcId, "_token": "{{ csrf_token() }}",},
            success:function(data){
                content = "";
                $.each(data, function(i, item) {
                    date = new Date(item.created_at);
                    dd = date.toLocaleString() 
                    
                    content += "<tr>";
                        content += "<td>"+(i+1)+"</td>";
                        content += "<td>"+item.transfer_bill_no+"</td>";
                        content += "<td>"+dd+"</td>";
                        content += "<td align='center'><a href='/linehallDetail/"+item.id+"' target='_blank'><i class='fa fa-print' aria-hidden='true'></i></a></td>";
                    content += "</tr>";
                });

                $("#linehallList").html(content);
            }
        });
    }
</script>