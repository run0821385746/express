<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head><?php require('inc_header.php'); ?>
</head>
<body>
    <div class="thetop"></div>
    <?php require('inc_topmenu.php'); ?>
    
    <!--------------- T R A C K I N G --------------->
    <div class="BlueBG">
        <div class="container-fluid">
            <div class="wrap-pad">
                <div class="row">
                    <div class="col">
                        <div class="header-subtopic mb-4">
                            <h1>ตรวจสอบสถานะ</h1>
                            <h4>ติดตามพัสดุของคุณได้ทุกที่ ทุกเวลา</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="icon-circle"><img src="images/icon-tracking.png"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-8 col-12 offset-lg-3 offset-md-2">
                        <div class="trackBox">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="หมายเลขพัสดุ" aria-label="Recipient's username" aria-describedby="button-addon2">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="button-addon2">ตรวจสอบ<i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                            
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="content-padding more-padding">
        <div class="container-fluid">
            <div class="wrap-pad">
                <div class="row">
                    <div class="col">
                        <h3>หมายเลขพัสดุ : <span>SEV0000000011</span></h3>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col">
                        <ul class="tracking-status">
                            <li>
                                <ul class="date">
                                    <li>วันที่ 12 พ.ค. 2563</li>
                                    <li>เวลา 12.00</li>
                                </ul>
                                <div class="status-img"><img src="images/icon-check.png"></div>
                                <ul class="status">
                                    <li>นำส่งเรียบร้อย</li>
                                    <li>ผู้รับ อัฐฌา สมพรกิจกุล</li>
                                </ul>
                            </li>
                            <li>
                                <ul class="date">
                                    <li>วันที่ 11 พ.ค. 2563</li>
                                    <li>เวลา 14.00</li>
                                </ul>
                                <div class="status-img"><img src="images/icon-exclamation.png"></div>
                                <ul class="status">
                                    <li>พัสดุติดปัญหา</li>
                                    <li>ติดต่อผู้รับไม่ได้</li>
                                </ul>
                            </li>
                            <li>
                                <ul class="date">
                                    <li>วันที่ 11 พ.ค. 2563</li>
                                    <li>เวลา 09.30</li>
                                </ul>
                                <div class="status-img"><img src="images/icon-time.png"></div>
                                <ul class="status oneLine">
                                    <li>นำส่งพัสดุ</li>
                                </ul>
                            </li>
                            <li>
                                <ul class="date">
                                    <li>วันที่ 11 พ.ค. 2563</li>
                                    <li>เวลา 08.30</li>
                                </ul>
                                <div class="status-img"><img src="images/icon-time.png"></div>
                                <ul class="status oneLine">
                                    <li>จ่ายพัสดุ</li>
                                </ul>
                            </li>
                            <li>
                                <ul class="date">
                                    <li>วันที่ 10 พ.ค. 2563</li>
                                    <li>เวลา 17.00</li>
                                </ul>
                                <div class="status-img"><img src="images/icon-exclamation.png"></div>
                                <ul class="status">
                                    <li>พัสดุติดปัญหา</li>
                                    <li>สินค้าค้างส่ง</li>
                                </ul>
                            </li>
                            <li>
                                <ul class="date">
                                    <li>วันที่ 8 พ.ค. 2563</li>
                                    <li>เวลา 08.30</li>
                                </ul>
                                <div class="status-img"><img src="images/icon-time.png"></div>
                                <ul class="status">
                                    <li>สาขาปลายทางรับพัสดุแล้ว</li>
                                    <li>บ้านโป่ง - ราชบุรี</li>
                                </ul>
                            </li>
                            <li>
                                <ul class="date">
                                    <li>วันที่ 7 พ.ค. 2563</li>
                                    <li>เวลา 15.30</li>
                                </ul>
                                <div class="status-img"><img src="images/icon-time.png"></div>
                                <ul class="status oneLine">
                                    <li>อยู่ระหว่างขนส่ง</li>
                                </ul>
                            </li>
                            <li>
                                <ul class="date">
                                    <li>วันที่ 7 พ.ค. 2563</li>
                                    <li>เวลา 10.00</li>
                                </ul>
                                <div class="status-img"><img src="images/icon-product.png"></div>
                                <ul class="status">
                                    <li>รับพัสดุเข้าระบบ</li>
                                    <li>สามพราน - นครปฐม</li>
                                </ul>
                            </li>
                        </ul>
                              
                    </div>
                </div>
                        
                
            </div>
        </div>
    </div>
    
    <?php include('inc_topbutton.php'); ?>
    <?php require('inc_footer.php'); ?>
</body>
</html>