@extends("welcome")
@section("content")

<div class="col-md-6">
    <div class="main-card mb-3 card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-md-6"><br>
                    <h4>ข้อมูลผู้รับพัสดุ</h4> 
                </div>
                <div class="col-lg-6 col-md-6"><br>
                    <form action="/receiver_search_receive" method="post">
                        {{csrf_field()}}
                        <div class="position-relative form-group">
                            <div class="input-group">
                            <input type="hidden" name="tracking_id" value="{{!empty($trackings->id) ? $trackings->id : null}}">
                                <input type="text" class="form-control" name="search_phone" placeholder="กรอกเบอร์โทรติดต่อ" required>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" @if ($trackings->booking->booking_type !== '1') disabled @endif>ค้นหา</button>
                                </div>
                            </div>
                        </div>
                    </form>    
                </div>
            </div>  
            
            <div class="row">
                <div class="col-lg-12 text-left"> 
                    {{csrf_field()}}
                    <div class="position-relative form-group">
                        <label for="exampleText" class=""> ผู้รับพัสดุ </label>
                        <textarea name="address" id="exampleText" class="form-control" readonly>{{!empty($customer->cust_name) ? $customer->cust_name : null}} {{!empty($customer->cust_address) ? $customer->cust_address : null}} {{!empty($customer->District['name_th']) ? $customer->District['name_th'] : null}} {{!empty($customer->amphure['name_th']) ? $customer->amphure['name_th'] : null}} {{!empty($customer->province['name_th']) ? $customer->province['name_th'] : null}} {{!empty($customer->cust_postcode) ? $customer->cust_postcode : null}}</textarea>
                    </div>
                </div>
            </div>
                <div class="row">
                    <div class="col-lg-12"> 
                        <div class="row">
                            <div class="col-lg-6 col-md-6 text-left">
                                <button class="btn btn-block btn-primary">รายละเอียดพัสดุที่ต้องการส่ง</button>
                            </div>
                            <div class="col-lg-6 col-md-6 text-right">
                                <button onclick="alertSelectProduct();" class="btn btn-block btn-light">สินค้าอื่นๆ</button>
                            </div>
                        </div><br>

                        <div class="main-card mb-6 card" >
                            <div class="card-body">
                                <form action="/countingPrice/{{!empty($trackings->id) ? $trackings->id : null}}" method="post">
                                    {{csrf_field()}}
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <h5 class="card-title">ประเภทพัสดุ</h5>
                                        </div>
                                        <div class="col-lg-6 col-md-6 text-right">
                                            <h5 class="card-title">ยอดเก็นเงินปลายทาง(COD)</h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <div class="position-relative form-group">
                                                @if (!empty($customer))
                                                    <select class="mb-2 form-control" name="parcelType_id">
                                                @else
                                                    <select class="mb-2 form-control" name="parcelType_id" disabled>
                                                @endif
                                                    
                                                        @if (!empty($parcelTypes))
                                                            @foreach ($parcelTypes as $parcelType)
                                                                <option value="{{$parcelType->id}}">{{$parcelType->parcel_type_name}}</option>
                                                            @endforeach
                                                        @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            {{-- {{dd($Customer_sender->cust_cod_register_status)}} --}}
                                            @if ((!empty($customer)) && ($Customer_sender->cust_cod_register_status != ""))
                                                @if ($Customer_sender->CustomerCod->cod_status == '1')
                                                    <input name="subtracking_cod" id="subtracking_cod" placeholder="กรอกยอดเก็บเงินปลายทาง (บาท)" type="text" class="form-control text-right" value="">
                                                @else
                                                    <input name="subtracking_cod" id="subtracking_cod" placeholder="กรอกยอดเก็บเงินปลายทาง (บาท)" type="text" class="form-control text-right" value="" disabled>
                                                @endif
                                            @else
                                                <input name="subtracking_cod" id="subtracking_cod" placeholder="กรอกยอดเก็บเงินปลายทาง (บาท)" type="text" class="form-control text-right" value="" disabled>
                                            @endif
                                        </div>
                                    </div><br>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <div class="position-relative form-group">
                                                <h5 class="card-title">ขนาดพัสดุ</h5>
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6">
                                                        <fieldset class="position-relative form-group">
                                                            <div class="position-relative form-check"><label class="form-check-label">
                                                                    <input name="selected_dimension_type" type="radio" class="form-check-input" value="1"
                                                                        onChange="getDimensionInputView(this)" checked> เลือกขนาดกล่อง
                                                                </label>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <fieldset class="position-relative form-group">
                                                            <div class="position-relative form-check">
                                                                <label class="form-check-label">
                                                                    @if (!empty($customer))
                                                                    <input name="selected_dimension_type" type="radio" class="form-check-input" value="2" onChange="getDimensionInputView(this)">
                                                                    @else
                                                                    <input name="selected_dimension_type" type="radio" class="form-check-input" value="2" onChange="getDimensionInputView(this)" disabled>
                                                                    @endif
                                                                    กำหนดเอง 
                                                                </label>
                                                            </div>
                                                        </fieldset>  
                                                    </div>
                                                </div>
                                                <div class="row" id="selected_parcel" style="display:block;">
                                                    <div class="col-lg-12 col-md-12">
                                                    @if (!empty($customer))
                                                        <select class="mb-2 form-control" name="selected_dimension_value">
                                                    @else
                                                        <select class="mb-2 form-control" name="selected_dimension_value" disabled>
                                                    @endif
                                                                @if (!empty($productPrices))
                                                                    @foreach ($productPrices as $productPrice)
                                                                        <option value="{{$productPrice->id}}">{{$productPrice->product_name}}  ___ ({{$productPrice->product_width}}x{{$productPrice->product_length}}x{{$productPrice->product_hight}})</option>
                                                                    @endforeach
                                                                @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div id="dimention_parcel" style="display:none;">
                                                    <div class="alert alert-info fade show" role="alert">
                                                        <div class="row">
                                                            <div class="col-lg-6 col-md-6">
                                                                <div class="position-relative form-group">
                                                                    <label for="width" class="width"> กว้าง</label>
                                                                    <input style="min-width: 60px;" name="width" id="width" placeholder="0" type="text"
                                                                        class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6">
                                                                <div class="position-relative form-group">
                                                                    <label for="hight" class="hight"> สูง</label>
                                                                    <input style="min-width: 60px;" name="hight" id="hight" placeholder="0" type="text"
                                                                        class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6">
                                                                <div class="position-relative form-group">
                                                                    <label for="length" class="length">ยาว</label>
                                                                    <input style="min-width: 60px;" name="length" id="length" placeholder="0" type="text"
                                                                        class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="position-relative form-group"> <br>
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12 text-right">
                                                        <fieldset class="position-relative form-group">
                                                            <div class="position-relative form-check">
                                                                <label class="form-check-label">น้ำหนัก(กรัม)</label>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                                <div class="row" >
                                                    <div class="col-lg-12 col-md-12 ">
                                                    @if (!empty($customer))
                                                        <input name="weigth" id="weigth" placeholder="กรอกน้ำหนัก(กรัม)" type="text" class="form-control text-right" required>
                                                    @else
                                                        <input name="weigth" id="weigth" placeholder="กรอกน้ำหนัก(กรัม)" type="text" class="form-control text-right" required disabled>
                                                    @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body"> 
                                        <input type="hidden" name="subtracking_no" value="202004002-0001">
                                        <input type="hidden" name="subtracking_tracking_id" value="{{!empty($trackings->id) ? $trackings->id : null}}">
                                        <input type="hidden" name="branch_id" value="1">
                                        @if (!empty($customer))
                                            @if ($trackings->booking->booking_type == '1')
                                                <button type="submit" class="mt-1 btn btn-primary">คำนวณราคา </button>
                                            @else  
                                                <button type="button" class="mt-1 btn btn-light">คำนวณราคา </button>
                                            @endif
                                        @else
                                            <button type="button" class="mt-1 btn btn-light">คำนวณราคา </button>
                                        @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="row">
        <div class="col-lg-12">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <h5 class="card-title">รวมรายการพัสดุ สำหรับผู้รับรายนี้
                    </h5>
                    <div class="table-responsive scrollbar" style="max-width: 100%; height: 465px; overflow: scroll; overflow-x: hidden;">
                        <table class="mb-0 table">
                            <thead>
                                <tr>
                                    {{-- <th width="10%" class="text-left">ID</th> --}}
                                    <th width="5%" class="text-left" style="border-bottom: 1px solid #000 !important;">ลำดับ</th>
                                    <th width="25%" class="text-left" style="border-bottom: 1px solid #000 !important;">รายการ</th>
                                    <th width="20%" class="text-left" style="border-bottom: 1px solid #000 !important;">รายละเอียดพัสดุ</th>
                                    <th width="15%" class="text-left" style="border-bottom: 1px solid #000 !important;">น้ำหนัก(g)</th>
                                    <th width="15%" class="text-right" style="border-bottom: 1px solid #000 !important;">ยอดเงิน</th>
                                    <th width="20%" class="text-center" style="border-bottom: 1px solid #000 !important;">ลบ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 0; 

                                @endphp
                               @if (!empty($subTrackingList))
                                    @foreach ($subTrackingList as $subTracking)
                                        @php
                                            $i++; 
                                        @endphp
                                        @if ($subTracking->subtracking_dimension_type != '-')
                                            <tr>
                                                {{-- <th scope="row">{{$subTracking->id}}</th> --}}
                                                <td class="text-left" style="border-top:1px solid #818181 !important;">
                                                    {{ $i }}
                                                </td>
                                                <td class="text-left" style="border-top:1px solid #818181 !important;">
                                                    {{$subTracking->parceltype->parcel_type_name}}
                                                </td>
                                                <td class="text-left" style="border-top:1px solid #818181 !important;">
                                                    @php
                                                        $dimension = App\Model\DimensionHistory::where('dimension_history_subtracking_id',$subTracking->id)->first();
                                                    @endphp
                                                    @if ($dimension)
                                                        {{$dimension->dimension_history_width}}x{{$dimension->dimension_history_length}}x{{$dimension->dimension_history_hight}}
                                                    @endif
                                                </td>
                                                <td class="text-left" style="border-top:1px solid #818181 !important;">{{number_format($dimension->dimension_history_weigth, 0)}}</td>
                                                <td class="text-right" style="border-top:1px solid #818181 !important;">
                                                    {{number_format($subTracking->subtracking_price, 2)}}
                                                </td>
                                                <td class="text-center" style="border-top:1px solid #818181 !important;">
                                                    @if ($trackings->booking->booking_type == '1') 
                                                        <a href="/delete_subtracking/{{$subTracking->id}}">
                                                            <button type="submit" class="mb-2 mr-2 border-0 btn-transition btn btn-outline-danger">ลบ</button>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @if($subTracking->subtracking_cod  != '0')
                                                <tr>
                                                    {{-- <th scope="row">{{$subTracking->id}}</th> --}}
                                                    <td class="text-right" style="border:none !important;">

                                                    </td>
                                                    <td class="text-right" style="border:none !important;">

                                                    </td>
                                                    <td>
                                                        <strong>COD</strong><span class="pull-right">{{ number_format($subTracking->subtracking_cod, 2) }}</span>
                                                    </td>
                                                    <td class="text-right">
                                                        <strong>COD_fee</strong>
                                                    </td>
                                                    <td class="text-right">
                                                        {{number_format($subTracking->subtracking_cod_fee, 2)}}
                                                    </td>
                                                    <td class="text-center">
                                                        
                                                    </td>
                                                </tr>
                                            @endif
                                        @endif
                                    @endforeach
                                    @endif
                                    @if (!empty($saleOtherList))
                                    @foreach ($saleOtherList as $saleOther)
                                        @php
                                            $i++; 
                                        @endphp
                                        <tr>
                                            {{-- <th scope="row">{{$saleOther->id}}</th> --}}
                                            <td class="text-left" style="border-top:1px solid #818181 !important;">
                                                {{ $i }}
                                            </td>
                                            <td class="text-left" style="border-top:1px solid #818181 !important;">
                                                {{$saleOther->productPrice->product_name}}
                                            </td>
                                            <td class="text-left" style="border-top:1px solid #818181 !important;"></td>
                                            <td class="text-left" style="border-top:1px solid #818181 !important;"></td>
                                            <td class="text-right" style="border-top:1px solid #818181 !important;">
                                                {{number_format($saleOther->sale_other_price, 2)}}
                                            </td>
                                            <td class="text-center" style="border-top:1px solid #818181 !important;">
                                                @if ($trackings->booking->booking_type == '1') 
                                                    <a href="/deleteProductInList/{{$saleOther->id}}">
                                                        <button type="button" class="mb-2 mr-2 border-0 btn-transition btn btn-outline-danger">ลบ</button>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        {{-- <th scope="row">{{$saleOther->id}}</th> --}}
                                        <td class="text-center" colspan='4' style="border-top:1px solid #818181 !important;">รวม</td>
                                        <td class="text-right" style="border-top:1px solid #818181 !important;">
                                            {{number_format($trackings->tracking_amount, 2)}}
                                        </td>
                                        <td class="text-center" style="border-top:1px solid #818181 !important;"></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table><br><br>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:-20px;">
                <div class="col-lg-12 col-md-12 text-right">
                    <div class="card-body">
                        @if (!empty($subTrackingList))
                            @if (!empty($saleOtherList))
                                @php
                                    $count_subTrackingList = count($subTrackingList);
                                    $count_saleOtherList = count($saleOtherList);
                                    $total_count = $count_subTrackingList + $count_saleOtherList;
                                @endphp
                                @if ($total_count>0)
                                    @if ($trackings->booking->booking_type == '1')
                                        <a href="/updateTrackingDetailList/{{$trackings->id}}">
                                            <button type="button" class="mt-1 btn btn-primary">บันทึกรายการ</button>
                                        </a>
                                    @else  
                                        <button type="button" class="mt-1 btn btn-light">บันทึกรายการ</button>
                                    @endif
                                @else   
                                    <button class="mt-1 btn btn-light">บันทึกรายการ</button>
                                @endif
                            @endif
                         @endif
                         @if ($i <= 0)
                            <a href="/connectBooking/{{!empty($trackings->tracking_booking_id) ? $trackings->tracking_booking_id : null}}">
                                <button class="mt-1 btn btn-light">กลับ</button>
                            </a>
                         @else
                            <button class="mt-1 btn btn-light" disabled>กลับ</button>
                         @endif
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@if (!empty($trackings->id))
@if (!empty($customer))
    
