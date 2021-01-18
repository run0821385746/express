@extends("welcome")
@section("content")

<div class="col-md-6">
    <div class="main-card mb-3 card">
        <div class="card-body">
            @if (!empty($parcelType->id))
                <h5 class="card-title">แก้ไขประเภทพัสดุใหม่</h5>
                <form method="post" action="/parceltype/{{$parcelType->id}}" method="post">
                @method('PUT')
            @else
                <h5 class="card-title">เพิ่มประเภทพัสดุใหม่</h5>
                <form method="post" action="/parceltype" method="post">
            @endif

            {{csrf_field()}}
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="position-relative form-group">
                        <label for="parcel_type_name" class="">ชื่อประเภทพัสดุ</label>
                        <input name="parcel_type_name" id="parcel_type_name" placeholder="" type="text"
                            class="form-control" value="{{!empty($parcelType->parcel_type_name) ? $parcelType->parcel_type_name : null}}" autofocus required>
                    </div>
                </div>
            </div>
            <div class="row">   
                <div class="col-lg-12">
                    <div class="position-relative form-group">
                        <label for="parcel_type_description" class="">รายละเอียดเงื่อนไขที่เกี่ยวข้อง</label>
                        <textarea name="parcel_type_description" id="parcel_type_description" placeholder="" type="text" class="form-control">{{!empty($parcelType->parcel_type_description) ? $parcelType->parcel_type_description : null}}
                        </textarea>
                    </div>
                </div>
            </div>  
            @if (!empty($parcelType->id))
                <div class="d-block text-center "><br>
                    <button class="btn-wide  btn btn-success">บันทึกการแก้ไข</button>
                </div>
            @else
                <div class="d-block text-center "><br>
                    <button class="btn-wide  btn btn-primary">บันทึกข้อมูล</button>
                </div>
            @endif
            </form>
            <a href="/parceltype_get_list/1">
                    <button class="btn-wide  btn btn-light" style="float: right;">กลับ</button>
                </a>
        </div>
    </div>
</div>

@endsection