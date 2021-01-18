@extends("welcome")
@section("content")

<div class="col-md-6">
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5 class="card-title">รับพัสดุใหม่</h5>
            <h5 class="card-title">เพิ่มข้อมูลผู้รับ</h5>
            <!-- <form class="#" method="post" action="/customer"> -->
            {{csrf_field()}}
            <!--Token ของแบบฟอร์ม-->
            <div class="position-relative form-group">
                <label for="sender_name" class="">ชื่อผู้รับพัสดุ</label>
                <input name="sender_name" id="sender_name" placeholder="" type="text" class="form-control">
            </div>
            <div class="position-relative form-group">
                <label for="exampleText" class="">ที่อยู่ผู้รับ </label>
                <textarea name="address" id="exampleText" class="form-control"></textarea>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="position-relative form-group">
                        <label for="sub_district" class="">ตำบล</label>
                        <input name="sub_district" id="sub_district" placeholder="" type="text" class="form-control">
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
                    <form action="/customer_search">
                        <div class="col-lg-12 col-md-12">
                            <div class="position-relative form-group"><label for="phone" class="">เบอร์โทรติดต่อ</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="phone">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary">ค้นหา</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="position-relative form-group">
                        <label for="COD" class="">ยอดเก็นเงินปลายทาง(COD)</label>
                        <input name="COD" id="COD" placeholder="" type="text" class="form-control">
                    </div>
                </div>
            </div>
            <!-- </form> -->
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="row">
        <div class="col-lg-12">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <h5 class="card-title">ประเภทพัสดุ</h5>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <h5 class="card-title">ขนาดกล่อง</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="position-relative form-group"><br>
                                <select class="mb-2 form-control">
                                    <option>เลือกประเภทพัสดุ </option>
                                    <option>-</option>
                                    <option>-</option>
                                    <option>-</option>
                                    <option>-</option>
                                    <option>-</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="position-relative form-group">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <fieldset class="position-relative form-group">
                                            <div class="position-relative form-check"><label class="form-check-label">
                                                    <input name="radio1" type="radio" class="form-check-input" value="0"
                                                        onChange="getDimensionInputView(this)" checked> เลือกขนาดกล่อง
                                                </label>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <fieldset class="position-relative form-group">
                                            <div class="position-relative form-check"><label class="form-check-label">
                                                    <input name="radio1" type="radio" class="form-check-input" value="1"
                                                        onChange="getDimensionInputView(this)"> กำหนดเอง </label>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                                <select class="mb-2 form-control">
                                    <option>เลือกขนาดกล่อง </option>
                                    <option>-</option>
                                    <option>-</option>
                                    <option>-</option>
                                    <option>-</option>
                                    <option>-</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="dimention_parcel" style="display:none;">
                        <div class="alert alert-info fade show" role="alert">
                            <div class="row">
                                <div class="col-lg-3 col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="width" class="width"> กว้าง</label>
                                        <input name="width" id="width" placeholder="ระบุ" type="text"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="hight" class="hight"> สูง</label>
                                        <input name="hight" id="hight" placeholder="ระบุ" type="text"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="length" class="length">ยาว</label>
                                        <input name="length" id="length" placeholder="ระบุ" type="text"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="weight" class="weight">น้ำหนัก</label>
                                        <input name="weight" id="weight" placeholder="ระบุ" type="text"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <a href="/request_service_create_detail">
                        <button class="mt-1 btn btn-primary">เพิ่ม</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title">รวมรายการพัสดุ สำหรับผู้รับรายนี้
                    </h5>
                    <div class="table-responsive">
                        <table class="mb-0 table">
                            <thead>
                                <tr>
                                    <th width="10%" class="text-left">No</th>
                                    <th width="70%" class="text-left">รายการ</th>
                                    <th width="20%" class="text-left">ยอดเงิน</th>
                                    <th width="20%" class="text-center">ลบ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row"></th>
                                    <td></td>
                                    <td></td>
                                    <td class="text-center">
                                        <button class="mb-2 mr-2 border-0 btn-transition btn btn-outline-danger">
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"></th>
                                    <td></td>
                                    <td></td>
                                    <td class="text-center">
                                        <button class="mb-2 mr-2 border-0 btn-transition btn btn-outline-danger">
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="card-body">
            <a href="/request_service">
                <button class="mt-1 btn btn-success">บันทึกรายการ</button>
            </a>
            <a href="/request_service">
                <button class="mt-1 btn btn-light">ยกเลิก</button>
            </a>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
function getDimensionInputView(x) {
    if (x.value == '0') {
        document.getElementById("dimention_parcel").style.display = 'none'; // you need a identifier for changes
    } else {
        document.getElementById("dimention_parcel").style.display = 'block'; // you need a identifier for changes
    }
}
</script>

@endsection