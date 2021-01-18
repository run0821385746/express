@extends("welcome")
@section("content")
<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header"> Courier List
            <div class="btn-actions-pane-right">

            </div>
        </div>  
        <div class="card-body table-responsive">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>CourierID</th>
                        <th>ชื่อ-สกุล</th>
                        <th>เขตพื้นที่รับผิดชอบ</th>
                        <th>COD_authen</th>
                        <th>สถานะ</th>
                        <th>ทำรายการ</th>
                    </tr>
                </thead>
                <tbody>

                    {{-- @if (!empty($courierList))
                        @foreach ($courierList as $courier)
                            <tr>
                                <td class="text-center text-muted">{{!empty($courier->id)? $courier->id : null}}</td>
                                <td class="text-center">{{!empty($courier->emp_firstname)? $courier->emp_firstname : null}} {{!empty($courier->emp_lastname)? $courier->emp_lastname : null}} </td>
                                <td class="text-center">โซนพื้นที่1</td>
                                <td class="text-center">อนุญาติ</td>
                                <td class="text-center"><div class="badge badge-warning">Active</div></td>
                                <td class="text-center">
                                    <a href="/getTransferByCourier/{{!empty($courier->id)? $courier->id : null}}">
                                        <button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-sm">ทำจ่ายพัสดุ</button>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif --}}
                </tbody>
            </table> <br><br>
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
                url:"{{url('getCurierListDatatable')}}",
                dataType: 'json',
                data:{
                        "_token": "{{ csrf_token() }}",
                        "id": "{{ $id }}",
                    },
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'id', name: 'id'},
                {data: 'emp_firstname', name: 'emp_firstname'},
                {data: 'area', name: 'area', className:'text-center'},
                {data: 'cod', name: 'cod', className:'text-center'},
                {data: 'emp_status', name: 'emp_status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
    });
</script>