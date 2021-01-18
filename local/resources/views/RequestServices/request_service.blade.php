@extends("welcome")
@section("content")

<div class="col-md-6">
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5 class="card-title">รับพัสดุใหม่</h5>
            <!-- <form class="#" method="post" action="/customer"> -->
            {{csrf_field()}}
            <!--Token ของแบบฟอร์ม-->

            <div class="position-relative form-group"><label for="name" class="">ชื่อผู้ส่งพัสดุ</label>
                <input name="name" id="name" placeholder="" type="text" class="form-control">
            </div>
            <div class="position-relative form-group"><label for="exampleText" class="">ที่อยู่ผู้ส่ง
                </label><textarea name="address" id="exampleText" class="form-control"></textarea>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="position-relative form-group"><label for="phone" class="">ตำบล</label><input
                            name="phone" id="phone" placeholder="" type="text" class="form-control">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="position-relative form-group"><label for="phone" class="">อำเภอ</label><input
                            name="phone" id="phone" placeholder="" type="text" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="position-relative form-group"><label for="phone" class="">จังหวัด</label><input
                            name="phone" id="phone" placeholder="" type="text" class="form-control">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="position-relative form-group"><label for="phone" class="">รหัสไปรษณีย์</label><input
                            name="phone" id="phone" placeholder="" type="text" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="row">
                        <form action="">
                            <div class="col-lg-12 col-md-12">
                                <div class="position-relative form-group"><label for="phone"
                                        class="">เบอร์โทรติดต่อ</label>
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
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="position-relative form-group"><label for="phone"
                            class="">เบอร์สำรอง(ถ้ามี)</label><input name="phone" id="phone" placeholder="" type="text"
                            class="form-control">
                    </div>
                </div>
            </div>
            <!-- </form> -->
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5 class="card-title"> ที่อยู่ที่ให้เข้ารับพัสดุ </h5>
            <fieldset class="position-relative form-group">
                <div class="position-relative form-check"><label class="form-check-label">
                        <input name="radio1" type="radio" class="form-check-input" value="0" onChange="getValue(this)"
                            checked> ที่อยู่เดียวกับ ที่อยู่ผู้ส่งสินค้า</label>
                </div>
                <div class="position-relative form-check"><label class="form-check-label">
                        <input name="radio1" type="radio" class="form-check-input" value="1" onChange="getValue(this)">
                        ที่อยู่ใหม่ </label>
                </div>
            </fieldset>
            <div id="newAddressInputView" style="display:none;">
                <div class="alert alert-info fade show" role="alert">
                    <div class="position-relative form-group"><label for="exampleText" class="">ที่อยู่ผู้ส่ง
                        </label><textarea name="address" id="exampleText" class="form-control"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="position-relative form-group"><label for="sub-district"
                                    class="">ตำบล</label><input name="sub-district" id="sub-district" placeholder=""
                                    type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="position-relative form-group"><label for="district" class="">อำเภอ</label><input
                                    name="district" id="district" placeholder="" type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="position-relative form-group"><label for="province"
                                    class="">จังหวัด</label><input name="province" id="province" placeholder=""
                                    type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="position-relative form-group"><label for="phone"
                                    class="">รหัสไปรษณีย์</label><input name="phone" id="phone" placeholder=""
                                    type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="position-relative form-group"><label for="qty_parcel"
                            class="">จำนวนพัสดุที่ต้องการส่ง</label><input name="qty_parcel" id="qty_parcel"
                            placeholder="" type="text" class="form-control">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <h5 class="card-title">เลือก Courier ที่รับผิดชอบ</h5>
                    <select class="mb-2 form-control">
                        <option>เลือก Couries </option>
                        <option>นิรันดร แสงทอง</option>
                        <option>นิรุต กอไก่</option>
                        <option>สมชาย สบายดี</option>
                    </select>
                </div>
            </div>

            </form>
        </div>
    </div>


    <!-- <div class="row">
        <div class="col-lg- col-md-4">
            <a href="/request_service_create_detail">
                <button class="mt-1 btn btn-primary">เพิ่มรายการพัสดุ (ถ้ามี)</button>
            </a>
        </div>
    </div>   -->
    <div class="row">
        <div class="col-lg-12">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title">รายการพัสดุที่ต้องการส่ง
                    </h5>
                    <div class="table-responsive">
                        <table class="mb-0 table">
                            <thead>
                                <tr>
                                    <th width="10%" class="text-left">No</th>
                                    <th width="60%" class="text-left">รายการ</th>
                                    <th width="20%" class="text-left">ยอดเงิน</th>
                                    <th width="10%" class="text-left">ลบ</th>
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
            <div class="row">
                <div class="col-lg-4 col-md-4"><a href="/request_service_create_detail">
                        <button class="mt-1 btn btn-primary">เพิ่มรายการพัสดุ</button>
                    </a> </div>
                <div class="col-lg-4 col-md-4 text-right">
                    <h4>รวมยอดค่าส่ง</h4>
                </div>
                <div class="col-lg-3 col-md-3 text-right">
                    <h2> 0.00 </h2>
                </div>
                <div class="col-lg-1 col-md-1 text-right"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="card-body">
            <button class="mt-1 btn btn-primary" onclick="alertSuccess();">บันทึกการเรียกรถเข้ารับพัสดุ</button>
            <a href="/request_service_list">
                <button class="mt-1 btn btn-secondary">ยกเลิก</button>
            </a>
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">
function getValue(x) {
    if (x.value == '0') {
        document.getElementById("newAddressInputView").style.display = 'none'; // you need a identifier for changes
    } else {
        document.getElementById("newAddressInputView").style.display = 'block'; // you need a identifier for changes
    }
}
</script>
@endsection