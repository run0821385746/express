@extends("welcome")
@section("content")  

<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header">Receive Parcel From Drop Center
        </div>
        <div class="row">
                <div class="col-lg-1 col-md-1 text-right"><br> Tracking No</div>
                <div class="col-lg-3 col-md-3"> <br>
                    <div class="position-relative form-group">
                        <form action="/checkSendingStatusParcel/{{!empty($parcelBillId) ? $parcelBillId : null}}" method="POST">
                            {{csrf_field()}}
                            @method('PUT')
                            <div class="input-group">
                            <input type="text" class="form-control" name="tracking_no" autofocus>
                            <input type="hidden" name="parcelBillId" value="{{!empty($parcelBillId) ? $parcelBillId : null}}">
                            <div class="input-group-append">
                                <button class="btn btn-primary">ค้นหา</button>
                            </div>
                            </div>
                        </form>  
                    </div>
                </div>
                <div class="col-lg-8 col-md-8 text-right"><br>
            <button class="mb-2 mr-2 btn btn-primary">จำนวนTracking<span
                    class="badge badge-pill badge-light">
                    @if (!empty($parcelDetailList))
                        {{count($parcelDetailList)}}
                    @else
                        0
                    @endif
                </span>
            </button>
            <button class="mb-2 mr-2 btn btn-success">รับสำเร็จ<span
                    class="badge badge-pill badge-light">
                    @if (!empty($parcelReceiveDonelList))
                        {{count($parcelReceiveDonelList)}}
                    @else
                        0
                    @endif
                
                </span>
            </button>
            <button class="mb-2 mr-2 btn btn-danger">ติดปัญหา<span
                    class="badge badge-pill badge-light">
                    @if (!empty($parcelWrongList))
                        {{count($parcelWrongList)}}
                    @else
                        0
                    @endif
                </span>
            </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>
                        <th width="10%" class="text-center">No</th>
                        <th width="15%" class="text-center">Tracking No</th>
                        <th width="5%" class="text-center">กล่องที่</th>
                        <th width="20%" class="text-center">ชื่อผู้ส่ง</th>
                        <th width="20%" class="text-center">ชื่อผู้รับ</th>
                        <th width="15%" class="text-center">สถานะ</th>
                        <th width="15%" class="text-center">ทำรับแล้ว</th>
                        <th width="10%" class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($parcelDetailList))
                        @php
                            $rowNo = 1;
                            $tranferready = 1;
                        @endphp
                        @foreach ($parcelDetailList as $parcelDetail)
                            @php
                                $subtrackingarray = explode(",", $parcelDetail->parcel_received_amount);
                                sort($subtrackingarray);
                            @endphp
                            @for ($i = 0; $i < count($subtrackingarray); $i++)
                                @if ($i == 0)
                                    <tr>
                                        <td class="text-center">
                                            {{$rowNo++}}
                                        </td>
                                        <td class="text-center ">{{$parcelDetail->transfer_dropcenter_tracking_no}}</td>
                                        <td class="text-center ">
                                                {{$subtrackingarray[$i].'/'.$parcelDetail->parcel_amount}}
                                        </td>
                                        <td class="text-center">{{$parcelDetail->dc_sender->drop_center_name}}</td>
                                        <td class="text-center">{{$parcelDetail->dc_receiver->drop_center_name}}</td>
                                        <td class="text-center">{{$parcelDetail->transfer_dropcenter_status}}</td>
                                        <td class="text-center">
                                            @php
                                                $subtrackingrecivearray = explode(",", $parcelDetail->to_dc_received_amount);
                                                sort($subtrackingrecivearray);
                                                $boxsta = 0;
                                                for ($ri=0; $ri < count($subtrackingrecivearray); $ri++) { 
                                                    if($subtrackingrecivearray[$ri] == $subtrackingarray[$i]){
                                                        $boxsta = 1;
                                                    }
                                                }

                                                if($boxsta == 1){
                                                    echo '<span style="color: green">ตรวจเช็คแล้ว</span>';
                                                }else{
                                                    echo '<span style="color: rgb(248, 141, 0)">ยังไม่ตรวจเช็ค</span>';
                                                    $tranferready = 0;
                                                }
                                            @endphp
                                        </td>
                                        <td class="text-center" rowspan="{{count($subtrackingarray)}}">
                                            @if ($parcelDetail->transfer_dropcenter_status == 'ReceiveDone' || $parcelDetail->transfer_dropcenter_status == 'ReceiveDoneReturn')
                                                <button class="mb-2 mr-2 border-0 btn-transition btn btn-outline-success">ทำรับแล้ว</button>
                                            @elseif ($parcelDetail->transfer_dropcenter_status == 'ParcelWrong')
                                                <button class="mb-2 mr-2 border-0 btn-transition btn btn-outline-warning">พัสดุติดปัญหา</button>
                                            @else
                                                <button class="mb-2 mr-2 border-0 btn-transition btn btn-outline-danger" onClick="addStatusWrong('{{$parcelDetail->transfer_dropcenter_tracking_id}}','{{$parcelDetail->id}}');">แจ้งติดปัญหา</button>
                                            @endif
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="text-center">
                                            {{$rowNo++}}
                                        </td>
                                        <td class="text-center ">{{$parcelDetail->transfer_dropcenter_tracking_no}}</td>
                                        <td class="text-center ">
                                                {{$subtrackingarray[$i].'/'.$parcelDetail->parcel_amount}}
                                        </td>
                                        <td class="text-center">{{$parcelDetail->dc_sender->drop_center_name}}</td>
                                        <td class="text-center">{{$parcelDetail->dc_sender->drop_center_name}}</td>
                                        <td class="text-center">{{$parcelDetail->transfer_dropcenter_status}}</td>
                                        <td class="text-center">
                                            @php
                                                $subtrackingrecivearray = explode(",", $parcelDetail->to_dc_received_amount);
                                                sort($subtrackingrecivearray);
                                                $boxsta = 0;
                                                for ($ri=0; $ri < count($subtrackingrecivearray); $ri++) { 
                                                    if($subtrackingrecivearray[$ri] == $subtrackingarray[$i]){
                                                        $boxsta = 1;
                                                    }
                                                }

                                                if($boxsta == 1){
                                                    echo '<span style="color: green">ตรวจเช็คแล้ว</span>';
                                                }else{
                                                    echo '<span style="color: rgb(248, 141, 0)">ยังไม่ตรวจเช็ค</span>';
                                                    $tranferready = 0;
                                                }
                                            @endphp
                                        </td>
                                    </tr>
                                @endif
                            @endfor
                        @endforeach
                    @endif
                </tbody>
            </table> <br><br><br><br>
            <div class="d-block text-left card-footer">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        @if ($transferBillStatus->transfer_bill_status == 'receive-done')
                        <a href="#"><button class="btn-wide  btn btn-light" type="button">ทำรับโดย {{$transferBillStatus->Employee->emp_firstname}}</button></a>
                        @else
                            @if (count($parcelDetailList) == count($parcelReceiveDonelList)+count($parcelWrongList))
                                <a href="/saveStatusDoneToTransferBill/{{!empty($parcelBillId) ? $parcelBillId : null}}"><button class="btn-wide  btn btn-primary" type="submit" >บันทึกข้อมูล</button></a>
                            @else
                                <a href="/saveStatusDoneToTransferBill/{{!empty($parcelBillId) ? $parcelBillId : null}}"><button class="btn-wide  btn btn-primary" type="submit" disabled>บันทึกข้อมูล</button></a>
                            @endif
                        @endif
                        <a href="/getParcelListFromOtherDC/{{$emp_branch_id}}"><button class="btn-wide  btn btn-secondary">กลับ</button></a>
                    </div>
                </div>
                <div style="position:fixed; bottom:0px; right:10px; width:300px;" class="card">
                    <div class="card-header bg-primary" id="duplicates_tap">
                        รายการแสกนซ้ำ
                    </div>
                    <div class="card-body" style="padding:0px; height:200px; overflow:auto;" id="duplicates_body">
                        @if (count($ReciveTranferDropCenterDuplicates) > 0)
                            <ul class="list-group">
                                @foreach ($ReciveTranferDropCenterDuplicates as $ReciveTranferDropCenterDuplicate)
                                    <li class="list-group-item">
                                        <b style="color:red;">{{$ReciveTranferDropCenterDuplicate->duplicate_tracking_no}}</b>
                                        <br>
                                        <small>
                                            {{date_format($ReciveTranferDropCenterDuplicate->created_at,"d/m/Y H:i:s")}}
                                        </small>
                                        <span class="pull-right">
                                            @if ($ReciveTranferDropCenterDuplicate->duplicate_status == '1')
                                                <small>ไม่พบรายการจัดส่ง</small>
                                            @elseif ($ReciveTranferDropCenterDuplicate->duplicate_status == '2')
                                                <small>มีรายการพัสดุซ้ำโปรดตรวจสอบ</small>
                                            @elseif ($ReciveTranferDropCenterDuplicate->duplicate_status == '3')
                                                <small>ทำรับซ้ำ</small>
                                            @elseif ($ReciveTranferDropCenterDuplicate->duplicate_status == '4')
                                                <small>ไม่อยู่ในสถานะพร้อมทำรับ</small>
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
            </div>
        </div>
    </div>
    <input type="hidden" name="_token" id="token" value="{{ csrf_token()}}">
