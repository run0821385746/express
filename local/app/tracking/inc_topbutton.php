<!----- TOP BUTTON ----->
<div class="scrolltop">
    <div class="scroll icon"><i class="fas fa-chevron-up"></i></div>
</div>


<script type="text/javascript">
    $(window).scroll(function() {
        if ($(this).scrollTop() > 500 ) {
            $('.scrolltop:hidden').stop(true, true).fadeIn();
        } else {
            $('.scrolltop').stop(true, true).fadeOut();
        }
    });
    $(function(){$(".scroll").click(function(){$("html,body").animate({scrollTop:$(".thetop").offset().top},"1000");return false})})
</script>