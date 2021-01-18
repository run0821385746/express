<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php require('inc_header.php'); ?>
    <?php require('inc/conndb.php'); ?>
</head>
<body>

<div class="row">
  <div class="col-md-12" style="">
    <div id="spinner_frame" 
    style="display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        -webkit-transform: translate(-50%, -50%);
        -moz-transform: translate(-50%, -50%);
        -o-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        z-index: 100;
    "><p align="center">
        <img src="./images/preloader_big.gif">
    </p></div>
  </div>
</div>

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
                                <input type="text" id="tracking_no" class="form-control" placeholder="หมายเลขพัสดุ" autofocus>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary btnCheck " type="button" id="button-addon2">ตรวจสอบ<i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                            
                    </div>
                </div>
                <div id="div_break"></div>
            </div>
        </div>
    </div>
    

    <!-- <div class="content-padding more-padding  show_div " style="display: none;;"> -->
    <div class="more-padding  show_div " style="display: none;;">
        <div class="container-fluid">
            <div class="wrap-pad">

              <!-- <div class="content-padding show_result " > -->
              <div class="content-padding " >
                <div class="row">
                    <div class="col">
                        <h3>หมายเลขพัสดุ : <span id="tracking_no_span"> </span></h3>
                    </div>
                </div> 
                <div class="row">
                    <div class="col">
                        <ul class="tracking-status show_result ">
                        </ul>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include('inc_topbutton.php'); ?>
    <?php require('inc_footer.php'); ?>
</body>
</html>
    <script type="text/javascript">
        var input = document.getElementById("tracking_no");
        input.addEventListener("keyup", function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                document.getElementById("button-addon2").click();
            }
        });
        $(document).ready(function () {

            $(".btnCheck").click(function(event) {

                
                var tracking_no = $("#tracking_no").val();

                $("#tracking_no_span").html(tracking_no);
            
                if(tracking_no==""){
                    $("#tracking_no").focus();
                    return false;
                }else{

                    $("#spinner_frame").show();
                        // alert(tracking_no);
                        $.ajax({
                            type: "POST",
                            url: "inc/get_data.php",
                            // dataType: 'json',
                            dataType: 'html',
                            data: { tracking_no:tracking_no },
                            success: function(data){
                                // alert(data);
                                console.log(data);
                                $(".show_result").html("");
                                $(".show_div").show();
                                


                     //         if(data==""){

                     //             setTimeout(function(){
                     //                 $(".show_result").show();
                                    //  $("#rs_tracking_no").html("<font color=red> == ไม่พบข้อมูล หมายเลขพัสดุ ที่ระบุ ==</font>");
                     //                 $("#spinner_frame").hide();
                     //                 $(".show_track").hide();
                                    // }, 500);  
                                    
                     //         }else{

                     //             setTimeout(function(){
                     //                 $(".show_result").show();
                                    //  $.each(data, function( index, value ) {
                              //               $("#rs_tracking_no").html(value.tracking_no);
                              //               $(".rs_status_img").attr("src", value.rs_status_img);
                              //               $(".rs_cause_01").html(value.rs_cause_01);
                              //               $(".rs_cause_02").html(value.receiver);
                              //               $(".rs_date").html(value.rs_date);
                              //               $(".rs_time").html(value.rs_time);
                              //               $("#spinner_frame").hide();
                              //               $(".show_track").show();
                              //           });
                                    // }, 1500); 

                     //         }

                                $("#div_break").fadeIn(500, function(){
                                    $("html,body").animate({ scrollTop: $("#div_break").offset().top-50 }, 1000);
                                });

                                setTimeout(function(){
                                    $("#spinner_frame").hide();
                                    $(".show_result").html(data);
                                }, 1500); 

                            }
                        });
                }

            });
        });
    </script>