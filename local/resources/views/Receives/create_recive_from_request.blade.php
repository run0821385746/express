@extends("welcome")
@section("content")
<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header"> Transfer To Drop Center </div>
        <div class="row">
            <div class="col-lg-1 col-md-1 text-right"><br> Tracking No</div>
            <div class="col-lg-3 col-md-3"> <br>
                <div class="position-relative form-group">
                    <form action="/add_recive_from_courier_request" method="POST">
                        <div class="input-group">
                            {{csrf_field()}}
                            <input type="text" class="form-control" name="tracking_no" autofocus required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">ค้นหา</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>  
            <div class="col-lg-8 col-md-8 text-right"><br>
                <button class="mb-2 mr-2 btn btn-primary">จำนวนTrackingค้างรับทั้งหมด
                    <span
                        class="badge badge-pill badge-light">
                        @if (!empty($track_unsuccessfull))
                            {{count($track_unsuccessfull)}}
                        @else
                            0
                        @endif
                    </span>
                </button>
                <button class="mb-2 mr-2 btn btn-success">จำนวนTrackingที่รับสำเร็จ
                    <span
                        class="badge badge-pill badge-light">
                        @if (!empty($track_success))
                            {{count($track_success)}}
                        @else
                            0
                        @endif
                    </span>
                </button>
                <button class="mb-2 mr-2 btn btn-info">จำนวนTrackingที่กำลังทำรับ
                    <span
                        class="badge badge-pill badge-light">
                        @if (!empty($track_doing))
                            {{count($track_doing)}}
                        @else
                            0
                        @endif
                    </span>
                </button>
                {{-- <button class="mb-2 mr-2 btn btn-success">รับสำเร็จ<span
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
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="8%" class="text-left">Tracking No</th>
                        <th width="8%" class="text-center">กล่องที่</th>
                        <th width="20%" class="text-left">ผู้เข้ารับ</th>
                        <th width="10%" class="text-center">สถานะ</th>
                        <th width="10%" class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $list_no = 1;
                        $recive_btn_disabled = 1;
                    @endphp
                    @if (!empty($ReciveRequests))
                        @foreach ($ReciveRequests as $ReciveRequest)
                            @php
                                $recive_checkarray = explode(",", $ReciveRequest->recive_check);
                                sort($recive_checkarray);
                            @endphp
                            @for ($i = 0; $i < count($recive_checkarray); $i++)
                                @if ($i == 0)
                                    <tr>
                                        <td class="text-center">
                                            {{$list_no++}}
                                        </td>
                                        <td class="text-left">
                                            {{$ReciveRequest->tracking_No}} 
                                        </td>
                                        <td class="text-center">
                                            {{$recive_checkarray[$i].'/'.$ReciveRequest->parcel_amount}}
                                        </td>
                                        <td class="text-left">
                                            {{$ReciveRequest->courier->emp_firstname.' '.$ReciveRequest->courier->emp_lastname.'('.$ReciveRequest->courier->id.')'}}
                                        </td>
                                        <td class="text-center">
                                            @if (count($recive_checkarray) == $ReciveRequest->parcel_amount)
                                                <p style="color: green;">ทำรับแล้ว</p>
                                            @else
                                                ทำรับยังไม่ครบ
                                                @php
                                                    $recive_btn_disabled = 0;
                                                @endphp
                                            @endif
                                        </td>
                                        <td class="text-center" valign='center' rowspan="{{count($recive_checkarray)}}">
                                            <form action="/delete_recive_from_courier_request" method="POST">
                                                {{csrf_field()}}
                                                <input type="hidden" name="id" value="{{!empty($ReciveRequest->id) ? $ReciveRequest->id : null}}">
                                                <button class="mb-2 mr-2 border-0 btn-transition btn btn-outline-danger">ลบ</button>
                                            </form>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="text-center">
                                            {{$list_no++}}
                                        </td>
                                        <td class="text-left">
                                            {{$ReciveRequest->tracking_No}} 
                                        </td>
                                        <td class="text-center">
                                            {{$recive_checkarray[$i].'/'.$ReciveRequest->parcel_amount}}
                                        </td>
                                        <td class="text-left">
                                            {{$ReciveRequest->courier->emp_firstname.' '.$ReciveRequest->courier->emp_lastname.'('.$ReciveRequest->courier->id.')'}}
                                        </td>
                                        <td class="text-center">
                                            @if (count($recive_checkarray) == $ReciveRequest->parcel_amount)
                                                <p style="color: green;">ทำรับแล้ว</p>
                                            @else
                                                ทำรับยังไม่ครบ
                                                @php
                                                    $recive_btn_disabled = 0;
                                                @endphp
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endfor
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" align="center">
                                ไม่พบรายการ
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table> <br><br>
                <div class="col-lg-12 col-md-12">
                    <div class="card-body">
                        <a href="{{url('')}}">
                            <button class="mt-1 btn btn-light pull-right">กลับ</button>
                        </a>
                        @if ($recive_btn_disabled == 0 || count($ReciveRequests) == 0)
                            <button type="button" class="mt-1 btn btn-primary pull-right" style='margin-right:5px;' disabled>บันทึกรายการ</button>
                        @else
                            <a href="/save_recive_from_courier_request/{{$employee->emp_branch_id}}">
                                <button type="button" class="mt-1 btn btn-primary pull-right" style='margin-right:5px;'>บันทึกรายการ</button>
                            </a>
                        @endif 
                    </div>
                </div>
                <br><br>
        </div>
    </div>
</div>

@endsection