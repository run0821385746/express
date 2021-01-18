@extends("welcome")
@section("content")
<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header"> Courier List
            <div class="btn-actions-pane-right">

            </div>
        </div>  
        <div class="card-body table-responsive">
            <table class="data-table align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>  
                    <tr>
                        <th class="text-center">ลำดับ</th>
                        <th class="text-center">รหัสใบนำส่ง</th>
                        <th class="text-left">ผู้นำส่ง</th>
                        <th class="text-left">ทะเบียนรถนำส่ง</th>
                        <th class="text-center">เบอร์ติดต่อ</th>
                        <th class="text-center">ผู้จ่ายงาน</th>
                        <th class="text-right">สถานะ</th>
                        <th class="text-right">ยอด COD</th>
                        <th class="text-right">เวลาจ่ายงาน</th>
                        <th class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <tbody>  
                </tbody>
            </table>
            <br><br>
        </div>
    </div>
</div>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>
    $(function () {
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                method:"POST",
                url:"{{url('getTranferBillListDatatable')}}",
                dataType: 'json',
                data:{
                        "_token": "{{ csrf_token() }}",
                        "id": "{{ $id }}",
                    },
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', className:'text-center'},
                {data: 'transfer_bill_no', name: 'transfer_bill_no'},
                {data: 'transfer_bill_courier_id', name: 'transfer_bill_courier_id'},
                {data: 'tranfer_driver_sender_numberplate', name: 'tranfer_driver_sender_numberplate'},
                {data: 'courierPhone', name: 'courierPhone'},
                {data: 'tranfer_by_employee_id', name: 'tranfer_by_employee_id'},
                {data: 'transfer_bill_status', name: 'transfer_bill_status', className:'text-center'},
                {data: 'CODamount', name: 'CODamount', className:'text-right'},
                {data: 'created_at', name: 'created_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className:'text-center'},
            ]
        });
    });

    function CODReciveConfirm(COD, id){
        Swal.fire({
        title: 'ยอดCOD '+COD+' บาท',
        text: "รับยอดCODครบแล้วหรือไม่ ?",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, ฉันเก็บครบแล้ว'
        }).then((result) => {
            if (result.value) {
                window.location="/Recive_cod/"+id;
            }
        });
    }

    function closingDetail(FN, LN, PS, D){
        date  = D.substring(8, 10)+'/';
        date  += D.substring(5, 7)+'/';
        date  += D.substring(0, 4)+' ';
        date  += D.substring(11, 16);
        Title = "ผู้รับยอด COD";
        if(PS == "พนักงานจัดส่งพัสดุ(Courier)"){
            Title = "ปิดยอดอัตโนมัติ";
        }
        Swal.fire({
            type: 'success',
            title: Title,
            showCancelButton: false,
            showConfirmButton: false,
            reverseButtons: false,
            html:   '<div class="row">'+
                        '<div class="col-lg-12 col-md-12 text-left">'+
                            'ชื่อ : '+FN+' '+LN+
                            '<br>ตำแหน่ง : '+PS+
                            '<br>เวลาปิดยอด : '+date+
                        '</div>'+
                    '</div>'
        });
    }
</script>