@extends("welcome")
@section("content")

<div class="col-md-6">
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5 class="card-title" id='test'>เพิ่มเขตในพื้นที่</h5>
            <form method="post" action="/dropcenterareaadd">
            
                {{csrf_field()}}
                <div class="row">
                    <div class="col-lg-8 col-md-8">
                        <div class="position-relative form-group">
                            <label for="drop_center_name" class="">อำเภอ</label>
                            <select name="amphures" id="amphures" class="form-control" onchange="findzip(this)">
                                <option value="">เลือกอำเภอ</option>
                                @foreach ($amphures as $amphure)
                                    <option value="{{$amphure->id}}">{{$amphure->name_th}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="position-relative form-group">
                            <label for="drop_center_name_initial" class="">รหัสไปรษณีย์</label>
                            <select name="zipcode" id="zipcode" class="form-control" onchange="finddistric(this)" disabled>
                                <option value="">เลือกรหัสไปรษณีย์</option>
                                
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8 col-md-8" style="padding-left:45px;">
                        <div id="showdistric"></div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <a href="/dropcenterArea/{{$id}}">
                            <button type="button" class="btn-wide  btn btn-light text-right" style="float: right;">กลับ</button>
                        </a>
                        <span class="d-block text-right">
                            <button class="btn-wide  btn btn-primary">บันทึกข้อมูล</button>&nbsp;
                        </span>
                    </div>
                </div>
                <input type="hidden" name="drop_center_id" id="drop_center_id" value="{{$id}}">
            </form>
        </div>
    </div>
</div>

@endsection

{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> --}}
<script>
//     $.ajaxSetup({
//     headers: {
//       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//     }
//   });
    function findzip(amphureid){
        amphureid = amphureid.value;
        $.ajax({
            method:"POST",
            url:"{{url('drop_center_area_findzip')}}",
            dataType: 'json',
            data:{"amphureid":amphureid, "_token": "{{ csrf_token() }}",},
            success:function(data){
                $('#zipcode').html('');
                $('#zipcode').append($("<option></option>").attr("value", '').text('เลือกรหัสไปรษณีย์')); 
                $('#zipcode').attr('disabled', false);
                $.each(data, function(i, item) {
                    if(item.drop_center_id == null){
                        centerarea = "";
                    }else{
                        centerarea = "(สาขา "+item.drop_center_id+")";
                    }
                    $('#zipcode').append($("<option></option>").attr("value", item.zip_code).text(item.zip_code+centerarea)); 
                });
            }
        });
    }
    
    function finddistric(zipcode){
        zipcode = zipcode.value;
        $.ajax({
            method:"POST",
            url:"{{url('drop_center_area_finddistric')}}",
            dataType: 'json',
            data:{"zipcode":zipcode, "_token": "{{ csrf_token() }}",},
            success:function(data){
                content = "<div style='font-weight:bold; font-size:16px; margin-left:-15px;'>รายการตำบลในเขตพื้นที่รหัสไปรษณีย์ทั้งหมด</div>";
                $.each(data, function(i, item) {
                    content += "<div>"+item.name_th+"</div>";
                });
                $("#showdistric").html(content);
            }
        });
    }
</script>