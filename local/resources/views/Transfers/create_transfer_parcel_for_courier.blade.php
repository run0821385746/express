@extends("welcome")
@section("content")

<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header">Transfer To Courier</div>
       
        <div class="row">
            <div class="col-lg-1 col-md-1 text-right"><br> พนักงานส่ง </div>
            <div class="col-lg-4 col-md-4"> <br>
                <div class="position-relative form-group">
                    <div class="input-group">
                        <h4>{{!empty($employeecurier->emp_firstname) ? $employeecurier->emp_firstname : null}} {{!empty($employeecurier->emp_lastname) ? $employeecurier->emp_lastname : null}}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-1 col-md-1 text-right"><br> Tracking No</div>
            <div class="col-lg-3 col-md-3"> <br>
                <div class="position-relative form-group">
                <form action="/addTrackingToCourier/{{!empty($employeecurier->id) ? $employeecurier->id : null}}" method="POST">
                        {{csrf_field()}}
                        @method('PUT')
                        <div class="input-group">
                            {{-- <input type="hidden" class="form-control" name="transfer_bill_id" value="{{!empty($TranserBill->id) ? $TranserBill->id : null}}"> --}}
                            <input type="text" class="form-control" name="tracking_no" autofocus
                            @if (!empty($TranserBillHasBeenOpen))
                                @if (substr($TranserBillHasBeenOpen->created_at, 0,10) != date('Y-m-d'))
                                    value="กรุณาทำการปิดบิลของรอบที่แล้ว" disabled
                                @endif
                            @endif
                            >
                            <input type="hidden" name="courier_id" value="{{!empty($employeecurier->id) ? $employeecurier->id : null}}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" 
                                    @if (!empty($TranserBillHasBeenOpen))
                                        @if (substr($TranserBillHasBeenOpen->created_at, 0,10) != date('Y-m-d'))
                                            disabled
                                        @endif
                                    @endif
                                >ค้นหา</button>
                            </div>
                        </div>
                    </form>   
                </div>
            </div>
            <div class="col-lg-8 col-md-8 text-right"><br>
                <button class="mb-2 mr-2 btn btn-primary">จำนวนTracking<span class="badge badge-pill badge-light">
                        @if (!empty($transfers))
                            {{count($transfers)}}
                        @else
                            0
                        @endif
                    </span>
                </button>
                {{-- <button class="mb-2 mr-2 btn btn-success">จัดส่งสำเร็จ<span
                        class="badge badge-pill badge-light">
                        @if (!empty($parcelReceiveDonelList))
                            {{count($parcelReceiveDonelList)}}
                        @else
                            0
                        @endif
                    </span></button>
                <button class="mb-2 mr-2 btn btn-danger">ติดปัญหา<span
                        class="badge badge-pill badge-light">
                        @if (!empty($parcelWrongList))
                            {{count($parcelWrongList)}}
                        @else
                            0
                        @endif
                    </span></button> --}}
            </div>
        </div>
        <div class="table-responsive">
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    @if(!empty($transfers))
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="10%" class="text-center">Booking No</th>
                            <th width="15%" class="text-center">Tracking No</th>
                            <th width="15%" class="text-center">จำนวนพัสดุ</th>
                            <th width="15%" class="text-center">สถานะเบิก</th>
                            <th width="10%" class="text-center">COD</th>
                            <th width="10%" class="text-center">ทำรายการ</th>
                        </tr>
                    @endif
                </thead>
                <tbody>
                    @php
                        $btn = 0;
                    @endphp
                    @if (!empty($transfers))
                        @php
                            $i = 1;
                            $total_cod_amount = 0;
                            $btn = 1;
                        @endphp
                        @foreach ($transfers as $transfer)
                            @php
                                $subtrackingrecivearray = explode(",", $transfer->parcel_received_amount);
                                sort($subtrackingrecivearray);
                            @endphp
                            @for ($iforbox = 0; $iforbox < count($subtrackingrecivearray); $iforbox++)
                                @if ($iforbox == 0)
                                    <tr>
                                        <td class="text-center text-muted">{{$i++}}</td>
                                        <td class="text-center">{{$transfer->booking->booking_no}}</td>
                                        <td class="text-center">
                                            {{$transfer->tracking->tracking_no}}@if (strpos($transfer->transfer_status, 'Return') !== false)(RTN)@endif      
                                        </td>
                                        <td class="text-center">
                                            @if ($transfer->parcel_received_amount == null)
                                                0/{{$transfer->parcel_amount}}
                                            @else
                                                {{$subtrackingrecivearray[$iforbox].'/'.$transfer->parcel_amount}}
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (count($subtrackingrecivearray) == $transfer->parcel_amount)
                                                <span style="color: green">กำลังทำเบิกให้ CR </span>
                                            @else
                                                @php
                                                    $btn = 0;
                                                @endphp
                                                <span style="color: rgb(248, 141, 0)">พัสดุยังไม่ครบจำนวน</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $trackings = App\Model\SubTracking::where('subtracking_tracking_id',$transfer->tracking->id)->where('subtracking_under_tracking_id', $subtrackingrecivearray[$iforbox])->first();
                                            @endphp
                                            {{number_format($trackings->subtracking_cod,2)}}
                                        </td>
                                        <td class="text-center" style="padding: 0px;" rowspan="{{count($subtrackingrecivearray)}}">
                                            <form action="/deleteParcelWhenTransferToCurrire" method="POST" style="margin-bottom: 0px;">
                                                {{csrf_field()}}
                                                <input type="hidden" name="tracking_id" value="{{!empty($transfer->tracking->id) ? $transfer->tracking->id : null}}">
                                                <input type="hidden" name="currier_id" value="{{!empty($employeecurier->id) ? $employeecurier->id : null}}">
                                                <button class="border-0 btn-transition btn btn-outline-danger">ลบ</button>
                                            </form>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="text-center text-muted">{{$i++}}</td>
                                        <td class="text-center">{{$transfer->booking->booking_no}}</td>
                                        <td class="text-center">
                                            {{$transfer->tracking->tracking_no}}@if (strpos($transfer->transfer_status, 'Return') !== false)(RTN)@endif          
                                        </td>
                                        <td class="text-center">
                                            @if ($transfer->parcel_received_amount == null)
                                                0/{{$transfer->parcel_amount}}
                                            @else
                                                {{$subtrackingrecivearray[$iforbox].'/'.$transfer->parcel_amount}}
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (count($subtrackingrecivearray) == $transfer->parcel_amount)
                                                <span style="color: green">กำลังทำเบิกให้ CR </span>
                                            @else
                                                @php
                                                    $btn = 0;
                                                @endphp
                                                <span style="color: rgb(248, 141, 0)">พัสดุยังไม่ครบจำนวน</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $trackings = App\Model\SubTracking::where('subtracking_tracking_id',$transfer->tracking->id)->where('subtracking_under_tracking_id', $subtrackingrecivearray[$iforbox])->first();
                                            @endphp
                                            {{number_format($trackings->subtracking_cod,2)}}
                                        </td>
                                    </tr>
                                @endif
                            @endfor
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center "><font color="red">ไม่พบรายการ</font></td>
                        </tr>
                    @endif

                    <div style="position:fixed; bottom:0px; right:10px; width:300px; z-index:1;" class="card">
                        <div class="card-header bg-primary" id="duplicates_tap">
                            รายการแสกนซ้ำ
                        </div>
                        <div class="card-body" style="padding:0px; height:200px; overflow:auto;" id="duplicates_body">
                            @if (count($TransfersDuplicates) > 0)
                                <ul class="list-group">
                                    @foreach ($TransfersDuplicates as $TransfersDuplicate)
                                        <li class="list-group-item">
                                            <b style="color:red;">{{$TransfersDuplicate->duplicate_tracking_no}}</b>
                                            <br>
                                            <small>
                                                {{date_format($TransfersDuplicate->created_at,"d/m/Y H:i:s")}}
                                            </small>
                                            <span class="pull-right">
                                                @if ($TransfersDuplicate->duplicate_status == '1')
                                                    <small>ไม่พบรายการ Tracking</small>
                                                    {{-- <small>รายการซ้ำ</small> --}}
                                                @elseif ($TransfersDuplicate->duplicate_status == '2')
                                                    <small>ทำจ่ายซ้ำ</small>
                                                @elseif ($TransfersDuplicate->duplicate_status == '3')
                                                    <small>รายการนี้เบิกไปจ่ายแล้ว</small>
                                                @elseif ($TransfersDuplicate->duplicate_status == '4')
                                                    <small>ปลายทางรับแล้ว</small>
                                                @elseif ($TransfersDuplicate->duplicate_status == '5')
                                                    <small>ไม่อยู่ในสถานะทำเบิกจ่าย</small>
                                                @elseif ($TransfersDuplicate->duplicate_status == '6')
                                                    <small>พื้นที่COURIER หรือปลายทางไม่ถูกต้อง</small>
                                                @else
                                                    <small>Tracking ไม่อยู่ในรายการจัดส่ง</small>
                                                @endif
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div align='center' style="padding-top:20px;">Empty...</div>
                            @endif
                        </div>
                    </div>
                </tbody>
            </table> <br><br><br>
            <div class="row">
                <div class="col-lg-12 col-md-12 text-left">
                    <div class="card-body">
                        <form action="/saveTransferToCourier/{{!empty($employeecurier->id) ? $employeecurier->id : null}}" method="POST">
                            {{csrf_field()}}
                            <div class="row">
                                <div class="col-lg-9 col-md-12"></div>
                                <div class="col-lg-3 col-md-4">
                                    <input type="text" class="form-control mb-2 mr-sm-2" name="tranfer_driver_sender_numberplate" id="tranfer_driver_sender_numberplate" placeholder="ทะเบียนรถผู้ส่งสินค้า" required 
                                        @if (!empty($TranserBillHasBeenOpen))
                                            @if (substr($TranserBillHasBeenOpen->created_at, 0,10) == date('Y-m-d'))
                                                value="{{$TranserBillHasBeenOpen->tranfer_driver_sender_numberplate}}" readonly 
                                            @endif
                                        @endif
                                    >
                                </div>
                            </div>
                            <input type="hidden" name="id" id="id" value="">
                            <button style="display: none;" id='submitsavecorier'></button>
                        </form>
                        @if ($btn == 1)
                            @if (!empty($TranserBillHasBeenOpen))
                                @if (substr($TranserBillHasBeenOpen->created_at, 0,10) != date('Y-m-d'))
                                    <button class="mt-1 btn btn-primary" disabled>บันทึกรายการ</button>
                                @else
                                    <button class="mt-1 btn btn-primary" id="btnclicksubmit">บันทึกรายการ</button>
                                @endif
                            @else
                                <button class="mt-1 btn btn-primary" id="btnclicksubmit">บันทึกรายการ</button>
                            @endif
                        @else
                            <button class="mt-1 btn btn-primary" disabled>บันทึกรายการ</button>
                        @endif
                        <a href="#" data-toggle="modal" data-target="#exampleModalCenter" target="blank">
                            <button class="mt-1 btn btn-success">พิมพ์ใบ Delivery Record</button>
                        </a>
                        <a href="/getCurierList/{{$employeecurier->emp_branch_id}}">
                            <button class="mt-1 btn btn-light">กลับ</button>
                        </a>
                        </form>
                        <input type="hidden" name="_token" id="token" value="{{ csrf_token()}}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $("#btnclicksubmit").click(function(){
        $("#submitsavecorier").trigger( "click" );
        if($("#tranfer_driver_sender_numberplate").val() !==''){
            $("#btnclicksubmit").attr('Disabled', true);
        }
    });
    $("#tranfer_driver_sender_numberplate").keyup(function(){
        if($("#tranfer_driver_sender_numberplate").val() !==''){
            $("#btnclicksubmit").attr('Disabled', false);
        }else{
            $("#btnclicksubmit").attr('Disabled', true);
        }
    });
    function alertSubmitCod(){
        var token = $('#token').val();  
        Swal.fire({
            type: 'warning',
            title: 'ยอดเก็บเงินปลายทาง',
            showCancelButton: false,
            showConfirmButton: false,
            reverseButtons: false,
            html:   '<form action="/closeJosbCurrier" method="POST">'+
                    '{{csrf_field()}}'+
                    '<input type="hidden" name="_token" value="'+token+'">'+
                    '<div class="row"><br><br>'+
                        '<div class="col-lg-12 col-md-12 text-left">'+
                        '<input type="hidden" name="currier_id" value="{{!empty($employeecurier->id) ? $employeecurier->id : null}}">'+
                            '<div class="position-relative form-group">'+
                                '<div class="row">'+
                                    '<div class="col-lg-6 col-md-6 text-left">ยอดเก็บเงินปลายทาง</div>'+
                                    '<div class="col-lg-6 col-md-6 text-right"><h4> '+
                                        '@if (!empty($total_cod_amount)) '+
                                            '{{number_format($total_cod_amount,2)}}'+
                                        ''+
                                        '@endif '+
                                    '</h4>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="row">'+
                        '<div class="col-lg-12 col-md-12">'+
                            '<button type="submit" class="mt-1 btn btn-primary">บันทึกยอดรับเงินจาก Currier {{!empty($employeecurier->id) ? $employeecurier->id : null}}</button>'+
                        '</div>'+
                    '</div>'+
                    '</form>'
        });
    }

    function alertShowDetail(){
        var token = $('#token').val();  
        Swal.fire({
            type: 'warning',
            title: 'ข้อมูลผู้รับพัสดุ',
            showCancelButton: false,
            showConfirmButton: false,
            reverseButtons: false,
            html:   '<form action="/closeJosbCurrier" method="POST">'+
                    '{{csrf_field()}}'+
                    '<input type="hidden" name="_token" value="'+token+'">'+
                    '<div class="row"><br><br>'+
                        '<div class="col-lg-12 col-md-12 text-left">'+
                        '<input type="hidden" name="currier_id" value="{{!empty($employeecurier->id) ? $employeecurier->id : null}}">'+
                            '<div class="position-relative form-group">'+
                                '<label for="province" class="">ข้อมูลผู้รับพัสดุ</label>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '</form>'
        });
    }

    function alertHistoryWrongDetail(){
        var token = $('#token').val();  
        Swal.fire({
            type: 'warning',
            title: 'ประวัติการติดต่อ',
            showCancelButton: false,
            showConfirmButton: false,
            reverseButtons: false,
            html:   '<form action="/closeJosbCurrier" method="POST">'+
                    '{{csrf_field()}}'+
                    '<input type="hidden" name="_token" value="'+token+'">'+
                    '<div class="row"><br><br>'+
                        '<div class="col-lg-12 col-md-12 text-left">'+
                        '<input type="hidden" name="currier_id" value="{{!empty($employeecurier->id) ? $employeecurier->id : null}}">'+
                            '<div class="position-relative form-group">'+
                                '<label for="province" class="">ข้อมูลผู้รับพัสดุ</label>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                  
                    '</form>'
        });
    }

    $(document).ready(function(){
        $("#submitsavetransfer").click(function(){
            $("#submitsavetranferform").trigger("click");
        });

        $("#duplicates_tap").click(function(){
            duplicates_body = $("#duplicates_body").is(":hidden");
            if(duplicates_body == false){
                $("#duplicates_body").hide();
            }else{
                $("#duplicates_body").show();
            }
        });
    });
    <?php
        $Duplicates_Qty = count($TransfersDuplicates);
        if($Duplicates_Qty == Session::get('Duplicates_Qty') && Session::get('Duplicates_Name') == "tranfer_courier"){
    ?>
        $("#duplicates_body").hide();
    <?php
        }
        Session::put('Duplicates_Qty', $Duplicates_Qty);
        Session::put('Duplicates_Name', "tranfer_courier");
    ?>

</script>

@endsection
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header text-white bg-success">
          <h5 class="modal-title" id="exampleModalLongTitle">Delivery Record Daily List</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
                <table width='100%'>
                    <thead>
                        <tr>
                            <th>ที่</th>
                            <th>รหัส</th>
                            <th>เวลา</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($TranserBillList as $key=>$BillList)
                            <tr>
                                <td>{{($key+1)}}</td>
                                <td>{{$BillList->transfer_bill_no}}</td>
                                <td>{{ date_format($BillList->created_at,"d/m/Y H:i:s") }}</td>
                                <td align='center'><a href="/printDeleveryReport/{{!empty($BillList->id) ? $BillList->id : null}}" target="blank"><i class='fa fa-print' aria-hidden='true'></i></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-dismiss="modal">ปิด</button>
        </div>
      </div>
    </div>
</div>