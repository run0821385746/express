@extends("welcome")
@section("content")

<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header">กำหนดค่าบริการจัดส่ง
            <div class="btn-actions-pane-right">

            </div>
        </div>
        <div class="table-responsive">
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>
                        <th width="20%" class="text-center">น้ำหนัก(kg)</th>
                        <th width="20%" class="text-center">Size(cm)</th>
                        <th width="20%" class="text-center">ราคา</th>
                        <th width="20%" class="text-center">สถานะ</th>
                        <th width="20%" class="text-center">ทำรายการ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" colspan="2">ค่าบริการเก็บเงินปลายทาง(COD)</td>
                        <td class="text-center"><span id='codshow'>{{$CODPrices->parcel_price}}</span>%</td>
                        <td class="text-center">
                            @if ($CODPrices->parcel_price_status == '1')
                                ปกติ
                            @else
                                ยกเลิก
                            @endif
                            </td>
                        <td class="text-center">
                            <a href="#" data-toggle="modal" data-target="#exampleModalCenter">
                                <button type="button" id="PopoverCustomT-1" class="btn btn-warning btn-sm">ตั้งค่าใหม่</button>
                            </a>
                        </td>
                    </tr>
                    @foreach ($parcelPrices as $parcelPrice)
                        <tr>
                            <td class="text-center">{{$parcelPrice->parcel_total_weight / 1000}}</td>
                            <td class="text-center">{{$parcelPrice->parcel_total_dimension}}</td>
                            <td class="text-center">{{$parcelPrice->parcel_price}}</td>
                            <td class="text-center">
                                @if ($parcelPrice->parcel_price_status == '1')
                                    ปกติ
                                @else
                                    ยกเลิก
                                @endif
                                </td>
                            <td class="text-center">
                                <a href="/parcelprice/{{$parcelPrice->id}}">
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
        <!--         <div class="col-lg-6 col-md-6 text-left">
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
                    <a href="/parcel_price_create">
                        <button class="btn-wide  btn btn-primary">เพิ่มอัตราค่าส่งพัสดุ</button>
                    </a>
                    <button class="btn-wide  btn btn-success">Export Documents</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header text-white bg-success">
          <h5 class="modal-title" id="exampleModalLongTitle">ค่าบริการเก็บเงินปลายทาง(COD)</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" style="padding-left: 30px; padding-right: 30px;">
            <div class="input-group mb-3">
                <input type="text" id="CODPrice" name="CODPrice" class="form-control" value="{{$CODPrices->parcel_price}}">
                <div class="input-group-append">
                  <span class="input-group-text" id="basic-addon2">%</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="CODPriceSubmit" data-dismiss="modal" class="btn btn-success">บันทึก</button>
        </div>
      </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $("#CODPriceSubmit").click(function(){
            CODPrice = $("#CODPrice").val();
            $.post("{{url('priceCOD')}}",
                {
                    CODPrice,
                    _token: "{{ csrf_token() }}"
                },
                function(data){
                    result = JSON.parse(data);
                    alert(result.msg);
                    $("#codshow").html(CODPrice);
                }
            )
        });
    });
</script>