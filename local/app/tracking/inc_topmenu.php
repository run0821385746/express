<div class="header mobile-none">
    <div class="container-fluid">
        <div class="wrap-pad">
            <div class="row">
                <div class="col-lg-3">
                    <a class="mainlogo" href="/local/app/tracking/index.php">
                        <img src="/local/app/tracking/images/mainlogo.jpg">
                        <p>SERVICE EXPRESS</p>
                    </a>
                </div>
                <div class="col-lg-9 pl-0">
                    <div class="phoneBox">
                        <div><i class="fas fa-phone-alt"></i></div>
                        <span>02-007-4755</span>
                    </div>
                    <ul class="mainmenu">
                        <li><a href="/local/app/tracking/index.php#service">บริการของเรา</a></li>
                        <li><a href="/local/app/tracking/index.php#fee">ค่าบริการรับ-ส่งสินค้า</a></li>
                        <li><a href="/local/app/tracking/index.php#area">เขตพื้นที่ให้บริการ</a></li>
                        <li><a href="/local/app/tracking/index.php#footer">ติดต่อเรา</a></li>
                        <li><a href="/local/app/tracking/tracking.php">ตรวจสอบสถานะ</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<header class="mobile">
    <div class="container-fluid">
        <div class="row">
            <div class="col-7">
                <a class="mainlogo" href="/local/app/tracking/index.php">
                    <img src="/local/app/tracking/images/mainlogo.jpg">
                    <p>SERVICE EXPRESS</p>
                </a>
            </div>
            <div class="col-5 pl-0">
                <ul class="mainmenu">
                    <li><a href="tracking.php"><i class="fas fa-search"></i>ตรวจสอบสถานะ</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>

<script type="text/javascript">
    // HEADER //
    $(function(){
        var shrinkHeader = 200;
        $(window).scroll(function() {
            var scroll = getCurrentScroll();
            if ( scroll >= shrinkHeader ) {
                $('.header').addClass('shrink');
            }
            else {
                $('.header').removeClass('shrink');
            }
        });
        function getCurrentScroll() {
            return window.pageYOffset || document.documentElement.scrollTop;
        }
    });
</script>