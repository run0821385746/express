@extends("welcome")
@section("content")

<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header">กำหนดราคากล่องพัสดุ
        </div>
        <div class="table-responsive">
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>
                        <th width="10%" class="text-center">รหัสสินค้า</th>
                        <th width="20%" class="text-left">ชื่อสินค้า</th>
                        <th width="30%" class="text-center">รายละเอียด(กว้าง x ยาว x สูง)</th>
                        <th width="10%" class="text-center">dimension</th>
                        <th width="10%" class="text-right">ราคาขาย</th>
                        <th width="10%" class="text-right">สถานะ</th>
                        <th width="10%" class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productPrices as $productPrice)
                        <tr>
                        <td class="text-center text-muted">{{$productPrice->id}}</td>
                            <td class="text-left">{{$productPrice->product_name}}</td>
                            <td class="text-center">{{$productPrice->product_width}} x {{$productPrice->product_length}} x {{$productPrice->product_hight}}</td>
                            <td class="text-center">{{$productPrice->product_dimension}}</td>
                            <td class="text-right">{{number_format($productPrice->product_price,2)}}</td>
                            <td class="text-right">
                                @if ($productPrice->product_price_status == '1')
                                    ปกติ 
                                @else
                                    ยกเลิก 
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="/product_price/{{$productPrice->id}}">
                                <button type="button" id="PopoverCustomT-1"
                                    class="btn btn-primary btn-sm">ตั้งค่าใหม่</button>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table><br><br>
        </div>
        <div class="d-block text-left card-footer">
            <div class="row">
             <!--    <div class="col-lg-6 col-md-6 text-right">
                    <nav class="" aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item"><a href="javascript:void(0);" class="page-link"
                                    aria-label="Previous"><span aria-hidden="true">«</span><span
                                        class="sr-only">Previous</span></a></li>
                            <li class="page-item"><a href="javascript:void(0);" class="page-link">1</a></li>
                            <li class="page-item active"><a href="javascript:void(0);" class="page-link">2</a></li>
                            <li class="page-item"><a href="javascript:void(0);" class="page-link">3</a></li>
                            <li class="page-item"><a href="javascript:void(0);" class="page-link"
                                    aria-label="Next"><span aria-hidden="true">»</span><span
                                        class="sr-only">Next</span></a></li>
                        </ul>
                    </nav>
                </div> -->
                <div class="col-lg-6 col-md-6 text-right">
                    <a href="/product_price_create">
                        <button class="btn-wide  btn btn-primary">เพิ่มราคากล่องพัสดุ</button>
                    </a>
                    <button class="btn-wide  btn btn-success">Export Documents</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection