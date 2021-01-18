@extends("welcome")
@section("content")
    
<div class="col-md-8">
    <div class="main-card mb-3 card">
        <div class="card-body">
  
            <!-- {{-- @if (!empty($employee->id))
                <h5 class="card-title">แก้ไขข้อมูลพนักงาน</h5>
                <form action="/employee/{{$employee->id}}" method="post">
                @method('PUT')
            @else   --}} -->
                <h5 class="card-title">เพิ่มพนักงานใหม่</h5>
                <form action="/employee" method="post">
            <!-- {{-- @endif --}}
            {{csrf_field()}} -->
            <div class="row">  
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="emp_firstname" class="">ชื่อ</label>
                            <input name="emp_firstname" id="emp_firstname" placeholder="" type="text"
                                class="form-control" value="" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="emp_lastname" class="">สกุล </label>
                            <input name="emp_lastname" id="emp_lastname" placeholder="" type="text"
                                class="form-control" value="" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="position-relative form-group">
                            <label for="emp_address" class="">ที่อยู่ติดต่อ</label>
                            <textarea name="emp_address" id="emp_address" placeholder="" type="text"
                                class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">  
                    <div class="col-lg-6 col-md-6">
                        {{csrf_field()}}
                        <div class="position-relative form-group">
                            <label for="emp_postcode" class="">รหัสไปรษณีย์</label>
                            {{-- <div class="input-group">
                                <input type="text" class="form-control" name="emp_postcode" id="emp_postcode" value="" required readonly>
                                <a href="postcode_get_list">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">ค้นหา</button>
                                    </div>
                                </a>
                            </div> --}}
                            <div class="input-group mb-3">
                                <input name="emp_postcode" id="emp_postcode" value="" type="text" class="form-control" required>
                                <div class="input-group-append">
                                    <button class="btn btn-info" type="button" onclick="findaddress()">ค้นหา</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="emp_province" class="">จังหวัด</label>
                            <select name="emp_province" id="emp_province" class="form-control" onchange="findamphure(this)">
                                <option value="">กรุณาเลือกจังหวัด</option>
                                @foreach ($provinces as $province)
                                    <option value="{{$province->id}}">{{$province->name_th}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="emp_district" class="">อำเภอ</label>
                            <select name="emp_district" id="emp_district" class="form-control" onchange="finddistric(this)" disabled>
                                <option value="">กรุณาเลือกอำเภอ</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="emp_sub_district" class="">ตำบล</label>
                            <select name="emp_sub_district" id="emp_sub_district" class="form-control" onchange="findzipcode(this)" disabled>
                                <option value="">กรุณาเลือกตำบล</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="emp_phone" class="">เบอร์ติดต่อ</label>
                            <input name="emp_phone" id="emp_phone" placeholder="" type="text" class="form-control"
                            value="" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                        <label for="emp_position" class="">ตำแหน่งงาน</label>
                            <select class="mb-2 form-control" name="emp_position" id="emp_position">
                                <?php
                                    if($employee->emp_position == "เจ้าของกิจการ(Owner)"){
                                ?>
                                    {{-- <option value="เจ้าของกิจการ(Owner)">เจ้าของกิจการ(Owner)</option> --}}
                                    <option value="ผู้จัดการเขตพื้นที่(Area Manager)">ผู้จัดการเขตพื้นที่(Area Manager)</option>
                                    <option value="ผู้จัดการสาขา(Drop Center Manager)">ผู้จัดการสาขา(Drop Center Manager)</option>
                                    <option value="พนักงานหน้าร้าน(Admin)">พนักงานหน้าร้าน(Admin)</option>
                                    <option value="พนักงานส่งพัสดุ(Line Haul)">พนักงานส่งพัสดุ(Line Haul)</option>
                                    <option value="พนักงานจัดส่งพัสดุ(Courier)">พนักงานจัดส่งพัสดุ(Courier)</option>
                                <?php
                                    }else if($employee->emp_position == "ผู้จัดการเขตพื้นที่(Area Manager)"){
                                ?>
                                    <option value="ผู้จัดการสาขา(Drop Center Manager)">ผู้จัดการสาขา(Drop Center Manager)</option>
                                    <option value="พนักงานหน้าร้าน(Admin)">พนักงานหน้าร้าน(Admin)</option>
                                    <option value="พนักงานส่งพัสดุ(Line Haul)">พนักงานส่งพัสดุ(Line Haul)</option>
                                    <option value="พนักงานจัดส่งพัสดุ(Courier)">พนักงานจัดส่งพัสดุ(Courier)</option>
                                <?php
                                    }else if($employee->emp_position == "ผู้จัดการสาขา(Drop Center Manager)"){
                                ?>
                                    <option value="พนักงานหน้าร้าน(Admin)">พนักงานหน้าร้าน(Admin)</option>
                                    <option value="พนักงานส่งพัสดุ(Line Haul)">พนักงานส่งพัสดุ(Line Haul)</option>
                                    <option value="พนักงานจัดส่งพัสดุ(Courier)">พนักงานจัดส่งพัสดุ(Courier)</option>
                                <?php
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="emp_branch_id" class="">สาขาที่ประจำการ</label>
                            <select class="mb-2 form-control" name="emp_branch_id" value="emp_branch_id">
                                @foreach ($dropcenters as $dropcenter)
                                <option  value="{{$dropcenter->id}}" >{{$dropcenter->drop_center_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if (!empty($employee->id))
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="emp_status" class="">สถานะ</label>
                            <select class="mb-2 form-control" name="emp_status">
                                {{-- <option value="0">เลือกสถานะ</option> --}}
                                <option value="1">ปกติ</option>
                                <option value="2">พ้นสภาพการเป็นพนักงาน</option>
                            </select>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="email" class="">Email (username)</label>
                            <input name="email" id="email" placeholder="" type="email"
                                class="form-control" value="" onkeyup="usernameadd(this)" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6" style="display: none;">
                        <div class="position-relative form-group">
                            {{-- <label for="username" class="">username</label> --}}
                            <input name="username" id="username" placeholder="" type="text"
                                class="form-control" value="" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="password" class="">password</label>
                            <input name="password" id="password" placeholder="" type="password"
                                class="form-control" value="" required>
                        </div>
                    </div>
                </div>
                    <div class="d-block text-center"><br>
                        <button class="btn-wide  btn btn-primary" type="submit">บันทึกข้อมูลพนักงานใหม่</button>
                    </div>
               
            </form>
            <a href="/employee_list/{{$employee->emp_branch_id}}">
                <button class="btn-wide  btn btn-light" type="reset" style="float: right;">กลับ</button>
            </a>
        </div>
    </div>
</div>

@endsection
<script>
    //     $.ajaxSetup({
    //     headers: {
    //       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //     }
    //   });
        function usernameadd(email){
            // alert(email.value);
            $("#username").val(email.value);
        }
        function findaddress(){
            zipcode = $("#emp_postcode").val();
            if(zipcode != ""){
                $.ajax({
                    method:"POST",
                    url:"{{url('findaddress')}}",
                    dataType: 'json',
                    data:{"zipcode":zipcode, "_token": "{{ csrf_token() }}"},
                    success:function(data){
                        // console.log(data.province[0].name_th);
                        $('#emp_province').attr('readonly', true);
                        $('#emp_province').html('');
                        // $('#emp_province').append($("<option></option>").attr("value", '').text('เลือกจังหวัด')); 
                        $.each(data.provincesall, function(i, item) {
                            if(data.province[0].id != item.id){
                                // $('#emp_province').append($("<option></option>").attr("value", item.id).text(item.name_th)); 
                            }else{
                                $('#emp_province').append($("<option></option>").attr("value", item.id).attr("selected","selected").text(item.name_th)); 
                            }
                        });

                        $('#emp_district').html('');
                        $.each(data.amphures, function(i, item) {
                            $('#emp_district').append($("<option></option>").attr("value", item.id).text(item.name_th));
                            $('#emp_district').attr('disabled', false);
                        });

                        $('#emp_sub_district').html('');
                        $.each(data.Districts, function(i, item) {
                            $('#emp_sub_district').append($("<option></option>").attr("value", item.id).text(item.name_th));
                            $('#emp_sub_district').attr('disabled', false);
                        });
                    }
                });
            }
        }

        function findamphure(provincename){
            provincename = provincename.value;
            $.ajax({
                method:"POST",
                url:"{{url('find_amphure')}}",
                dataType: 'json',
                data:{"provinceid":provincename, "_token": "{{ csrf_token() }}",},
                success:function(data){
                    // alert(data);
                    $('#emp_district').html('');
                    $('#emp_district').append($("<option></option>").attr("value", '').text('กรุณาเลือกอำเภอ')); 
                    $('#emp_district').attr('disabled', false);
                    $.each(data, function(i, item) {
                        $('#emp_district').append($("<option></option>").attr("value", item.id).text(item.name_th)); 
                    });
                }
            });
        }
        
        function finddistric(districname){
            districname = districname.value;
            $.ajax({
                method:"POST",
                url:"{{url('finddistric')}}",
                dataType: 'json',
                data:{"districid":districname, "_token": "{{ csrf_token() }}",},
                success:function(data){
                    $('#emp_sub_district').html('');
                    $('#emp_sub_district').append($("<option></option>").attr("value", '').text('กรุณาเลือกตำบล')); 
                    $('#emp_sub_district').attr('disabled', false);
                    $.each(data, function(i, item) {
                        $('#emp_sub_district').append($("<option></option>").attr("value", item.id).text(item.name_th)); 
                    });
                }
            });
        }
        
        function findzipcode(zipcode){
            districename = zipcode.value;
            $.ajax({
                method:"POST",
                url:"{{url('findzipcode')}}",
                dataType: 'json',
                data:{"districeid":districename, "_token": "{{ csrf_token() }}",},
                success:function(data){
                    $('#emp_postcode').attr('readonly', false);
                    $("#emp_postcode").val(data.zip_code);
                }
            });
        }
    </script>