@extends("welcome")
@section("content")

<div class="col-md-6">
    <div class="alert alert-info fade show" role="alert">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <h4>ข้อมูลผู้ส่งพัสดุ</h4>
                    </div>
                    <div class="col-lg-6 col-md-6 text-right">
                        {{csrf_field()}}
                        <label for="booking_no">BookingNo: {{!empty($bookingData->booking_no) ? $bookingData->booking_no : null }} <br>
                            @if (!empty($bookingData->id))
                                @if ($bookingData->booking_type=='1')
                                    BookingType: พัสดุรับหน้าร้าน
                                @else
                                    BookingType: เรียกรถรับพัสดุ
                                @endif
                            @endif
                        </label>
                    </div>
                </div>  
                <br>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <form action="/customer_search" method="post">  
                            {{csrf_field()}}
                            <div class="position-relative form-group">  
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search_phone" placeholder=" กรอกเบอร์โทรติดต่อ"
                                        value="{{!empty($customer->cust_phone) ? $customer->cust_phone : null}}" autofocus required>
                                    <div class="input-group-append">
                                    <input type="hidden" name="booking_id" value="{{!empty($bookingData->id) ? $bookingData->id : null }}">
                                        @if (!empty($bookingData->id))
                                            @if ($bookingData->booking_type == '2')
                                                @if (count($trackingList) == 0)
                                                    <button type="submit" class="btn btn-primary">ค้นหา</button>
                                                @else
                                                    <button type="button" class="btn btn-primary" disabled>ค้นหา</button>
                                                @endif
                                            @else
                                                @if (count($trackingList) == 0)
                                                    <button type="submit" class="btn btn-primary">ค้นหา</button>
                                                @else
                                                    <button type="button" class="btn btn-primary" disabled>ค้นหา</button>
                                                @endif
                                            @endif
                                        @else
                                            <button type="submit" class="btn btn-primary">ค้นหา</button>
                                        @endif
                                    </div>
                                </div>
                            </div>  
                        </form>  
                    </div> 
                    <div class="col-lg-6 col-md-6">
                        {{csrf_field()}}
                        <div class="position-relative form-group">
                            <div class="row">
                                <div class="col-lg-12 text-right">
                                    {{-- <button type="button" onClick="addCustomer();" class="btn btn-lg btn-primary">เพิ่มข้อมูลลูกค้า</button> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="position-relative form-group">
                    <label for="sender_name" class="">ชื่อผู้ส่งพัสดุ</label>
                    <input name="sender_name" id="sender_name" placeholder="" type="text" class="form-control"
                        value="{{!empty($customer->cust_name) ? $customer->cust_name : old('sender_name')}}" readonly>
                </div>
                <div class="position-relative form-group">
                    <label for="exampleText" class="">ที่อยู่ผู้ส่ง </label>
                    <textarea name="address" id="exampleText" class="form-control" readonly>{{!empty($customer->cust_address) ? $customer->cust_address : null}}</textarea>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="sub_district" class="">ตำบล</label>
                            <input name="sub_district" id="sub_district" placeholder="" type="text" class="form-control"
                                value="{{!empty($customer->District['name_th']) ? $customer->District['name_th'] : null}}"
                                readonly>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="district" class="">อำเภอ</label>
                            <input name="district" id="district" placeholder="" type="text" class="form-control"
                                value="{{!empty($customer->amphure['name_th']) ? $customer->amphure['name_th'] : null}}" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="province" class="">จังหวัด</label>
                            <input name="province" id="province" placeholder="" type="text" class="form-control"
                                value="{{!empty($customer->province['name_th']) ? $customer->province['name_th'] : null}}" readonly>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="position-relative form-group">
                            <label for="postcode" class="">รหัสไปรษณีย์</label>
                            <input name="postcode" id="postcode" placeholder="" type="text" class="form-control"
                                value="{{!empty($customer->cust_postcode) ? $customer->cust_postcode : null}}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="alert alert-dark fade show" role="alert">
        <div class="row">
            <div class="col-lg-4 col-md-4 text-right">
                <h4>ยอดรวมค่าบริการ</h4>
            </div>
            <div class="col-lg-8 col-md-8 text-right">
                @if (!empty($bookingData->id))  
                    <h2> {{!empty($bookingData->booking_amount) ? number_format($bookingData->booking_amount, 2) : "0.00" }} </h2>
                    <table>
                        <thead>
                            <tr>
                                <th width="15%" class="text-left"></th>
                                <th width="55%" class="text-left"></th>
                                <th width="20%" class="text-right"></th>
                                <th width="10%" class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2" class="text-center" style='font-weight:bold;'>รับเงิน/บาท</th>
                                <td colspan="2" class="text-right" style='font-weight:bold;'>
                                    <div class="position-relative form-group">
                                        <input name="receive_moneyinput" id="receive_moneyinput" onkeyup="getchangeresukt(this)" type="number" class="form-control" value="">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-center" style='font-weight:bold;'>เงินทอน/บาท</th>
                                <td colspan="2" class="text-right" style='font-weight:bold; font-size:20px;'><span id="showchange">0.00</span></td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <h2> 0.00 </h2>
                @endif
            </div>
        </div>
    </div>
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h4 class="card-title">รายการพัสดุที่ต้องการส่ง
            </h4>
            <div class="table-responsive" style="max-width: 100%; height: 250px; overflow: scroll; overflow-x: hidden;">
                <table class="mb-0 table">
                    <thead>
                        <tr>
                            <th width="15%" class="text-left">Tracking No</th>
                            <th width="55%" class="text-left">ผู้รับพัสดุ</th>
                            <th width="20%" class="text-right">ยอดเงิน</th>
                            <th width="10%" class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $checktrack_empty = 1;
                        @endphp
                        @if (!empty($trackingList))
                            @foreach ($trackingList as $tracking)
                                <tr>
                                    <th scope="row">
                                        {{-- @if (empty($bookingData->RequestService->id))  --}}
                                            <a href="/getTrackingDetailFormTrackingId/{{$tracking->id}}">
                                                @if ($tracking->tracking_no !== '')
                                                    {{$tracking->tracking_no}}
                                                @else
                                                    กล่อง
                                                @endif
                                            </a>
                                        {{-- @else --}}
                                            {{-- {{$tracking->tracking_no}} --}}
                                        {{-- @endif --}}
                                    </th>
                                    <td class="text-left">
                                        @if ($tracking->tracking_receiver_id != '-')
                                        {{$tracking->receiver->cust_name}}
                                        @endif
                                       
                                    </td>
                                    <td class="text-right">
                                        {{number_format($tracking->tracking_amount, 2)}}
                                        @if ($tracking->tracking_amount <= 0)
                                            @php
                                                $checktrack_empty = 0;
                                            @endphp
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if ($bookingData->booking_type == '1') 
                                            <a href="/delete_tracking/{{$tracking->id}}">
                                                <button class="mb-2 mr-2 border-0 btn-transition btn btn-outline-danger">ลบ</button>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach   
                        @endif
                        {{-- <tr>
                            <td colspan="2" class="text-center" style='font-weight:bold;'>รวม</th>
                            <td class="text-right" style='font-weight:bold;'>{{!empty($bookingData->booking_amount) ? number_format($bookingData->booking_amount, 2) : "0.00" }}</td>
                            <td style='font-weight:bold;'>บาท</td>
                        </tr>
                        <tr>
                            <th scope="row"></th>
                            <td></td>
                            <td></td>
                            <td class="text-center">
                                <button class="mb-2 mr-2 border-0 btn-transition btn btn-outline-danger">ฟหก
                                </button>
                            </td>
                        </tr> --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 col-md-4">
            @if (!empty($bookingData->id))
                @if ($bookingData->booking_type=='1')
                    @if (!empty($bookingData->id))
                        <a href="/getTrackingDetail/{{$bookingData->id}}">
                    @else
                        <a href="/getTrackingDetail">
                    @endif
                        <button type="button" class="mt-1 btn btn-primary">เพิ่มรายการพัสดุ</button>
                    </a>
                @else
                    <button type="button" class="mt-1 btn btn-light">เพิ่มรายการพัสดุ</button>
                @endif
            @endif
        </div>
        @if (!empty($bookingData->id))
            @if ($bookingData->booking_type=='2')
                <div class="col-lg-8 col-md-8" align="right">  
                        @if (count($trackingList) == 0)
                            <button type="button" onClick="addCurrier();" class="mt-1 btn btn-primary">บันทึกการเรียกรถเข้ารับพัสดุ</button>
                        @else
                            <button type="button" class="mt-1 btn btn-light">บันทึกการเรียกรถเข้ารับพัสดุ</button>
                        @endif
            @else
                <div class="col-lg-8 col-md-8" align="right">
                    @if (count($trackingList)>0)
                        <form action="/saveAndCloseBookingJobs" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" name='id' id="id" value="{{!empty($bookingData->id) ? $bookingData->id : null }}">
                            <input type="hidden" name='receive_money' id="receive_money" >
                            <button type="submit" id='saveAndCloseBookingJobsInForm' class="mt-1 btn btn-primary" style="display: none;"></button>
                        </form>
                        @if ($checktrack_empty == 0)
                            <button class="mt-1 btn btn-primary" disabled>บันทึกและออกใบเสร็จรับเงิน</button>
                        @else
                            <button id='saveAndCloseBookingJobsForm' class="mt-1 btn btn-primary">บันทึกและออกใบเสร็จรับเงิน</button>
                        @endif
                    @else
                        <button type="button" class="mt-1 btn btn-light">บันทึกและออกใบเสร็จรับเงิน</button>
                    @endif
            @endif
        @else
            <div class="col-lg-8 col-md-8" align="right">
        @endif
            <a href="/bookingList/{{$employee->emp_branch_id}}">
                <button class="mt-1 btn btn-light">กลับไปหน้ารายการ</button>
            </a>
        </div>
        <input type="hidden" name="_token" id="token" value="{{ csrf_token()}}">
        <input type="hidden" name="_bookingId" id="bookingId" value="{{!empty($bookingData->id) ? $bookingData->id : null }}">
   </div>
</div>
{{-- </div> --}}
    {{-- @php --}}
        
    {{-- @endphp --}}
<script>
    $("#saveAndCloseBookingJobsForm").attr('disabled', true);
    $("#receive_moneyinput").val('');
    function currencyFormat (num) {
        return num.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
    }
    function getchangeresukt(receive_money){
        totle = {{!empty($bookingData->booking_amount) ? number_format($bookingData->booking_amount, 2) : "0.00" }};
        receive = receive_money.value-totle;
        $("#receive_money").val(receive_money.value);
        if(receive_money.value >= totle){
            $("#showchange").html(currencyFormat(receive));
            $("#saveAndCloseBookingJobsForm").attr('disabled', false);
        }else{
            $("#saveAndCloseBookingJobsForm").attr('disabled', true);
            $("#showchange").html("ยอดชำระไม่ครบ !!");
        }
        // result = receive-totle;
    }
    $("#saveAndCloseBookingJobsForm").click(function(){
        receive = $("#receive_moneyinput").val();
        // window.open('{{url('previewSlipReceiveParcel/')}}/{{!empty($bookingData->id) ? $bookingData->id : null }}/'+receive, '_blank');
        $("#saveAndCloseBookingJobsInForm").trigger( "click" );
    });
    function addCurrier(){
        var bookingId = $('#bookingId').val();
        var token = $('#token').val();  
        Swal.fire({
            type: 'warning',
            title: 'เลือก Courier  ที่ต้องการให้รับงานนี้',
            showCancelButton: false,
            showConfirmButton: false,
            reverseButtons: false,
            html:   '<form action="/saveRequestServiceBookingJobs/{{!empty($bookingData->id) ? $bookingData->id : null }}" method="POST">'+
                    '{{csrf_field()}}'+
                    '<input type="hidden" name="_token" value="'+token+'">'+
                    '<input type="hidden" name="booking_id" value="'+bookingId+'">'+
                    '@if (!empty($bookingData->id))'+
                    '@if ($bookingData->booking_type=='2')'+
                        '<div class="row"><br><br>'+
                            '<div class="col-lg-12 col-md-12 text-left">'+
                                '<div class="position-relative form-group">'+
                                    '<label for="province" class="">เลือก courier ที่ต้องการให้เข้ารับพัสดุ</label>'+
                                    '<div class="row" id="request_currier_id" style="display:block;">'+
                                        '<div class="col-lg-12 col-md-12">'+
                                            '<select class="mb-2 form-control" name="request_currier_id" required>'+
                                                '<option value="">เลือก courier</option>'+
                                                '@if (!empty($currierList))'+
                                                    '@foreach ($currierList as $currier)'+
                                                        '@if (!empty($bookingData->RequestService->id))'+
                                                            '@if ($bookingData->RequestService->request_currier_id == "$currier->id")'+
                                                                '<option value="{{$currier->id}}" selected>{{$currier->emp_firstname}} {{$currier->emp_lastname}}</option>'+
                                                            '@else'+
                                                                '<option value="{{$currier->id}}">{{$currier->emp_firstname}} {{$currier->emp_lastname}}</option>'+
                                                            '@endif'+
                                                        '@else'+
                                                            '<option value="{{$currier->id}}">{{$currier->emp_firstname}} {{$currier->emp_lastname}}</option>'+
                                                        '@endif'+
                                                    '@endforeach'+
                                                '@endif'+
                                            '</select>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                        '<div class="row"><br><br>'+
                            '<div class="col-lg-12 col-md-12 text-left">'+
                                '<div class="position-relative form-group">'+
                                    '<label for="province" class=""> จำนวนพัสดุที่ต้องการให้เข้ารับ</label>'+
                                    '<div class="row" id="request_parcel_qty" style="display:block;">'+
                                        '<div class="col-lg-12 col-md-12">'+
                                            '<select class="mb-2 form-control" name="request_parcel_qty" required>'+
                                                '<option value="">เลือกจำนวน</option>'+
                                                    '<option value="1" @if (!empty($bookingData->RequestService->id)) @if($bookingData->RequestService->request_parcel_qty == "1") selected @endif @endif>1 ชิ้น</option>'+
                                                    '<option value="2" @if (!empty($bookingData->RequestService->id)) @if($bookingData->RequestService->request_parcel_qty == "2") selected @endif @endif>2 ชิ้น</option>'+
                                                    '<option value="3" @if (!empty($bookingData->RequestService->id)) @if($bookingData->RequestService->request_parcel_qty == "3") selected @endif @endif>3 ชิ้น</option>'+
                                                    '<option value="4" @if (!empty($bookingData->RequestService->id)) @if($bookingData->RequestService->request_parcel_qty == "4") selected @endif @endif>4 ชิ้น</option>'+
                                                    '<option value="5" @if (!empty($bookingData->RequestService->id)) @if($bookingData->RequestService->request_parcel_qty == "5") selected @endif @endif>5-10 ชิ้น</option>'+
                                                    '<option value="6" @if (!empty($bookingData->RequestService->id)) @if($bookingData->RequestService->request_parcel_qty == "6") selected @endif @endif>มากว่า 10 ชิ้น</option>'+
                                            '</select>'+
                                        '</div>'+
                                    '</div>'+
                               ' </div>'+
                            '</div>'+
                        '</div>'+
                    '@endif'+
                '@endif'+
                '<div class="row">'+
                    '<div class="col-lg-12 col-md-12">'+
                        '<button type="submit" class="mt-1 btn btn-primary">บันทึกการเรียกรถเข้ารับพัสดุ</button>'+
                    '</div>'+
                '</div>'+
            '</form>'
        });
    }

    
</script>

<style>
    .swal2-popup{width: 600px;}
</style>

<!-- <script>
    $('#button').click( function(){
        $('p').toggle();
    })
</script> -->

<!-- <script>
 $('#button').click( function(){
        $('.joy').toggle();      ถ้าเป็น class ให้ใส่ . หน้าชื่อ  
         $('p').toggle();         ถ้าเป็น < > ใส่ type ได้เลย 
         $('#joy').toggle();      ถ้าเป็น id ใส่ # หน้าชื่อ 
     })
</script> -->

@endsection