</div>
<script>
    function addStatusWrong(trackingId, parcelDetailId){
        var token = $('#token').val();
        Swal.fire({
            type: 'warning',
            title: 'ระบุเหตุผลที่ติดปัญหา',
            showCancelButton: false,
            showConfirmButton: false,
            reverseButtons: false,
            html:   '<form action="/updateStatusWrongWithParcelFromOtherDC" method="POST">'+
                    '{{csrf_field()}}'+
                    '<input type="hidden" name="_token" value="'+token+'">'+
                    '<input type="hidden" name="tracking_id" value="'+trackingId+'">'+
                    '<input type="hidden" name="parcelDetail_id" value="'+parcelDetailId+'">'+
                    '<div class="row"><br><br>'+
                        '<div class="col-lg-12 col-md-12 text-left">'+
                            '<div class="position-relative form-group">'+
                                '<label for="province" class="">ระบุเหตุผลที่ติดปัญหา</label>'+
                                '<textarea name="wrong_problem_detail" id="exampleText" class="form-control" row="5"></textarea>'+
                                '<div class="row" id="request_currier_id" style="display:block;">'+
                                    '<div class="col-lg-12 col-md-12">'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '<div class="row">'+
                        '<div class="col-lg-12 col-md-12">'+
                            '<button type="submit" class="mt-1 btn btn-danger" disabled>บันทึกสถานะติดปัญหา</button>'+
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
        $Duplicates_Qty = count($ReciveTranferDropCenterDuplicates);
        if($Duplicates_Qty == Session::get('Duplicates_Qty') && Session::get('Duplicates_Name') == "recive_To_dc"){
    ?>
        $("#duplicates_body").hide();
    <?php
        }
        Session::put('Duplicates_Qty', $Duplicates_Qty);
        Session::put('Duplicates_Name', "recive_To_dc");
    ?>
</script>

@endsection