@extends("welcome")
@section("content")

<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header">
            รับพัสดุจาก DC ต้นทาง
            <div class="form-group">
                <div class="input-group" style="padding-top: 18px; padding-left:10px; margin-bottom: 0px !importent;">
                    {{csrf_field()}}
                    @method('PUT')
                    <input type="text" class="form-control" name="transfer_bill_no" id="transfer_bill_no" autofocus>
                    <div class="input-group-append">
                        <button class="btn btn-primary" id="find_transfer_bill_btn">ค้นหา</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>
                        <th class="text-center"> LH No</th>
                        <th class="text-center">TransferBillNo</th>
                        <th class="text-center">DROP CENTER ต้นทาง</th>
                        <th class="text-center">DROP CENTER ปลายทาง</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <tbody>  
                    @if (!empty($parcelFromOtherList))
                        @foreach ($parcelFromOtherList as $parcelFromOther)
                        <tr>
                            <td class="text-center text-muted">{{$parcelFromOther->id}}</td>
                            <td class="text-center text-muted">{{$parcelFromOther->transfer_bill_no}}</td>
                            <td class="text-center text-muted">{{$parcelFromOther->dc_sender->drop_center_name}}</td>
                            <td class="text-center">{{$parcelFromOther->dc_receiver->drop_center_name}}</td>
                            <td class="text-center">{{$parcelFromOther->transfer_bill_status}}</td>
                            <td class="text-center">
                                @if ($parcelFromOther->transfer_bill_status == 'sending')
                                    <a href="/getParcelDetailListFromOtherDC/{{$parcelFromOther->id}}">
                                        <button type="submit" id="PopoverCustomT-1" class="btn btn-primary btn-sm">ยืนยันการรับพัสดุ</button>
                                    </a>
                                @else
                                    <a href="/getParcelDetailListFromOtherDC/{{$parcelFromOther->id}}">
                                        <button type="submit" id="PopoverCustomT-1"
                                            class="btn btn-light btn-sm">ยืนยันการรับพัสดุ</button>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table><br><br>
        </div>
        <div class="d-block text-left card-footer">
           <!--  <div class="row">
                <div class="col-lg-6 col-md-6">
                    <nav class="" aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item"><a href="javascript:void(0);" class="page-link"
                                    aria-label="Previous"><span aria-hidden="true">«</span><span
                                        class="sr-only">Previous</span></a></li>
                            <li class="page-item"><a href="javascript:void(0);" class="page-link">1</a></li>
                            <li class="page-item active"><a href="javascript:void(0);" class="page-link">2</a></li>
                            <li class="page-item"><a href="javascript:void(0);" class="page-link">3</a></li>
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
<script>
    $("#find_transfer_bill_btn").click(function(){
        transfer_bill_no = $("#transfer_bill_no").val();
        $.post("{{url('find_transfer_bill')}}",
            {
                transfer_bill_no,
                _token: "{{ csrf_token() }}"
            },
            function(data){
                // alert(data);
                if(data == 'required'){
                    Swal.fire({
                        icon: 'error',
                        title: 'กรุณากรอกข้อมูล',
                        text: 'กรุณากรอกหมายเลขบิลนำส่ง !!',
                    })
                }else if(data == 'no_id'){
                    Swal.fire({
                        icon: 'error',
                        title: 'ไม่พบบิลนำส่ง',
                        text: 'กรุณาลองใหม่อีกครั้ง !!',
                    })
                }else{
                    window.location = "{{url('getParcelDetailListFromOtherDC')}}/"+data;
                }
            }
        );
    });
    $(document).on('keypress',function(e) {
        if(e.which == 13) {
            $("#find_transfer_bill_btn").trigger("click");
        }
    });
</script>
@endsection