@extends("welcome")
@section("content")

<div class="col-md-6">
    <div class="main-card mb-3 card">
        <div class="card-body">
            @if (!empty($productPrice->id))
                <h5 class="card-title">แก้ไขราคากล่องพัสดุ</h5>
                <form method="post" action="/product_price/{{$productPrice->id}}">
                    @method('PUT')
            @else
                <h5 class="card-title">กำหนดราคากล่องพัสดุ</h5>
                <form method="post" action="/product_price">
            @endif
                {{csrf_field()}}
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="position-relative form-group">
                            <label for="product_name" class="">ชื่อสินค้า</label>
                            <input name="product_name" id="product_name" placeholder="" type="text"
                        class="form-control" value="{{!empty($productPrice->product_name) ? $productPrice->product_name : null}}" autofocus required>
                        </div>
                    </div>  
                </div>
                <div class="alert alert-info fade show" role="alert">
                    <div class="row">
                        <div class="col-lg-3 col-md-3">
                            <div class="position-relative form-group">
                                <label for="product_width" class="product_width"> กว้าง</label>
                                <input name="product_width" id="product_width" placeholder="ระบุ" type="text" class="form-control" 
                                value="{{!empty($productPrice->product_width) ? $productPrice->product_width : null}}" required>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="position-relative form-group">
                                <label for="product_length" class="product_length">ยาว</label>
                                <input name="product_length" id="product_length" placeholder="ระบุ" type="text" class="form-control"
                                value="{{!empty($productPrice->product_length) ? $productPrice->product_length : null}}" required>
                            
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="position-relative form-group">
                                <label for="product_hight" class="product_hight"> สูง</label>
                                <input name="product_hight" id="product_hight" placeholder="ระบุ" type="text" class="form-control"
                                value="{{!empty($productPrice->product_hight) ? $productPrice->product_hight : null}}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="product_price" class="">ราคาที่กำหนด</label>
                            <input name="product_price" id="product_price" placeholder="" type="text"
                                class="form-control" value="{{!empty($productPrice->product_price) ? $productPrice->product_price : null}}" required>

                        </div>
                    </div>
                </div>
                @if (!empty($productPrice->id))
                    <div class="d-block text-center"><br>
                        <button class="btn-wide  btn btn-success">บันทึกการแก้ไข</button>
                    </div>
                @else 
                    <div class="d-block text-center"><br>
                        <button class="btn-wide  btn btn-primary">บันทึกข้อมูล</button>
                    </div>
                @endif
            </form>
            <a href="/product_price_get_list/1">
                <button class="btn-wide  btn btn-light" style="float: right;">กลับ</button>
            </a>
        </div>
    </div>
</div>

@endsection