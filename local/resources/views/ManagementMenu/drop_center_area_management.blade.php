@extends("welcome")
@section("content")
<style>
    .DropCenterAreadata{
        width: 350px !important;
    }
    .DropCenterAreaactiondata{
        width: 200px !important;
    }
</style>
<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header"> DropCenterArea List
        </div>
        <div class="card-body table-responsive">
            <table class="data-table align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>
                        <th class="text-center">NO</th>
                        <th class="text-center">รหัสไปรษณีย์</th>
                        <th class="text-center">อำเภอ</th>
                        <th class="text-center" style="width: 350px !important;">เขต/ตำบล</th>
                        <th class="text-center" style="width: 200px !important;">ทำรายการ</th>
                    </tr>
                </thead>  
                <tbody>
                    {{-- @foreach ($PostCodes as $PostCode)
                        @php
                            $Districts = App\Model\District::where('zip_code',$PostCode->postcode)->get(); 
                            $Districts2 = App\Model\District::where('zip_code', $PostCode->postcode)->first();
                            $amphures = App\Model\amphure::where('id', $Districts2->amphure_id)->first();
                        @endphp
                        <tr>
                            <td class="text-center">{{$PostCode->postcode}}</td>
                            <td class="text-center">{{$amphures->name_th}}</td>
                            <td class="text-center">
                                @foreach ($Districts as $District)
                                    {{$District->name_th}}</br>
                                @endforeach
                            </td>
                            <td class="text-center">
                                <a href="/dropcenterareadelect/{{$PostCode->id}}">
                                    <button type="button" id="PopoverCustomT-1" class="btn btn-primary btn-sm">ลบ</button>
                                </a>
                            </td>
                        </tr>  
                    @endforeach --}}
                </tbody>
            </table><br><br>
            <div class="d-block text-left card-footer">
                <div class="row">
                    <div class="col-lg-6 col-md-6 text-left">
                        
                    </div>
                    <div class="col-lg-6 col-md-6 text-right">
                        <a href="/drop_center_area_create/{{$id}}">
                            <button class="btn-wide  btn btn-primary">เพิ่ม เขตในพื้นที่ ใหม่</button>
                        </a>
                        &nbsp;
                        <a href="/dropcenter_get_list/{{$id}}">
                            <button class="btn-wide  btn btn-light" style="float: right;">กลับ</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header text-white bg-success">
              <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div id="courierinarealist"></div>
                <br>
                <form id='area_add_courier'>
                    {{csrf_field()}}
                    <input type="hidden" name="zipcode" id="zipcode" required>
                    <div class="row">
                        <div class="col-lg-3 col-md-3"></div>
                        <div class="col-lg-6 col-md-6">
                            <div class="input-group mb-3">
                                <select name="courierid" id="courierid" class="form-control" required>
                                    <option value="">เพิ่มพนักงานส่งพัสดุ</option>
                                    @foreach ($couriers as $courier)
                                        <option value="{{$courier->id}}">{{$courier->emp_firstname.' '.$courier->emp_lastname}}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-success">บันทึก</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3"></div>
                    </div>
                </form>
                <span id="area_add_courierdata" class="pull-right"></span>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-dismiss="modal">ปิด</button>
            </div>
          </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script>
        var zipcodevar = "";

        function deletedroupCenter(id){
            Swal.fire({
                icon: 'warning',
                title: 'ยืนยันการลบหรือไม่ ?',
                text: 'รายการ Courier ที่รับผิดชอบอยู่ในเขตจะหายไปด้วย !!',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: `ยืนยัน`,
                denyButtonText: `ไม่ต้องการลบ`,
            }).then((result) => {
                console.log(result);
                if (result.isConfirmed) {
                    // alert(id);
                    window.location.href = "/dropcenterareadelect/"+id;
                } else if (result.isDenied) {
                    // alert("55");
                }
            })
            // window.location.href = "/dropcenterareadelect/"+id;
        }
        $(function () {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    method:"POST",
                    url:"{{url('dropcenterAreaDataTable')}}",
                    dataType: 'json',
                    data:{
                            "_token": "{{ csrf_token() }}",
                            "id": "{{ $id }}",
                        },
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', className:'text-center'},
                    {data: 'postcode', name: 'postcode', className:'text-center'},
                    {data: 'ampure', name: 'ampure', className:'text-center'},
                    {data: 'Districts', name: 'Districts', className:'DropCenterAreadata'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, className:'text-center DropCenterAreaactiondata'},
                ]
            });
        });

        function courierinarea(zipcode){
            zipcodevar = zipcode;
            $("#zipcode").val(zipcode);
            $.ajax({
                method:"POST",
                url:"{{url('courierinarea')}}",
                dataType: 'json',
                data:{"zipcode":zipcode, "_token": "{{ csrf_token() }}",},
                success:function(data){
                    if(data == ""){
                        content = "<div align='center' style='color:red;'>ยังไม่มีผู้รับผิดชอบ</div>";
                    }else{
                        content = "";
                        content += "<table width='100%'>";
                            content += "<thead>";
                                content += "<tr>";
                                content += "<th>ที่</th>";
                                content += "<th>courier ID</th>";
                                content += "<th>ชื่อ-สกุล</th>";
                                content += "<th></th>";
                                content += "</tr>";
                            content += "</thead>";
                            content += "<tbody>";
                            $.each(data, function(i, item) {
                                // console.log(item.employee["emp_firstname"]);
                                content += "<tr>";
                                    content += "<td>"+(i+1)+"</td>";
                                    content += "<td>"+item.employee_id+"</td>";
                                    content += "<td>"+item.employee["emp_firstname"]+" "+item.employee["emp_lastname"]+"</td>";
                                    content += "<td align='center'><button type='button' class='btn btn-primary btn-md' onclick=\"area_del_courier('"+item.id+"')\">ลบ</button></td>";
                                content += "</tr>";
                            });
                            content += "</tbody>";
                        content += "</table>";
                    }

                    $("#courierinarealist").html(content);
                }
            });
        }

        $("form#area_add_courier").submit(function(){
            var formData = new FormData(this);
            $.ajax({
                url: '{{url('courierinarea_add')}}',
                type: 'POST',
                data: formData,
                async: false,
                success: function(data){
                    result = JSON.parse(data);
                    if(result.status == '1'){
                        courierinarea(zipcodevar);
                    }
                    $("#area_add_courierdata").html(result.msg);
                    setTimeout(() => {
                        $("#area_add_courierdata").html("");
                    }, 3000);
                },
                cache: false,
                contentType: false,
                processData: false
            });
            return false;
        });

        function area_del_courier(id){
            if(confirm("ต้องการลบผู้ส่ง ?")){
                $.post("{{url('courierinarea_Del')}}",
                    {
                        id,
                        _token:"{{ csrf_token() }}"
                    },
                    function(data){
                        result = JSON.parse(data);
                        if(result.status == '1'){
                            courierinarea(zipcodevar);
                        }
                        $("#area_add_courierdata").html(result.msg);
                        setTimeout(() => {
                            $("#area_add_courierdata").html("");
                        }, 3000);

                    }
                )
            }
        }
    </script>