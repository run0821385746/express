@extends("input")
@section("content")

<div class="col-lg-12 col-md-12">
    <div class="main-card  card">
        <div class="card-header">ข้อมูลลูกค้าfffff
        </div>
        <div class="table-responsive">
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>
                        <th width="10%" class="text-center">รหัสลูกค้า</th>
                        <th width="20%" class="text-left">ชื่อลูกค้า</th>
                        <th width="40%" class="text-left">ที่อยู่ลูกค้า</th>
                        <th width="10%" class="text-left">รหัสไปรษณีย์</th>
                        <th width="10%" class="text-left">เบอร์ติดต่อ</th>
                        <th width="10%" class="text-center">ทำรายการ</th>
                    </tr>  
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    <tr>
                        <td class="text-center text-muted">{{$customer->id}}</td>
                        <td class="text-left">{{$customer->cust_name}}</td>
                        <td class="text-left"> {{$customer->cust_address}} {{$customer->cust_province}}</td>
                        <td class="text-left">{{$customer->cust_postcode}}</td>
                        <td class="text-left">{{$customer->cust_phone}}</td>
                        <td class="text-center">

                            {{-- <form action="/updateSenderBooking/{{$customer->id}}" method="post">
                                {{csrf_field()}}
                                @method('PUT')
                                <input type="hidden" name="branch_id" value="1">
                                <input type="hidden" name="customer_id" value="{{$customer->id}}">
                                <button type="button" class="btn btn-danger btn-sm">เลือก </button>
                            </form> --}}
    
                        </td>
                    </tr>
                    @endforeach   
                </tbody>
            </table>
        </div>
        <div class="d-block text-left card-footer">
        <button id="button" type="button" class="btn btn-primary ">เพิ่มลูกค้าใหม่</button>
            <a href="/receive_add_parcel"><button class="btn-wide  btn btn-light "> ยกเลิก </button></a>
        </div>
    </div>
</div>

<div id="new_customer" class="col-md-6" style="display:none;"><br><br>
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5 class="card-title">เพิ่มข้อมูลูกค้าใหม่</h5>
            <form class="#" action="/customer" method="post">
                {{csrf_field()}}
                <div class="position-relative form-group">
                    <label for="cust_name" class="">ชื่อลูกค้า</label>
                    <input name="cust_name" id="cust_name" placeholder="" type="text" class="form-control">
                </div>
                <div class="position-relative form-group">
                    <label for="exampleText" class="">ที่อยู่ </label>
                    <input name="cust_address" id="exampleText" class="form-control">
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="sub_district" class="">ตำบล</label>
                            <input name="sub_district" id="sub_district" placeholder="" type="text"
                                class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="district" class="">อำเภอ</label>
                            <input name="district" id="district" placeholder="" type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">  
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="province" class="">จังหวัด</label>
                            <input name="province" id="province" placeholder="" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="postcode" class="">รหัสไปรษณีย์</label>
                            <input name="postcode" id="postcode" placeholder="" type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="cust_phone" class="">เบอร์โทร</label>
                            <input name="cust_phone" id="cust_phone" placeholder="" type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <button type="submit" class="mt-1 btn btn-success">บันทึกลูกค้าใหม่</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$('#button').click(function() {
    $('#new_customer').toggle();
})
</script>

@endsection