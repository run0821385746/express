{{-- @extends("welcome")
@section("content")

<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header">ข้อมูลพัสดุรับใหม่ 
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
                    </tr>
                </thead>
                @php
                    $i = 1;
                @endphp
                <tbody>
                    @if (!empty($bookingList))
                        @foreach ($bookingList as $booking)
                            <tr>  
                                <td class="text-center text-muted">{{$i++}}</td>
                                <td class="text-center text-muted">
                                    @if ($booking->booking_status=="done")
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
                                        <a href="/connectBooking/{{$booking->id}}">
                                            <font color="blue">รับงานใหม่</font>
                                        </a>
                                    @else
                                       <font color="green">สำเร็จ</font>
                                    @endif
                                </td>
                                <td class="text-right">{{number_format($booking->booking_amount, 2)}}</td>
                                <td class="text-center">{{$booking->created_at}}</td>
                            </tr>  
                        @endforeach  
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection --}}

<!DOCTYPE html>
<html>
<head>
<title>ข้อมูลพัสดุรับใหม่</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<style type="text/css">
   .box{
    width:600px;
    margin:0 auto;
    border:1px solid #ccc;
   }
</style>
</head>
<body>
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-header">ข้อมูลพัสดุรับใหม่ 
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
                        </tr>
                    </thead>
                    @php
                        $i = 1;
                    @endphp
                    <tbody>
                        @if (!empty($bookingList))
                            @foreach ($bookingList as $booking)
                                <tr>  
                                    <td class="text-center text-muted">{{$i++}}</td>
                                    <td class="text-center text-muted">
                                        @if ($booking->booking_status=="done")
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
                                            <a href="/connectBooking/{{$booking->id}}">
                                                <font color="blue">รับงานใหม่</font>
                                            </a>
                                        @else
                                           <font color="green">สำเร็จ</font>
                                        @endif
                                    </td>
                                    <td class="text-right">{{number_format($booking->booking_amount, 2)}}</td>
                                    <td class="text-center">{{$booking->created_at}}</td>
                                </tr>  
                            @endforeach  
                        @endif
                    </tbody>
                </table>
                <a href="/exportBookingListToExcel">
                    <button class="btn-wide  btn btn-success"> export </button>
                </a>
            </div>
        </div>
    </div>
</body>
</html>