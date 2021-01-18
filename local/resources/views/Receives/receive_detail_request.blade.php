@extends("welcome")
@section('content')

<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header">รายละเอียดพัสดุ
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-6">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">Booking Detail</h5>
                        <div class="table-responsive">  
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>  
                                        <th width="30%" class="text-left"></th>
                                        <th width="70%" class="text-left"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($booking))
                                            <tr>  
                                                <td class="text-left">Booking ID :</td>
                                                <td class="text-left">{{$booking->id}}</td>
                                            </tr>  
                                            <tr>  
                                                <td class="text-left">Booking No :</td>
                                                <td class="text-left">{{$booking->booking_no}}</td>
                                            </tr> 
                                            <tr>  
                                                <td class="text-left">Booking Type :</td>
                                                <td class="text-left">
                                                    @if ($booking->booking_type == "1")
                                                        พัสดุรับหน้าร้าน
                                                    @else
                                                        เรียกรถเข้ารับพัสดุ
                                                    @endif
                                                </td>
                                            </tr> 
                                            <tr>  
                                                <td class="text-left">Booking Status :</td>
                                                <td class="text-left">
                                                    {{$booking->booking_status}}
                                                </td>
                                            </tr> 
                                            <tr>  
                                                <td class="text-left">Booking Create Date :</td>
                                                <td class="text-left">{{$booking->created_at}}</td>
                                            </tr> 
                                            <tr>  
                                                <td class="text-left">Booking Update Date :</td>
                                                <td class="text-left">{{$booking->updated_at}}</td>
                                            </tr> 
                                    @endif
                                </tbody>
                            </table><br><br>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-6">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">Sender Detail</h5>
                        <div class="table-responsive">  
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>  
                                        <th width="30%" class="text-left"></th>
                                        <th width="70%" class="text-left"></th>
                                    </tr>
                                
                                </thead>
                                <tbody>
                                    @if (!empty($sender))
                                            <tr>  
                                                <td class="text-left">Sender ID :</td>
                                                <td class="text-left">{{$sender->id}}</td>
                                            </tr>  
                                            <tr>  
                                                <td class="text-left">Customer Name :</td>
                                                <td class="text-left">{{$sender->cust_name}}</td>
                                            </tr> 
                                            <tr>  
                                                <td class="text-left">Customer Address :</td>
                                                <td class="text-left">{{$sender->cust_address}} 
                                                    {{$sender->District->name_th}}
                                                    {{$sender->amphure->name_th}}
                                                    {{$sender->province->name_th}}
                                                </td>
                                            </tr> 
                                            <tr>  
                                                <td class="text-left">Customer Postcode :</td>
                                                <td class="text-left">{{$sender->cust_postcode}}</td>
                                            </tr> 
                                            <tr>  
                                                <td class="text-left">Customer Phone :</td>
                                                <td class="text-left">{{$sender->cust_phone}}</td>
                                            </tr> 
                                            <tr>  
                                                <td class="text-left">COD Status :</td>
                                                <td class="text-left">
                                                    @if ($sender->cust_cod_register_status == "1")
                                                        register success
                                                    @else
                                                        no register
                                                    @endif
                                                </td>
                                            </tr> 
                                    @endif
                                </tbody>
                            </table><br><br>
                        </div>
                    
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">  
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>  
                        <th width="10%" class="text-center">Tracking No</th>
                        <th width="20%" class="text-center">Receive Name</th>
                        <th width="30%" class="text-center">Receive Address</th>
                        <th width="10%" class="text-center">Receive Phone</th>
                        <th width="20%" class="text-right">Tracking Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($trackings))
                        @foreach ($trackings as $tracking)
                            <tr>  
                                <td class="text-center">{{$tracking->tracking_no}}</td>
                                <td class="text-left">{{$tracking->receiver->cust_name}}</td>
                                <td class="text-left">
                                    {{$tracking->receiver->cust_address}}
                                    {{$tracking->receiver->District->name_th}}
                                    {{$tracking->receiver->amphure->name_th}}
                                    {{$tracking->receiver->province->name_th}}
                                    {{$tracking->receiver->cust_postcode}}
                                </td>
                                <td class="text-center">{{$tracking->receiver->cust_phone}}</td>
                                <td class="text-right">{{$tracking->tracking_amount}}฿</td>
                            </tr>  
                        @endforeach  
                    @endif
                </tbody>
            </table><br><br>
        </div>
        @if (count($subTrackings)>0)
            <div class="table-responsive">  
                <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                    <thead>
                        <tr>  
                            <th width="10%" class="text-center">Tracking No</th>
                            <th width="10%" class="text-center">SubTracking No</th>
                            <th width="10%" class="text-center">Parcel Type </th>
                            <th width="20%" class="text-center">Parcel Dimension Detail </th>
                            <th width="10%" class="text-right">Parcel Weight</th>
                            <th width="10%" class="text-right">COD</th>
                            <th width="20%" class="text-right">SubTracking Amount/Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($subTrackings))
                            @foreach ($subTrackings as $subTracking)
                                <tr>  
                                    <td class="text-center">{{$subTracking->tracking->tracking_no}}</td>
                                    <td class="text-center">{{$subTracking->subtracking_no}}</td>
                                    <td class="text-center">
                                        @if ($subTracking->subtracking_dimension_type == '1')
                                            เลือกจากกล่อง
                                        @else
                                            กำหนดเอง
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $dimension = App\Model\DimensionHistory::where('dimension_history_subtracking_id',$subTracking->id)->first();
                                        @endphp
                                        @if (!empty($dimension->dimension_history_width))
                                            {{$dimension->dimension_history_width}}x{{$dimension->dimension_history_length}}x{{$dimension->dimension_history_hight}}
                                        @endif
                                    </td>
                                
                                    <td class="text-right">
                                        @if (!empty($dimension->dimension_history_weigth))
                                            {{number_format($dimension->dimension_history_weigth, 0)}}
                                        @endif
                                    </td>
                                    <td class="text-right">{{number_format($subTracking->subtracking_cod, 2)}}</td>
                                    <td class="text-right">{{$subTracking->subtracking_price}}฿</td>
                                </tr>  
                            @endforeach  
                        @endif
                    </tbody>
                </table><br><br>
            </div>
        @endif

        @if (count($saleOtherList)>0)
            <div class="table-responsive">  
                <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                    <thead>
                        <tr>  
                            <th width="10%" class="text-center">No</th>
                            <th width="80%" class="text-left">Description</th>
                            <th width="10%" class="text-right">Price</th>
                        </tr>
                    </thead>
                    <tbody>

                        @if (!empty($saleOtherList))
                            @foreach ($saleOtherList as $saleOther)
                                <tr>  
                                    <td class="text-center">{{$saleOther->id}}</td>
                                    <td class="text-left">
                                        
                                        @php
                                            $productPrice = App\Model\ProductPrice::where('id',$saleOther->sale_other_product_id)->first();
                                        @endphp
                                        {{$productPrice->product_name}} {{$productPrice->product_width}}x {{$productPrice->product_length}} x{{$productPrice->product_hight}}
                                    </td>
                                    <td class="text-right">{{number_format($saleOther->sale_other_price, 2)}}</td>
                                </tr>  
                            @endforeach  
                        @endif
                    </tbody>
                </table><br><br>
            </div>
        @endif

        <div class="d-block card-footer">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <a href ="/getRequestServiceList/{{$employee->emp_branch_id}}" class="mm-active">
                        <button class="btn-wide  btn btn-light"> กลับ </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection