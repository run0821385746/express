@extends("welcome")
@section("content")

<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header">พัสดุติดปัญหา
        </div>
        <div class="table-responsive">
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>
                        <th width="10%" class="text-center">ID</th>
                        <th width="10%" class="text-center">BookingID</th>
                        <th width="10%" class="text-center">TrackingID</th>
                        <th width="10%" class="text-center">SubTrackingID</th>
                        <th width="30%" class="text-center">ปัญหาที่เจอ</th>
                        <th width="15%" class="text-center">สถานะ</th>
                        <th width="15%" class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($parcelWrongList))
                        @foreach ($parcelWrongList as $parcelWrong)
                        <tr>
                            <td class="text-center text-muted">{{$parcelWrong->id}}</td>
                            <td class="text-center">{{$parcelWrong->wrong_booking_id}}</td>
                            <td class="text-center">{{$parcelWrong->wrong_tracking_id}}</td>
                            <td class="text-center">{{$parcelWrong->wrong_subtracking_id}}</td>
                            <td class="text-left">{{$parcelWrong->wrong_problem_detail}}</td>
                            <td class="text-center"><div class="badge badge-warning">{{$parcelWrong->wrong_status}}</div></td>
                            <td class="text-center">
                                <button type="button" id="PopoverCustomT-1"
                                    class="btn btn-primary btn-sm">แสดงรายละเอียด</button>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table><br><br>
        </div>
        <div class="d-block text-left card-footer">
            <div class="row">
                <div class="col-lg-6 col-md-6 text-left">
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
                </div> 
                <div class="col-lg-6 col-md-6 text-right">
                    <button class="btn-wide  btn btn-success">Export Documents</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection