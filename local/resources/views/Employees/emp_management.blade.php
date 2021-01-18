@extends("welcome")
@section("content")

<div class="col-md-12">  
    <div class="main-card mb-3 card">
        <div class="card-header">ข้อมูลพนักงาน
        </div>
        <div class="table-responsive">
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>  
                    <tr>
                        <th class="text-center">รหัสพนักงาน</th>
                        <th class="text-left">ชื่อ-สกุล</th>
                        <th class="text-left">ตำแหน่ง</th>
                        <th class="text-center">เบอร์ติดต่อ</th>
                        <th class="text-right">สถานะ</th>
                        <th class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($employees as $emp)
                    <tr>
                    <td class="text-center text-muted">{{$emp->id}}</td>
                        <td class="text-left">{{$emp->emp_firstname}} {{$emp->emp_lastname}}</td>
                        <td class="text-left">{{$emp->emp_position}}</td>
                        <td class="text-center">{{$emp->emp_phone}}</td>
                        <td class="text-right">
                                @if ($emp->emp_status == '1')
                                    ปกติ
                                @else
                                    ยกเลิก
                                @endif
                        </td>
                        <td class="text-center">
                            @if ($employee->emp_position == 'เจ้าของกิจการ(Owner)' && $emp->emp_position == 'ผู้จัดการเขตพื้นที่(Area Manager)')
                                <button type="button" onclick="Add_Branch('{{$emp->id}}','{{$employee->id}}','{{$emp->emp_firstname}}')" class="btn btn-success btn-sm">พื้นที่ดูแล</button>
                            @endif
                            <a href="/get_dropcenter_list_for_edit_employee/{{$emp->id}}">
                                <button type="button" id="PopoverCustomT-1" 
                                    class="btn btn-primary btn-sm">ตั้งค่าใหม่</button>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table><br><br>
        </div>
        <div class="d-block text-left card-footer">
            <div class="row">
               <!--  <div class="col-lg-6 col-md-6 text-left">
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
                </div> -->
                <div class="col-lg-6 col-md-6 text-right">
                    <a href="/get_dropcenter_list_for_add_employee"><button class="btn-wide  btn btn-primary">เพิ่มพนักงานใหม่</button></a>
                    <button class="btn-wide  btn btn-success">Export Documents</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function Add_Branch(emp_id, doing_id, emp_name){
        Swal.fire({
            customClass: 'swal-wide',
            showCancelButton: false,
            showConfirmButton: false,
            reverseButtons: false,
            html:   '<div class="row" style="margin-left: -58px !important; margin-right: -58px !important; margin-top: -15px !important;">'+
                        '<div class="col-lg-12 col-md-12">'+
                            '<div class="card">'+
                                '<div class="card-header" style="color:#fff; background-color:rgb(118, 205, 255);">Responsible branch : '+emp_name+'</div>'+
                                '<div class="card-body text-left" style="font-size:14px;">'+
                                    '<form id="Add_branch_to_mana_area" method="post">'+
                                        '<div class="input-group mb-3">'+
                                            '{{csrf_field()}}'+
                                            '<input type="hidden" name="employee_id" value="'+emp_id+'" />'+
                                            '<input type="hidden" name="create_by" value="'+doing_id+'" />'+
                                            '<select class="form-control" name="branch_id" id="branch_id" required />'+
                                                '<option value=""> --กรุณาเลือกสาขา -- </option>'+
                                            '</select>'+
                                            '<div class="input-group-append">'+
                                                '<button type="button" id="save_Add_area" class="btn btn-success">เพิ่มพื้นที่ดูแล</button>'+
                                            '</div>'+
                                        '</div>'+
                                    '</form><br>'+
                                    '<div id="listshowArea"></div>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'
        });
        // $.ajax({
        //     method:"POST",
        //     url:"{{url('find_droupcenter_Empty')}}",
        //     dataType: 'json',
        //     data:{"_token": "{{ csrf_token() }}",},
        //     success:function(data){
        //         $("#branch_id").html("");
        //         $.each(data, function(i, v){
        //             $('#branch_id').append($("<option></option>").attr("value", v.id).attr('data-id',v.id).text(v.drop_center_name+'('+v.drop_center_name_initial+')'));
        //         });
        //     }
        // });
        getdroup_center_List();
        getlistArea(emp_id);

        $("#save_Add_area").click(function(){
            var formData = new FormData($("form#Add_branch_to_mana_area")[0]);
            $.ajax({
                url: "{{url('Add_branch_to_mana_area')}}",
                type: 'POST',
                data: formData,
                async: false,
                success: function(data){
                    result = JSON.parse(data);
                    if(result.status == '1'){
                        getdroup_center_List();
                        getlistArea(emp_id);
                    }else{
                        alert("เพิ่มข้อมูลไม่สำเร็จ !!");
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
            return false;
        });
    }


    function getdroup_center_List(){
        $.ajax({
            method:"POST",
            url:"{{url('find_droupcenter_Empty')}}",
            dataType: 'json',
            data:{"_token": "{{ csrf_token() }}",},
            success:function(data){
                $("#branch_id").html("");
                $('#branch_id').append($("<option></option>").attr("value", '').attr('data-id','').text(' --กรุณาเลือกสาขา -- '));
                $.each(data, function(i, v){
                    $('#branch_id').append($("<option></option>").attr("value", v.id).attr('data-id',v.id).text(v.drop_center_name+'('+v.drop_center_name_initial+')'));
                });
            }
        });
    }

    function getlistArea(emp_id){
        content = "<table class='table data-table'>";
            content += "<thead>";
                content += "<tr>";
                    content += "<th>No</th>";
                    content += "<th>ชื่อสาขา</th>";
                    content += "<th>ผู้เพิ่มข้อมูล</th>";
                    content += "<th>ดำเนินการ</th>";
                content += "</tr>";
            content += "</thead>";
            content += "<tbody>";
            content += "</tbody>";
        content += "</table>";
        $("#listshowArea").html(content);

        $(function () {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    method:"POST",
                    url:"{{url('manaArea_droupcenter_List')}}",
                    dataType: 'json',
                    data:{
                            "_token": "{{ csrf_token() }}",
                            "emp_id":emp_id
                        },
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
                    {data: 'branch_id', name: 'branch_id'},
                    {data: 'create_by', name: 'create_by'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, className:"text-center"},
                ]
            });
        });
    }

    function delete_manaArea_droupcenter(id, emp_id) {
        if(confirm('ต้องการลบพื้นที่ในการดูแลหรือไม่ ?')){
            $.post("{{url('delete_manaArea_droupcenter')}}",
                {
                    id,
                    _token: "{{ csrf_token() }}"
                },
                function(data,status){
                    result = JSON.parse(data);
                    if(result.status == '1'){
                        getdroup_center_List();
                        getlistArea(emp_id);
                    }else{
                        alert("เพิ่มข้อมูลไม่สำเร็จ !!");
                    }
                }
            )
        }
    }
    
</script>
@endsection