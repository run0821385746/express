@extends("welcome")
@section("content")

<div class="col-md-12">  
    <div class="main-card mb-3 card">
        <div class="card-header">การขอ Reset Password</div>
        <div class="card-body">
            <div class="table-responsive" id="tablelist">
            
            </div>
        </div>
        {{-- <div class="d-block text-left card-footer">
            <div class="row">   
                <div class="col-lg-6 col-md-6"></div>
                <div class="col-lg-6 col-md-6"> --}}
                    {{-- <button class="btn-wide  btn btn-success pull-right">Export Documents</button> --}}
                {{-- </div>
            </div>
        </div> --}}
    </div>
</div>
<script>
    parcel_in_dropcenter();
    function parcel_in_dropcenter(){
        content = "<table class='table data-table' >";
            content += "<thead>";
                content += "<tr>";
                    content += "<th>No</th>";
                    content += "<th>ชื่อพนักงาน</th>";
                    content += "<th>เวลาที่ขอ</th>";
                    content += "<th>สถานะ</th>";
                    content += "<th>ดำเนินการ</th>";
                content += "</tr>";
            content += "</thead>";
            content += "<tbody>";
            content += "</tbody>";
        content += "</table>";
        $("#tablelist").html(content);
        $(function () {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    method:"POST",
                    url:"{{url('requerest_password_datatable')}}",
                    dataType: 'json',
                    data:{
                            "_token": "{{ csrf_token() }}"
                        },
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'employee_id', name: 'employee_id'},
                    {data: 'create_at', name: 'create_at'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, className:"text-center"},
                ]
            });
        });
    }
</script>
@endsection