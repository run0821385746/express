@extends("welcome")
@section("content")
    
<div class="col-md-8">
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5 class="card-title">แก้ไขข้อมูลพนักงาน</h5>
            <form action="/employee/{{$employ->id}}" method="post">
            @method('PUT')
            {{csrf_field()}}
            <div class="row">  
                <div class="col-lg-6 col-md-6">
                    <div class="position-relative form-group">
                        <label for="emp_firstname" class="">ชื่อ</label>
                        <input name="emp_firstname" id="emp_firstname" placeholder="" type="text"
                            class="form-control" value="{{!empty($employ->emp_firstname) ? $employ->emp_firstname : null}}">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="position-relative form-group">
                        <label for="emp_lastname" class="">สกุล </label>
                        <input name="emp_lastname" id="emp_lastname" placeholder="" type="text"
                            class="form-control" value="{{!empty($employ->emp_lastname) ? $employ->emp_lastname : null}}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="position-relative form-group">
                        <label for="emp_address" class="">ที่อยู่ติดต่อ</label>
                        <textarea name="emp_address" id="emp_address" placeholder="" type="text"
                            class="form-control">{{!empty($employ->emp_address) ? $employ->emp_address : null}}</textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    {{csrf_field()}}
                    {{-- <div class="position-relative form-group">
                        <input type="text" class="form-control" name="emp_postcode" id="emp_postcode" value="{{!empty($employ->emp_postcode) ? $employ->emp_postcode : null}}" readonly>
                    </div> --}}
                    <label for="emp_postcode" class="">รหัสไปรษณีย์</label>
                    <div class="input-group mb-3">
                        <input name="emp_postcode" id="emp_postcode" value="{{!empty($employ->emp_postcode) ? $employ->emp_postcode : null}}" type="text" class="form-control" required>
                        <div class="input-group-append">
                            <button class="btn btn-info" type="button" onclick="findaddress()">ค้นหา</button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="position-relative form-group">
                        <label for="emp_province" class="">จังหวัด</label>
                        <select name="emp_province" id="emp_province" class="form-control" required onchange="findamphure(this)">
                            <option value="">เลือกจังหวัด</option>
                            @foreach ($provinces as $province)
                                    @if ($province->id == $employ->emp_province)
                                        <option value="{{$province->id}}" selected>{{$province->name_th}}</option>
                                    @else
                                        <option value="{{$province->id}}">{{$province->name_th}}</option>
                                    @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="position-relative form-group">
                        <label for="emp_district" class="">อำเภอ</label>
                        <select name="emp_district" id="emp_district" class="form-control" required onchange="finddistric(this)" readonly>
                            <option value="">เลือกอำเภอ</option>
                            @foreach ($amphures as $amphure)
                                @if ($amphure->id == $employ->emp_district)
                                    <option value="{{$amphure->id}}" selected>{{$amphure->name_th}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="position-relative form-group">
                        <label for="emp_sub_district" class="">ตำบล</label>
                        <select name="emp_sub_district" id="emp_sub_district" class="form-control" required onchange="findzipcode(this)" readonly>
                            <option value="">เลือกตำบล</option>
                            @foreach ($Districts as $District)
                                @if ($District->id == $employ->emp_sub_district)
                                    <option value="{{$District->id}}" selected>{{$District->name_th}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="position-relative form-group">
                        <label for="emp_phone" class="">เบอร์ติดต่อ</label>
                        <input name="emp_phone" id="emp_phone" placeholder="" type="text" class="form-control"
                        value="{{!empty($employ->emp_phone) ? $employ->emp_phone : null}}">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="position-relative form-group">
                        <label for="emp_position" class="">ตำแหน่งงาน</label>
                        <select class="mb-2 form-control" name="emp_position" id="emp_position" required onclick="changeposition()">
                            <option value="">เลือกตำแหน่งงาน</option>
                            <?php
                                    if($employee->emp_position == "เจ้าของกิจการ(Owner)"){
                                ?>
                                    <option value="เจ้าของกิจการ(Owner)" @if ($employ->emp_position == 'เจ้าของกิจการ(Owner)')selected='selected'@endif>เจ้าของกิจการ(Owner)</option>
            
                                    <option value="ผู้จัดการเขตพื้นที่(Area Manager)"@if ($employ->emp_position == 'ผู้จัดการเขตพื้นที่(Area Manager)')selected='selected'@endif>ผู้จัดการเขตพื้นที่(Area Manager)</option>
            
                                    <option value="ผู้จัดการสาขา(Drop Center Manager)"@if ($employ->emp_position == 'ผู้จัดการสาขา(Drop Center Manager)')selected='selected'@endif>ผู้จัดการสาขา(Drop Center Manager)</option>
            
                                    <option value="พนักงานหน้าร้าน(Admin)"@if ($employ->emp_position == 'พนักงานหน้าร้าน(Admin)')selected='selected'@endif>พนักงานหน้าร้าน(Admin)</option>
            
                                    <option value="พนักงานส่งพัสดุ(Line Haul)"@if ($employ->emp_position == 'พนักงานส่งพัสดุ(Line Haul)')selected='selected'@endif>พนักงานส่งพัสดุ(Line Haul)</option>

                                    <option value="พนักงานจัดส่งพัสดุ(Courier)"@if ($employ->emp_position == 'พนักงานจัดส่งพัสดุ(Courier)')selected='selected'@endif>พนักงานจัดส่งพัสดุ(Courier)</option>
                                <?php
                                    }else if($employee->emp_position == "ผู้จัดการเขตพื้นที่(Area Manager)"){
                                ?>
                                    <option value="ผู้จัดการเขตพื้นที่(Area Manager)"@if ($employ->emp_position == 'ผู้จัดการเขตพื้นที่(Area Manager)')selected='selected'@endif>ผู้จัดการเขตพื้นที่(Area Manager)</option>

                                    <option value="ผู้จัดการสาขา(Drop Center Manager)"@if ($employ->emp_position == 'ผู้จัดการสาขา(Drop Center Manager)')selected='selected'@endif>ผู้จัดการสาขา(Drop Center Manager)</option>
            
                                    <option value="พนักงานหน้าร้าน(Admin)"@if ($employ->emp_position == 'พนักงานหน้าร้าน(Admin)')selected='selected'@endif>พนักงานหน้าร้าน(Admin)</option>

                                    <option value="พนักงานส่งพัสดุ(Line Haul)"@if ($employ->emp_position == 'พนักงานส่งพัสดุ(Line Haul)')selected='selected'@endif>พนักงานส่งพัสดุ(Line Haul)</option>
            
                                    <option value="พนักงานจัดส่งพัสดุ(Courier)"@if ($employ->emp_position == 'พนักงานจัดส่งพัสดุ(Courier)')selected='selected'@endif>พนักงานจัดส่งพัสดุ(Courier)</option>
                                <?php
                                    }else if($employee->emp_position == "ผู้จัดการสาขา(Drop Center Manager)"){
                                ?>
                                    <option value="ผู้จัดการสาขา(Drop Center Manager)"@if ($employ->emp_position == 'ผู้จัดการสาขา(Drop Center Manager)')selected='selected'@endif>ผู้จัดการสาขา(Drop Center Manager)</option>
                                    
                                    <option value="พนักงานหน้าร้าน(Admin)"@if ($employ->emp_position == 'พนักงานหน้าร้าน(Admin)')selected='selected'@endif>พนักงานหน้าร้าน(Admin)</option>

                                    <option value="พนักงานส่งพัสดุ(Line Haul)"@if ($employ->emp_position == 'พนักงานส่งพัสดุ(Line Haul)')selected='selected'@endif>พนักงานส่งพัสดุ(Line Haul)</option>
            
                                    <option value="พนักงานจัดส่งพัสดุ(Courier)"@if ($employ->emp_position == 'พนักงานจัดส่งพัสดุ(Courier)')selected='selected'@endif>พนักงานจัดส่งพัสดุ(Courier)</option>
                                <?php
                                    }else{
                                        if($employee->emp_position == "พนักงานหน้าร้าน(Admin)"){
                                ?>
                                            <option value="พนักงานหน้าร้าน(Admin)"@if ($employ->emp_position == 'พนักงานหน้าร้าน(Admin)')selected='selected'@endif>พนักงานหน้าร้าน(Admin)</option>
                                <?php 
                                        }else if($employee->emp_position == "พนักงานส่งพัสดุ(Line Haul)"){
                                ?>
                                            <option value="พนักงานส่งพัสดุ(Line Haul)"@if ($employ->emp_position == 'พนักงานส่งพัสดุ(Line Haul)')selected='selected'@endif>พนักงานส่งพัสดุ(Line Haul)</option>
                                <?php 
                                        }else if($employee->emp_position == "พนักงานจัดส่งพัสดุ(Courier)"){
                                ?>
                                            <option value="พนักงานจัดส่งพัสดุ(Courier)"@if ($employ->emp_position == 'พนักงานจัดส่งพัสดุ(Courier)')selected='selected'@endif>พนักงานจัดส่งพัสดุ(Courier)</option>
                                <?php 
                                        }
                                ?>

                                <?php
                                    }
                                ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                {{-- <input type="hidden" name="test" value="4" /> --}}
                <div class="col-lg-6 col-md-6">
                    <div class="position-relative form-group">
                        <label for="emp_branch_id" class="">สาขาที่ประจำการ</label>
                        <select class="mb-2 form-control" name="emp_branch_id">
                            <option value="0" >เลือกสาขาที่ประจำการ</option>
                            @foreach ($dropcenters as $dropcenter)
                            <option value="{{$dropcenter->id}}" 
                            @if ($employ->emp_branch_id == $dropcenter->id)
                            selected='selected'
                            @endif>{{$dropcenter->drop_center_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if (!empty($employ->id))
                <div class="col-lg-6 col-md-6">
                    <div class="position-relative form-group">
                        <label for="emp_status" class="">สถานะ</label>
                        <select class="mb-2 form-control" name="emp_status">
                            <option value="0">เลือกสถานะ</option>
                            <option value="1"
                            @if ($employ->emp_status == '1')
                            selected='selected'
                            @endif>ปกติ</option>
                            <option value="2"
                            @if ($employ->emp_status == '2')
                            selected='selected'
                            @endif>พ้นสภาพการเป็นพนักงาน</option>
                        </select>
                    </div>
                </div>
                @endif
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="position-relative form-group">
                        <label for="username" class="">Email (username)</label>
                        <input name="username" id="username" placeholder="" type="text"
                            class="form-control" value="{{!empty($user->email) ? $user->email : null}}" >
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="position-relative form-group">
                        <label for="password" class="">Password </label>
                        <input name="password" id="password" placeholder="กรอกรหัสผ่านใหม่หากต้องการเปลี่ยน" type="password"
                            class="form-control">
                    </div>
                </div>
            </div>
            <div class="d-block text-center"><br>
                <button class="btn-wide  btn btn-success" type="submit">แก้ไขข้อมูลพนักงาน</button>
            </div>
            </form>
            @if (isset($editProfile))
                <a href="{{url('')}}">
                    <button class="btn-wide  btn btn-light" type="reset" style="float: right;">กลับหน้าหลัก</button>
                </a>
            @else
                <a href="/employee_list/{{$employee->emp_branch_id}}">
                    <button class="btn-wide  btn btn-light" type="reset" style="float: right;">กลับ</button>
                </a>
            @endif
        </div>
    </div>
</div>

@endsection
<script>
    var position = 0;
    //     $.ajaxSetup({
    //     headers: {
    //       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //     }
    //   });
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
                $('#emp_district').attr('readonly', false);
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
                $('#emp_sub_district').attr('readonly', false);
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

    function changeposition(){
        if(position == 0){
            if(confirm("โปรดยืนยันว่าคุณต้องการเปลี่ยนตำแหน่งงาน ?")){
                position = 1;
            }
        }
    }
</script>