<script>
    function alertSelectProduct(){
        // var token = $('#token').val();
        Swal.fire({
            type: 'warning',
            title: 'เลือกสินค้าอื่นๆ',
            showCancelButton: false,
            showConfirmButton: false,
            reverseButtons: false,
            html:   '<form action="/addProductToOrderList/{{$trackings->id}}" method="post">'+
                    // '<input type="hidden" name="_token" value="'+token+'">'+
                    '<input type="hidden" name="tracking_id" value="{{!empty($trackings->id) ? $trackings->id : null}}">'+
                    '{{csrf_field()}}'+
                    '<div class="row">'+
                        '<div class="col-lg-12 col-md-12 text-center">'+
                            '<table class="mb-0 table" width="100%">'+
                                '<thead>'+
                                    '<tr>'+
                                        '<th width="30%" class="text-left">รายการสินค้า</th>'+
                                        '<th width="30%" class="text-center">ขนาด</th>'+
                                        '<th width="30%" class="text-right">ราคา</th>'+
                                        '<th width="10%" class="text-center">เลือก</th>'+
                                    '</tr>'+
                                '</thead>'+
                                '<tbody>'+
                                    ' @if (!empty($productPrices))'+
                                        '@foreach ($productPrices as $productPrice)'+
                                            '<tr>'+
                                                '<td class="text-left">'+
                                                    '{{$productPrice->product_name}}'+
                                                '</td>'+
                                                '<td class="text-center">{{$productPrice->product_width}} x {{$productPrice->product_hight}} x {{$productPrice->product_length}}</td>'+
                                                '<td class="text-right">{{number_format($productPrice->product_price,2)}}</td>'+
                                                '<td class="text-right">'+
                                                // '<input type="text" name="product_id" value="{{$productPrice->id}}">'+
                                                '<a href="/addProductToOrderList/{{$trackings->id}}/{{$productPrice->id}}">'+
                                                    '<button type="button" class="mb-2 mr-2 border-0 btn-transition btn btn-outline-success">เลือก</button></td>'+
                                                '</a>'+
                                            '</tr>'+
                                        '@endforeach'+
                                    '@endif'+
                                '</tbody>'+
                            '</table>'+
                        '</div>'+
                    '</div>'+
                '</form>'
        });
    }
</script>
@endif
@endif


<script type="text/javascript">
function getDimensionInputView(x) {
    if (x.value == '1') {
        document.getElementById("dimention_parcel").style.display = 'none'; 
        document.getElementById("selected_parcel").style.display = 'block'; 
    } else {
        document.getElementById("dimention_parcel").style.display = 'block';
        document.getElementById("selected_parcel").style.display = 'none'; 
    }
}
</script>

@endsection