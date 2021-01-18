<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head><?php require('inc_header.php'); ?>
</head>
<body>
    <div class="thetop"></div>
    <?php require('inc_topmenu.php'); ?>
    
    <!--------------- B A N N E R --------------->
    <div class="container-fluid">
        <div class="row">
            <div class="col px-0">
                <div class="img-width">
                    <img class="mobile-none" src="images/banner.jpg">
                    <img class="mobile" src="images/banner-mobile.jpg">
                </div>
            </div>
        </div>
    </div>
    
    <!--------------- S E R V I C E --------------->
    <div class="content-padding" id="service">
        <div class="container-fluid">
            <div class="wrap-pad">
                <div class="row">
                    <div class="col">
                        <h1 class="doubleLine">บริการของเรา</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <ul class="service-section">
                            <li>
                                <div class="serviceBox">
                                    <div class="service-img"><img src="images/service01.png"></div>
                                    <p>Courier service</p>
                                </div>
                            </li>
                            <li>
                                <div class="serviceBox">
                                    <div class="service-img"><img src="images/service02.png"></div>
                                    <p>ระบุเวลาได้</p>
                                </div>
                            </li>
                            <li>
                                <div class="serviceBox">
                                    <div class="service-img"><img src="images/service03.png"></div>
                                    <p>บริการเก็บเงินปลายทาง (COD)</p>
                                </div>
                            </li>
                            <li>
                                <div class="serviceBox">
                                    <div class="service-img"><img src="images/service04.png"></div>
                                    <p>ค้นหาพื้นที่บริการด้วย Postcode</p>
                                </div>
                            </li>
                            <li>
                                <div class="serviceBox">
                                    <div class="service-img"><img src="images/service05.png"></div>
                                    <p>รับพัสดุหน้างาน</p>
                                </div>
                            </li>
                        </ul>      
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!--------------- S E R V I C E - F E E --------------->
    <div class="BlueBG" id="fee">
        <div class="container-fluid">
            <div class="wrap-pad">
                <div class="row">
                    <div class="col">
                        <div class="header-subtopic fee">
                            <h2>ค่าบริการรับ-ส่งสินค้า</h2>
                            <h4>ปทุมธานี นนทบุรี นครปฐม ราชบุรี</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-10 col-12 offset-lg-1">
                        <ul class="list-content">
                            <li><i class="fas fa-chevron-circle-right"></i>ราคาจัดส่ง <span>เริ่มต้นเพียง <span id="showstartPrice"></span> บาท</span></li>
                            <li><i class="fas fa-chevron-circle-right"></i>เข้ารับถึงที่ ระบุเวลาได้</li>
                            <li><i class="fas fa-chevron-circle-right"></i>มีบริการเก็บเงินปลายทาง (COD)</li>
                            <li><i class="fas fa-chevron-circle-right"></i>มีบริการรับพัสดุฝากส่งจากลูกค้า พร้อมทั้งออกใบเสร็จ และ Tracking Barcode</li>
                        </ul>
                    </div>
                </div>
                
                <?php
                    require_once('inc/conndb.php');
                    $sql = "SELECT * FROM parcel_prices where parcel_price_status = '1' and parcel_total_dimension != 'COD' order by parcel_total_weight ASC";
                        // die($sql);
                    $stmt = $conn->prepare($sql);
                    // $stmt->bindParam(1, $tracking_no);
                    $stmt->execute();
                    // $num_rows = $stmt->rowCount();
                ?>
                <div class="row">
                    <div class="col-lg-10 col-md-10 col-12 offset-lg-1 offset-md-1">
                        <div class="priceTB-head">
                            <div class="row">
                                <div class="col">น้ำหนัก <span>(kg)</span></div>
                                <div class="col">ขนาด <span>(cm)</span></div>
                                <div class="col">ราคา</div>
                            </div>  
                        </div>
                        <div class="priceTB-body">
                            <?php
                                $i = 0;
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $i++;
                            ?>
                            <div class="priceBox">
                                <div class="row">
                                    <div class="col"><?= $row['parcel_total_weight']/1000; ?></div>
                                    <div class="col"><?= $row['parcel_total_dimension']; ?></div>
                                    <div class="col" id="startPrice<?= $i;?>"><?= $row['parcel_price']; ?></div>
                                </div>
                            </div>
                            <?php
                                }
                            ?>
                            <!-- <div class="priceBox">
                                <div class="row">
                                    <div class="col">2</div>
                                    <div class="col">50</div>
                                    <div class="col">40</div>
                                </div> 
                            </div>
                            <div class="priceBox">
                                <div class="row">
                                    <div class="col">3</div>
                                    <div class="col">60</div>
                                    <div class="col">45</div>
                                </div>
                            </div>
                            <div class="priceBox">
                                <div class="row">
                                    <div class="col">4</div>
                                    <div class="col">70</div>
                                    <div class="col">50</div>
                                </div> 
                            </div>
                            <div class="priceBox">
                                <div class="row">
                                    <div class="col">5</div>
                                    <div class="col">80</div>
                                    <div class="col">55</div>
                                </div>
                            </div>
                            <div class="priceBox">
                                <div class="row">
                                    <div class="col">6</div>
                                    <div class="col">90</div>
                                    <div class="col">60</div>
                                </div> 
                            </div>
                            <div class="priceBox">
                                <div class="row">
                                    <div class="col">7</div>
                                    <div class="col">100</div>
                                    <div class="col">75</div>
                                </div>
                            </div>
                            <div class="priceBox">
                                <div class="row">
                                    <div class="col">8</div>
                                    <div class="col">110</div>
                                    <div class="col">90</div>
                                </div> 
                            </div>
                            <div class="priceBox">
                                <div class="row">
                                    <div class="col">9</div>
                                    <div class="col">120</div>
                                    <div class="col">105</div>
                                </div>
                            </div>
                            <div class="priceBox">
                                <div class="row">
                                    <div class="col">10</div>
                                    <div class="col">130</div>
                                    <div class="col">120</div>
                                </div> 
                            </div> -->
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col">
                        <div class="text-center">**สามารถส่งพัสดุที่มีขนาดด้านยาวสูงสุด 200 ซม. <span>(ราคาตามที่บริษัทกำหนด)**</span></div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <!--------------- S E R V I C E - F E E --------------->
    <div class="bkkBG" id="area">
        <div class="container-fluid">
            <div class="wrap-pad">
                <div class="row">
                    <div class="col">
                        <h1>เขตพื้นที่ให้บริการ</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <ul class="area">
                            <li>
                                <div>ปทุมธานี<i class="fas fa-plus-circle"></i></div>
                            </li>
                            <li>
                                <div>นนทบุรี<i class="fas fa-plus-circle"></i></div>
                            </li>
                            <li>
                                <div>นครปฐม<i class="fas fa-plus-circle"></i></div>
                            </li>
                            <li>
                                <div>ราชบุรี<i class="fas fa-plus-circle"></i></div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include('inc_topbutton.php'); ?>
    <?php require('inc_footer.php'); ?>
    <script>
        startPrice = $("#startPrice1").html();
        $("#showstartPrice").html(startPrice);
    </script>
</body>
</html>