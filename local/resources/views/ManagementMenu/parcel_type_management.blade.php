@extends("welcome")
@section("content")

<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header">ประเภทพัสดุและเงื่อนไข
            <div class="btn-actions-pane-right">

            </div>
        </div>
        <div class="table-responsive">
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>
                        <th class="text-center" width="10%">รหัสประเภท</th>
                        <th class="text-left" width="20%">ชื่อประเภทพัสดุ</th>
                        <th class="text-left" width="50%">รายละเอียดเงื่อนไขที่เกี่ยวข้อง</th>
                        <th class="text-right" width="10%">สถานะ</th>
                        <th class="text-center" width="10%">ทำรายการ</th>
                    </tr>
                </thead>  
                <tbody>
                    @foreach ($parcelTypes as $parcelType)
                        <tr>
                            <td class="text-center text-muted">{{$parcelType->id}}</td>
                            <td class="text-left">{{$parcelType->parcel_type_name}}</td>
                            <td class="text-left">{{$parcelType->parcel_type_description}}</td>
                            <td class="text-right"> 
                                @if ($parcelType->parcel_type_status == '1')
                                    ปกติ
                                @else
                                    ยกเลิก
                                @endif
                            </td>
                            <td class="text-center">
                            <a href="/parceltype/{{$parcelType->id}}">
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
               <!--  <div class="col-lg-6 col-md-6 text-left">
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
                    <a href="/parcel_type_create">
                        <button class="btn-wide  btn btn-primary">เพิ่มประเภทพัสดุ</button>
                    </a>
                    <button class="btn-wide  btn btn-success">Export Document</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection