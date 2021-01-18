@extends("welcome")
@section("content")

<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header">
            ข้อมูลพัสดุรับใหม่
            <span style="position: absolute; right:10px;">
                @php
                    $dateToday = date('Y-m-d');
                @endphp
                <input type="date" name="select_bill_date" id="select_bill_date" onchange="select_bill_date_js(this)" value="{{ $date = null? '':$date}}" class="form-control" />
            </span>
        </div>
        <div class="table-responsive">  

            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>  
                        <th width="5%" class="text-center">No</th>
                        <th width="10%" class="text-center">บิลเลขที่</th>
                        <th width="15%" class="text-left"> ข้อมูลผู้ส่ง</th>
                        <th width="15%" class="text-center"> ประเภทรับงาน</th>
                        <th width="10%" class="text-center">สถานะการเปิดงาน</th>
                        <th width="10%" class="text-right">ค่าบริการ</th>
                        <th width="10%" class="text-center">เวลาทำรายการ</th>
                        <th width="5%" class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                @php
                    $i = 1;
                @endphp
                <tbody>
                    @if (!empty($bookings))
                        @foreach ($bookings as $booking)
                            <tr>  
                                <td class="text-center text-muted">{{$i++}}</td>
                                <td class="text-center text-muted">
                                    @if ($booking->booking_status == "done" || $booking->booking_status=="request-done")
                                        <a href="/getReceiveDetail/{{$booking->id}}">
                                            {{$booking->booking_no}}
                                        </a>
                                    @else
                                        {{$booking->booking_no}}
                                    @endif
                                </td>
                                <td class="text-left">{{$booking->customer->cust_name}}</td>
                                <td class="text-center">
                                    @if ($booking->booking_type=='1')
                                    พัสดุรับหน้าร้าน
                                    @else
                                    เรียกรถเข้ารับพัสดุ
                                    @endif
                                </td>
                                
                                <td class="text-center"> 
                                    @if ($booking->booking_status == "new")
                                        <a href="/connectBooking/{{$booking->id}}" title="ดำเนินการต่อ">
                                            <font color="blue">รับงานใหม่</font>
                                        </a>
                                    @elseif ( $booking->booking_status == "request" )
                                       <font color="#E67E22">เรียกรถเข้ารับพัสดุ</font>
                                    @elseif ( $booking->booking_status == "fail")
                                        @if (empty($booking->RequestService->id))
                                            <font color="#F13B27">เข้ารับพัสดุไม่สำเร็จ</font>
                                        @else
                                            <a href="#" onclick="failview('{{$booking->booking_no}}','{{$booking->RequestService->action_status}}')"><font color="#F13B27">เข้ารับพัสดุไม่สำเร็จ</font></a>
                                        @endif
                                    @elseif ( $booking->booking_status == "request-done")
                                        <font color="๒F13B27">รอรับเข้าสาขา</font>
                                    @else
                                        <font color="green">สำเร็จ</font>
                                    @endif
                                </td>
                                <td class="text-right">{{number_format($booking->booking_amount, 2)}}</td>
                                <td class="text-center">{{$booking->created_at}}</td>
                                <td class="text-center">
                                    @if ($booking->booking_status == "done" || $booking->booking_status=="request-done")
                                        <a href="/previewTrackingBarcode_all_booking/{{$booking->id}}" target="blank" title="พิมพ์ Label">
                                            <button type="submit" id="PopoverCustomT-1" class="btn btn-primary btn-sm">
                                                <i class="metismenu-icon pe-7s-note2"></i>
                                            </button>
                                        </a>
                                        <a href="/previewSlipReceiveParcel/{{$booking->id}}" onload="windown.print()" target="blank" title="พิมพ์ใบเสร็จ">
                                            <button type="submit" id="PopoverCustomT-1" class="btn btn-success btn-sm">
                                                <i class="metismenu-icon pe-7s-note2"></i>
                                            </button>
                                        </a>
                                    @else
                                        <a href="/connectBooking/{{$booking->id}}" title="ดำเนินการต่อ">
                                            <button type="submit" id="PopoverCustomT-1" class="btn btn-primary btn-sm">
                                                <i class="metismenu-icon pe-7s-note2"></i>
                                            </button>
                                        </a>
                                    @endif
                                </td>
                            </tr>  
                        @endforeach  
                    @endif
                </tbody>
            </table><br><br>
        </div>
        <div class="d-block card-footer">
            <div class="row">
                <div class="col-lg-6 col-md-6 text-right">
                    {{-- <ul class="pagination">
                        <li class="page-item"><a href="javascript:void(0);" class="page-link"
                                aria-label="Previous"><span aria-hidden="true">«</span><span
                                    class="sr-only">Previous</span></a></li>
                        <li class="page-item"><a href="javascript:void(0);" class="page-link">1</a></li>
                        <li class="page-item active"><a href="javascript:void(0);" class="page-link">2</a></li>
                        <li class="page-item"><a href="javascript:void(0);" class="page-link">3</a></li>
                        <li class="page-item"><a href="javascript:void(0);" class="page-link"
                                aria-label="Next"><span aria-hidden="true">»</span><span
                                    class="sr-only">Next</span></a></li>
                    </ul> --}}
                </div>
             
                <div class="col-lg-6 col-md-6 text-right"> 
                    
                    <a href ="/create_receive_jobs" class="mm-active">
                        <button class="btn-wide  btn btn-primary"> เพิ่มรายการใหม่ </button>
                    </a>

                    <a href ="/previewDailyReport" class="mm-active" target="blank">
                        <button class="btn-wide  btn btn-primary"> สรุปยอดการรับพัสดุประจำวัน </button>
                    </a>

                    <a href ="/getSaleOtherList" class="mm-active" >
                        <button class="btn-wide  btn btn-primary"> ยอดขายสินค้าอื่นๆประจำวัน </button>
                    </a>

                    <a href="/export">
                        <button class="btn-wide  btn btn-success"> export to excel </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>  

<script>
    function select_bill_date_js(date){
        // alert(date.value);
        window.location.href = '{{url('bookingList')}}/{{$employee->emp_branch_id}}/'+date.value;
    }

    function failview(b_no, action_status){
        Swal.fire({
            icon: 'error',
            title: action_status,
            text: b_no
        })
    }

    function open_btn_slip_barcode(id){
        Swal.fire({
            icon: 'info',
            title: 'Slip print , Label Print',
            showCancelButton: true,
            showConfirmButton: false,
            reverseButtons: false,
            html:   '<div class="row">'+
                    '<div class="col-lg-12 col-md-12 text-center">'+
                        '<button class="btn btn-primary btn-lg" Onclick="print_label(\''+id+'\')" style="font-size:16px;">'+
                            'Label Print <i class="fa fa-tag" aria-hidden="true"></i>'+
                        '</button>'+
                        '&nbsp;'+
                        '<button class="btn btn-success btn-lg" Onclick="print_slip(\''+id+'\')" style="font-size:16px;">'+
                            'Slip print <i class="fa fa-clipboard" aria-hidden="true"></i>'+
                        '</button>'+
                    '</div>'+
                '</div>'
        });
        // Swal.fire({
        //     title: 'Do you want to save the changes?'+id,
        //     showDenyButton: true,
        //     showCancelButton: true,
        //     confirmButtonText: `Save`,
        //     denyButtonText: `Don't save`,
        // }).then((result) => {
        //     if (result.isConfirmed) {
        //         window.open('{{url('previewTrackingBarcode_all_booking/')}}/'+id, '_blank');
        //         return false;
        //     } else if (result.isDenied) {
        //         window.open('{{url('previewSlipReceiveParcel/')}}/'+id, '_blank');
        //         return false;
        //     }
        // })
    }

    function print_label(id){
        window.open('{{url('previewTrackingBarcode_all_booking/')}}/'+id, '_blank');
    }
    
    function print_slip(id){
        window.open('{{url('previewSlipReceiveParcel/')}}/'+id, '_blank');
    }
</script>
@endsection