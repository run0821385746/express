@extends("input")
@section("content")

<div class="col-lg-12 col-md-12">
    <div class="main-card  card">
        <div class="card-header">ข้อมูลรหัสไปรษณีย์ในระบบ
        </div>
        <div class="table-responsive">
            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                <thead>
                    <tr>
                        <th width="10%" class="text-center">รหัสไปรษณีย์</th>
                        <th width="20%" class="text-left">จังหวัด</th>
                        <th width="40%" class="text-left">อำเภอ</th>
                        <th width="10%" class="text-left">ตำบล</th>
                        <th width="10%" class="text-center">ทำรายการ</th>
                    </tr>  
                </thead>
                <tbody>
                    @foreach($postcodes as $postcode)
                    <tr>
                    <td class="text-center text-muted">{{$postcode->id}}</td>
                        <td class="text-left">{{$postcode->postcode}}</td>
                        <td class="text-left">{{$postcode->province}}</td>
                        <td class="text-center">
                            <a href="/postcode/{{$postcode->id}}">
                                <button type="button" class="btn btn-primary btn-sm">เลือก
                                </button>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection