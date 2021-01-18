@extends("welcome")
@section("content")

<div class="col-md-6">
    <div class="main-card mb-3 card">
        <div class="card-body">
            @if (!empty($parcelPrice->id))
                <h5 class="card-title">แก้ไขอัตราค่าบริการ</h5>
                <form method="post" action="/parcelprice/{{$parcelPrice->id}}">
                    @method('PUT')
            @else
                <h5 class="card-title">อัตราค่าบริการใหม่</h5>
                <form method="post" action="/parcelprice">
            @endif
            {{csrf_field()}}
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="position-relative form-group">
                        <label for="parcel_total_weight" class="">น้ำหนัก(kg)</label>
                        <input name="parcel_total_weight" id="parcel_total_weight" placeholder="" type="text" class="form-control" value="{{!empty($parcelPrice->parcel_total_weight) ? $parcelPrice->parcel_total_weight / 1000 : null}}" autofocus required>
                    
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="position-relative form-group">
                        <label for="parcel_total_dimension" class="">Size(cm)</label>
                        <input name="parcel_total_dimension" id="parcel_total_dimension" placeholder="" type="text" class="form-control" value="{{!empty($parcelPrice->parcel_total_dimension) ? $parcelPrice->parcel_total_dimension : null}}" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="position-relative form-group">
                        <label for="parcel_type_description" class="">อัคราค่าบริการ</label>
                        <input name="parcel_price" id="parcel_price" placeholder="" type="text"
                            class="form-control" value="{{!empty($parcelPrice->parcel_price) ? $parcelPrice->parcel_price : null}}" required>
                    </div>
                </div>
            </div>
            @if (!empty($parcelPrice->id))
                <div class="row">
                    <div class="col-lg-12">
                        <div class="position-relative form-group">
                            <label for="parcel_price_status" class="">สถานะ</label>
                            <select name="parcel_price_status" id="parcel_price_status" class="form-control">
                                <option value="1">ปกติ</option>
                                <option value="0">ยกเลิก</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="d-block text-center "><br>
                    <button class="btn-wide  btn btn-success">บันทึกการแก้ไข</button>
                </div>
            @else
                <div class="d-block text-center "><br>
                    <button class="btn-wide  btn btn-primary">บันทึกข้อมูล</button>
                </div>
            @endif
            </form>
            <a href="/parcel_price_get_list/1">
                    <button class="btn-wide  btn btn-light" style="float: right;">กลับ</button>
                </a>
        </div>
    </div>
</div>

@endsection