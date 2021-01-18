<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SERVICE-EXPRESS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="description" content="This is an example dashboard created using build-in elements and components.">
    <meta name="msapplication-tap-highlight" content="no">
    <!--
    =========================================================
    * ArchitectUI HTML Theme Dashboard - v1.0.0
    =========================================================
    * Product Page: https://dashboardpack.com
    * Copyright 2019 DashboardPack (https://dashboardpack.com)
    * Licensed under MIT (https://github.com/DashboardPack/architectui-html-theme-free/blob/master/LICENSE)
    =========================================================
    * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
    -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <link href="/architectui-html-free/architectui-html-free/main.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="/js/script.js"></script>
    <script src="/js/webcam.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" /> --}}
    {{-- <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet"> --}}
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>   --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    {{-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script> --}}
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <link rel="icon" href="{{url('local/public/uploadimg/cod_account/serviceExpress_icon.png')}}">
</head>

<body>
    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
        <div class="app-header header-shadow">
            <div class="app-header__logo" style="background: -webkit-linear-gradient(right, rgb(0, 191, 255), rgb(153, 204, 255));">
                <a href="{{url('')}}"><div class="logo-src" style="width: 180px !important; height:55px !important;"></div></a>
                <div class="header__pane ml-auto">
                    <div>
                        <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                            data-class="closed-sidebar">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="app-header__mobile-menu">
                <div>
                    <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="app-header__menu">
                <span>
                    <button type="button"
                        class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                        <span class="btn-icon-wrapper">
                            <i class="fa fa-ellipsis-v fa-w-6"></i>
                        </span>
                    </button>
                </span>
            </div>
            <div class="app-header__content" style="background: -webkit-linear-gradient(left, rgb(0, 191, 255), rgb(153, 204, 255));">
                <div class="app-header-left">
                    
                </div>
                <div class="app-header-right">
                    <div class="header-btn-lg pr-0">
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-right header-user-info ml-3">
                                    @if ($employee->emp_image !== null)
                                        <img src="data:image/jpeg;base64,{{$employee->emp_image}}" width="100%" style="max-height: 50px; max-width:50px; border:2px double #ccc; border-radius:50%; cursor:pointer;" onclick="ShowImgProfile()" />
                                    @else
                                        <img src="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAUFBQUFBQUGBgUICAcICAsKCQkKCxEMDQwNDBEaEBMQEBMQGhcbFhUWGxcpIBwcICkvJyUnLzkzMzlHREddXX0BBQUFBQUFBQYGBQgIBwgICwoJCQoLEQwNDA0MERoQExAQExAaFxsWFRYbFykgHBwgKS8nJScvOTMzOUdER11dff/CABEIAWgBaAMBIQACEQEDEQH/xAAdAAEAAQUBAQEAAAAAAAAAAAAAAQIEBQYHAwgJ/9oACAEBAAAAAPrAyVypwxkrlThljj8ZjLC3pq977J5e8yDJXKnDGSuVOGBkrlThjJXLHaNr1hEwCUL3Yt2yynDGSuVOGBkrlThjJeGh6nTEwkIFVKrad8yWGMlcqcMZEorU1PHTtKopkAiZplCVe6bjcKK1NRdGOtXrlsdy/DETFVMhCYlBJleoZXHWr1ywMdavW35ZazCqmYEwACYe/UclavXLAx1qw3PfKZpkiSaZgqpBMCvoOceuWMIZK5wHKPOJmlVCFVNSIkiohNMor6pslOGBkrfj9qSAQkCEiAe/XsphgXvLMKqpkSQGA1ls+b9FNRADJ9fxhkXnpmjQEwSHJvnDS6Xr0f6T3v1giqmUE7lvxdMfxnzQSCJo+XOE0gy/1n1CtMJiQnseWHM9REKkSQ5b8ZgG7/YOzRJNMkG0dTLDi1AJEJn5X5nVqQPbon1pn0kCUJ7LlcJqGngiQHx/9L9h5DyfVp23rFHx59hbKAA2rdXL7CAJiqET8mfcd8B5fnl9W7MAmAuuq2XLgQTFUDiX1WmCT5D6zsYSICelWnP0oEkJPHogBzPAbYqpkgkRvFtqMoCUAy+zyhJGA5ZugSgBs1pgwAJhe7klAaPqWzwmAAyfjZACYEt39wKOK7cVQQTAe0eQATFVJsGfJGpadn4AAJroCJAD13X1IU8e2H0AAD08gFVKYqUmY2cNe5duZVSTAEwvLQAARmsvhc77eWGvNXx+RAAHrlcJVAhICvp3prmnmzbbTw7KXMxVTImmqDI5zUQAIxPOtb+vsw1zT9n2xYfHt10LP5BMAmE7JsPOQA8tY12301136BNC304bxNt/rf7NnpAG+5Pl8kCpj9CtVGs2vt9fZrQOIda6pj/j2zudk9lWa3O4hFUDpGX5ZZTAlRzizFGp+fW/o3ivOM99LfP3Fa9r9RVsW4pILjq86dqADBaOGN156/bnHtBzH0l8P2bO5YL3eb8DaN4yOP5DSQlp+shTqfk7NaYf02zhivbawudwzoHWstdOX6oBpOAKJqxWBem73HjpHizWZeXqe+27CDY+sDG8ZoCrQsKW1N3GqeDM7VrWBem2V+HleHrteyhV1/NBzvSAaBiSw8soxuvJ3LT6GdyzH05I9Np2qEVbV08whecpxNQw/ieFNyjHHp5mQqW64Jvb8jIdgx4MlZ8g8CYTFVJVSTCqmqkVUhPp1vM4YGSudb5XQmUJCmSVMgCTp2104YyJRW1TndMygRUglAQmVJV0Db1NRdGOtXrYcw8RKJIlBIhKD06XmrV65YGOtXrlsTy7FgAJiYJIZDqGbx1q9csDHWr1yzx55p8ESEoBNMm1dJ92OtXrljCGSuVOGMlZaDrMATABOxb/AJfDGSuVOGBkrlThjJXLEaNr1sCRB77BumfU4YyVypwwMlcqcMZK5U4aMXjsXjLLyh63uTy99lZyVypwxkrlThj/xAAcAQEAAgMBAQEAAAAAAAAAAAAABgcBBAUCAwj/2gAIAQIQAAAAAYjsN4Xw6Mnmu4AA+FZxRjOM7VlykAGvUvDAerLmIAxVEZAHu2ZEAQeuWMgxlu3PtAaFLfEAMZmVmAVjDwAHq5+qGhSfgADGZfZ4V9AgAB9Lv2zzSWgBs9nj6oFkTY4VPAda3trRqHmg7twleQQGbl7BE6uwHq8dtTXGB1LqGtSmrjItSVfCi/AJRafs0qX1gTayODT4Etmuzs63moNUHcuKFVsYySuZ+TYqbTyH3vSuoSBI5yPVV6oF41pGTDOJPNgrDRyC4a34wEulwzWvMAtSutADvz0fKs9QCcQ/XA9Wr9TgwEB1Od4YyFj9Uh8TAPXkxkTCWFdcgA9+AEqmRWXPyDGfv8AFl9XxtQGLAOh50QN67NDX6nEqbwBNuDxwdm4Pt8NHqbMOrHXDFqxCNBLbk9aHx+vy39ngUtohcEThY3f0Dt/HVzIol0/p7g9QeRd8XrgWHazx8PcohO/9nw/P/NPtevCqDJc0zfPx7lMK2vrlScSO1cmvRfkveRPHjfkML+n2KkgJNLKUxyC/e4x8up2ok+pVVdlpSxXMHxn9BdfGfjs9uN+/ZV9bM3juI/UJ+hun59fPHS5n1M1nWTtXIeKR0ku+2M4xnHocbiLFnIruChjIA93dujm0r5AxnGQldpgq2JgAM3L2Ac2l/mABLbRAQGvzGWMhtXPugPFSR8AZtWUADUqPkAM2LOAAalYRsH2siYAAPMWhfE+W9KJx0QH/xAAcAQEAAQUBAQAAAAAAAAAAAAAABgEDBAUHAgj/2gAIAQMQAAAAAV20h2l7C0kdxwAFyab4FiF6QAFye7UBSE6AAJ3uwB5gOpAJJMgAY3OLIGT0m4ABG4aBNJEAVoPPOcEMnpfoABWOQsJXKwAB45lZHS8sCzr8++BDI4bPoYGDz/Hy+gZYNVz4lkqBTneuJBNahTmFl0PZgwubC/0i+CCaT31H0DSwi0ZXRrwIzD9r0EDQ6rExcvxOb4NZzySTIDR670YMyvhWzy+YSVVRXU6kY8tvAcxmm6CrVageZLkFVK88mGyVoNRqx5kOYBBJhlga/SBJL4EQlF8CkWoZcgAa3YehWhHcY2e4rSoo8+gGn1hvs0A8+gGr1Ckiy6FaKrF4BEHrD327AYFzLAxebbbL0udPPQER3myBrueeMjaaSxIptdBBt/ug0HOaZd3BzsOztelZIc838kGNySxcyNdzXseD48SjoVRzPdzERCBvd3Qco79iWl3reWW+XbXoA5xG3v1HOS/Qli3R0zfmt53d6f6OWad69wnl/wBD0tHQJWRuGui7E5Rq1bkE5x3/ANWyeS8gujS6UHJddWl3Sc27J4opOZlXzzGw2vQVeQ4Prz7Qqb2yk2mrW87K9MyUftivj3QbLZIdGhLZUoVpWgK088ysDL6T6AK0DQQgE334ACnOcAGX0j2ABHoUAlEuVUqoK4/OMcBWf7YAUgmlAF7oGwAIbGwAXpvuQWodHwAFd5IdrcxNHG8MB//EAEsQAAECBAIECQkGBAMHBQAAAAECAwAEBREQIQYSIEETIjEzUWFygbEHMERScYKSosEUFjJCkdEjQKHhCGJjFSQ0Q1NzwjZ0k7LS/9oACAEBAAE/AMZPm/ePgMXOac7CtiT5v3j4DFzmnOwrGYqUhLc7MoB3pBuf6Xg6XyjCVJZl3HDcm5skQ9pjUF5NMtNjvVD2klaeJvOkX6EgQuo1Bz8U68ffMF55XK6s+8THCOf9RX6mA88OR1Y94w3UJ9r8E48n2LMNaQ1lq1p1RHQoJMM6YVFHOtNODvSYa0xlXElL0q42SCLpIUIYqtPmebmkX6DxT81sZPm/ePgMXOac7CtiT5v3j4DFzmnOwrbk+b94+Axc5pzsK2JPm/ePgMZ6fk5JpZmH0oJSbA5k+wCJvShIumVY99z6ARM1Oem78LMq1fVHFT/THLHLZyxlqjOynMzCwPV5R+hiU0oOQmmL9K0fUGKTUpOcaKWZhJzJI/N3g4uc052FbEnzfvHwGLnNOdhW3J837x8Bi5zTnYVsSnNK7R8BE9UpOnt68w8EdA5SfYBFR0tm37ok08Cj1jms/tDjjjqytxZWo8qlEk/1x37WcHzCVqQoKQopUOQj6WinaWTsrqomhw7fzgRIVaTqKNaXeBUBxkHJQ9ohzmnOwrYk+b94+Axc5pzsK2DIoHpHywZFA9I+WDIoHpHyxwn2XiW178a/Jy5dcCeQPR/mgTyB6P8ANAmwsFsNWK+LrXva+UGRQPSPlgyKB6R8sOS6GkKWt8BIzJItFR0oDAWxTyFG5u8Rl7oh5519xTjzqlrPKonDvxvAwGydi+y2440tK21lCxyKBIPdaKZpYrVDE+Lg5cMN3tENMtvoS42+FIUMiIMigekfLBkUD0j5Y4T7LxLa9+Nfk5cuuBPIHo/zQJ5A9H+aBNhYLYasV8XWve18oMigekfLBkUD0j5YMigekfLsTnOjsDFnn2u2PHCoVOVprJdmF26EjlVFWrk3VFkKJQyORsfXp83fz9MrM3S3LtK1myeM2eQxTKtKVRrXZVZYHHQeVOE5zo7AxZ59rtjx25znR2Bizz7XbHjFZrcvSm7Cy31DiI+pibnJieeU8+5rKP8AQdA27fystMvyryXmHChaeQiKHpAzU0Bp06kwBmn1usROc6OwMWefa7Y8duc50dgY1asIpqdVFlPnNKejrMPPOzDq3XVla1G5J2r+YG3lsDZbWtpaVtrKVpIII3ERSq4KiENP2EwlIHbAxZ59rtjx25Pm/ePgMK5WW6VLkCyn1g8Gn6mHnnZh1brqytazckxvw37NvMW8znsZxnghxbS0rQopUkggjcRGj9cTU2eCdUEzCBxh63+bBzmnOwrblOaV2j4CKlUGaZKrfdPJklPrK6InJt6emHH3lXUs9wHQIywyxyxtjljlGWGWGWzljlGUZbEtMOyrzbzK9VaDcGKTVGqpKpdTYLGTiegw5zTnYVtsLQ0w4tZskEkk9AEVyrLqk2VC4ZRcNp+vmDsWwzxn9KdG6UVJnq7JMrHKhTydf4RnD3lX0CZNjXgrsMOq8Ew15V9AnjYV4DtMup8UxIaT6PVQXkavLP8AUhYNoQ4hwXQtKh1EHw288M8c8M4pFTdpc2l4XKDYOJ6Uwh1D8qXmjrIU2SFe0bBkUD0j5YMigekfLH2P/U/pGlFSLCP9msruVcZ0jr/Lt7se/a008q9H0ZW7JSKRPVFOSkBVmmj0LUPARXtP9KtIlOCcqriGFejsktNW6LDl74ucW3XGlpW2tSFpNwoGxEaPeU6v0dxCJp4zrA9dVnR7FxozpvIV6VS/LvB1AtrpNkutnoUIbdbeQlbagUnC+2Ni8aL1YoJpzp4rl+CJ3E7oMigekfLBkUD0j5YMigekfLjUp5unyb0wu3FGQ6SeSHnnJh5x5xV1rUST1mMsMvMZYXhxxtpDjjiwhCElSlKIAAAuSSdwjTrywT88+/IaPOmWkhdBmhzr3Z9VMElRJJuTt0esT9DnWp2SeKHUHMcqVJ3pV0gxofpaxWZFueljY5JmGCb6iuj9jDTqHm0OIN0keZy2ELU2tK0GykkEHoIzij1BFSkWnwRrjiuJ6FDZ0tqPDzSJNB4jOautRjfGeGfmt8eWCpPU/QqbSy5qKm3m5c9hXGPmtBK3NUfSCTSylS2ppaWHmhyqCjYH2iKO+tLxYtdCgT7CP3jPC0b9jOM4z2NFqkZOe4BarNTFknqVu2KjOIkZKYmFfkSSB0nkAhxxTri3Fm61qJJ6yb+f8u9Qcdr1KkAs8ExJ8Jb/ADuqIPhGiOiFY02q6KXSmklzVK3HXCUttIH5lkRpNoTpNohMFmsUt1lF7IfA12V9lYy21NOIQ2tTagld9RRGRtkbR5LFtJ0pQlaEqUuWdDZIzSoWNx3CKGU3mBbOyc/PBRSQQSCDyxR57/aEgw/fj2svtDI46UTVgxKJPLx1+A8/vjy2f+tFf+zYjyGaLNUDQqVnltATlWtMuq38FyNJiYl5ebZcYmGG3mXBZbbiQtKgdxCrxpB5CtAq2pbrEm9TH1fmk12R8CriKl/hoqiCTS9JpZ4dEy0pr+qNeHf8POn7arIXTXOtL5+qYZ/w76fOmy3aY12n1HwQYpn+GeeUUmq6UMNjeiWZLnzLKYoHkO0BoSkOuU9dSfT+edWFp+AWTHlm0Pla3oJOKlpVCZikIM1LBCbWbRziB1FEeTtZb0wo/WXU/q2qKIT9odH+n9fP6MTWq89Kk5LGun2jLGpzP2uemXb8XWIT7E5YZbdti2Fo8tsss6ZyYQLl6QZsPfWmKZJN06myEk2AES0s0yn2NpCfp5h5pt9l1l1AU24goWk70qFiO+KDSl0Tylppa73k5+YY9obCgDFF/wCKc/7Z8RhlGW1bDLYk3zKzbD4P4FA93IRAIUAQbgiKlMGWkZp29iEEJ9pygbXdsd2NsdOKIat5T/Jw1qXS84kL60SznCr81pfRTJeXND6U2RNSonB/8JaPzJiiJPDPHoQB+p2e7HPHuix2KK/9opssonjJGofamNLT9nlGGQu5ccJ7k+avF9m8SlDbntJ6LVl2vTWJwI6deYCUeF4vF4MXwvhfDSvRtM5pNTazqA8HS5mVv0LLiFo8VRS3izNpSTkviHbvsXi8Xi+GiCuGROMFdtUpWO/KNMXiuoMtbm2r96jG7Zthlju2MsKMoCZUOlswcbxls1tQvLo6lGD/AMadTc9l8WNvOZRok8G6uhBJs6hSf0zjSNzhazOHckhI7k+etjIOcHNsKv8Amt3Kyi0W2rYaSVBEu8tOtdYQEpTFLk1uupmFjiJzF95/tG7G3nKM7wNUkV/6oHxZRUllyoTyul9zxt/IgkRLPcOw056wEXi8Xi8Xi8Xh11LLTjqzZKElSj1DMmNZdSqSlrzLrpUr2XuR3QMgABlbYvhfzTC+DfZX6q0n9DeHzd909Kz5zftUaYuHGCeTjJ2jhpbPiWp4l0qs5MG3ujMxRWOcfPZTF/5FznHO0YtsWwtFsbY2gRbBh5TDqHE8qTDTiXW0OJzChtKUlCVKJAABJPsiqTq6zVFKR+EkIaB3JG8+JhlpLLTbaeRItFsbYWwtBi0WjKMotFotg8NV50dCz5zft0ydDCuCcP8ADUcj0H9jsiKtOhSVy7Rysdc/QRI8Wcl+2P5C+NQRwU9ON+q8vxi+F8LxfC8XwvF8L4XwpFMM65wjgswjl/zHoEVajpcBflUWWBxmxvHSIk6m5LWbcBW3/UQ3PSjoyfSOpXF8Y+0Mf9dHxCFzkq2Ll9Pcb+ETlWU6ChgFKd6t8UikFzVmZlPEyKEHf1mNLNHTIumpSSbMFQLiU/8ALUd46jFPnhNt2Vk6kcYdI6RF8LxeL43i+F4vF4vF4vDKeEeaR6ywP1No0ha4KsTo3FQUO8A7W/aMWi2Fok/ssxPy8k5NtNuOk6qCoBRAFyAIZZbYbQ02kBCRYDCr0cPa0zLJ/ifnQPzRmMaPR9fVmZlHF5UNnf1nBbaHULQ4kKQoEKScwQRYgxX5SXodYU1KzbZOS0t6wK0Ai+qoRKTSJppKxkr8yeg4W2rRbYtGUWikNcNU5FH+sk/CbxpiyU1Jp61g40PlPnlKSgFS1BI6TExXJBi4DhcV0IH1NoqmllUmnHWmHOAZCiBqZKI6Sr9oYm5iWm2Zxp1QfacDiXN4Uk3Big1diu0mSqLNhwzYK0+osZKT3HGr0fh9aYl0/wAQDjI9b+8WIij0fW1ZiZTlkUIO/rMWwqlQl6TTpufmTZphsrV1kbh1mKlUJiqVCbn31Hhn3Cs9V9wiTrM9KqT/ABSpII5eXuMSlfWgpS8dZPXywzU5J/kdCT0KygG4BBuPM22NE2eErDayOaQpf0+saZy+vKSz4HNuFPcvzjz7Uu2XHnAhA3nwETmkajdEo3YeurxAh+ZmJlWs86tZ6zBICSegQSSScPJTpB9knn6M8v8AhTV1sdTqRmPeEXwvE7XNH2dJGZV0cbMOuXHBpd3BUXi8Xjys6QXVLUJhzIWfmbfIj64tK1mm1dKAf1EJUpJyJES1SmJcizhHsiUriHAA8PfTCFpcSFIUFJO8eb0KYynZjsoHdmYqrH2mnzTYFzqEp9qcx5ufn2ZBnXXmo5IRvUYm5x+ddK3V36ByADoGLqVLbcSk5lJA9pFockplq92yR0pz8MJeYelJhiYYWUOtOBaFDcpJuDGj9YZr1Ikqg1YcKga6fVcGSk9xw0z0rFLbVISS/wDfVp46x/ykn/yMEkkkm5JzJjQnSzX4Kkz7vGyTLuq39CD9MKrUpekU6cn5g2bYbKz17gkdZOQioz0xU56anZlV3n3CtXefAbsG5OYdsUtG3ScvGJdCm2W0L5QADbqxSpSDcG0SFQcZVdB7SNxiXmG5psOI7x0HoOO7bobHAU2X9ZYKz72FRljKTswzyAKOr2TmMLba1JbQpajZKQVE9QzifnFzsyt1XJeyE9CdlxaWkKWo2AEPOKecW4rlUcPJTX/sdQeo77lmZvjs9Tyf/wBCJaWLyrn8AMeUihSsjVmZuWXYzgWtxvoUmwKu+8fZlesI0ZpLVSr1MlX12aW7ddt4QNa3fa0TUpwfHQOIf6GPKzpBruS9CYcyRZ6Z7RHETg2tTa0rScwQYZdS80hxPIRsoUUKBEU6c+zvJVf+Gu2tjbC2xKsGZmWWU/nUBCUhKUpAsAAB7BhpRKZsTQ7C/EHzOkEwWpLgxyurA7hmdqqBwsAp/CFcbFh92VfZfZWUOtLC0KG5STcEeyNFq3L6Q0CnVFkAFxFnED8jiclJ/WPKPNcPpEtoHKXZQgjrVxvrFo0ffMtXKQ70TTYPvKtFaqstRKPP1KZ5phkqI9Y8gSOtRyET88/Up2bnZhV3n3FOL9qjfGkpcDbhJ4hPF2LxeJVV0qT0eBimvF6Tbvyo4h7vMaMSpcmHZkjJoao7SsDIoHpHyxUaUmbk32OEuVA6uW8ZgwpKkKUlQspJII6xF43xfG+Gky7vSqOhBP6m302lpStKkKFwQRD7RZdW2dxx8j2k5p1Ufoj7lmJ66mep9I/8hGkE0Z2t1SYKrhUwsA9KU5D+gwacLTrbiTmhQUO43jyw6UCZVIUKVcuhCUTEzbepQuhGLaFOuJQkZkw02lptKEjIDalTZw+wxQ1/w5hPQoH9covF4vjeM8opFJMpIMtlVnCNZeX5jBkUD0j5cdLKaZWe+0oH8N/l6AseY0jN55sdDI8Sdhw6ra1dCSf0hKgtCFdIBHeMKpL67YdSM0fi9n9sWHnZZ5l9hwodaWFoWnIpUk3BB6RErMibYQ9fMjje3B95Muy44rkSImJh6bfdffcK3HDdSjjS2MlPEdIThkLwysONNr6Ug7Evzo9hihn+JMDpSP6Hb0Zp326oJWpN2mLLV2t2zVqeipSLzBABOaD0KEONraccbWmykEgj2Gx26+b1FY6EJ2JxWrKv9kj9cokVa8qyeq36G0WgpCgQRcERMsmXeWjdfL2Y0aa4N4sqPFc
                                        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" width="100%" style="max-height: 50px; max-width:50px; border:2px double #ccc; border-radius:50%; cursor:pointer;" onclick="ShowImgProfile()" />
                                    @endif
                                </div>
                                <div class="widget-content-left">
                                    <div class="btn-group">
                                        <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn">
                                            <div class="widget-content-left  ml-3" align='left'>
                                                <div class="widget-heading">
                                                    @if (!empty($employee))
                                                        {{$employee->emp_firstname}} {{$employee->emp_lastname}}
                                                    @endif
                                                </div>
                                                <div class="widget-subheading">
                                                    @if (!empty($employee))
                                                        {{$employee->emp_position}}
                                                    @endif
                                                </div>  
                                            </div>
                                            <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn">
                                                <i class="fa fa-angle-down ml-2 opacity-8"></i>
                                            </a>
                                        </a>
                                        <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu dropdown-menu-right">
                                            <a href="{{url('editProfile')}}" class="nav-link" style="padding: 0px;">
                                                <button type="button" tabindex="0" class="dropdown-item">
                                                    <span class="grid-tittle">แก้ไขโปรไฟล์</span>
                                                </button>
                                            </a>
                                            <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="padding: 0px;">
                                                <button type="button" tabindex="0" class="dropdown-item">
                                                        <span class="grid-tittle">ออกจากระบบ</span>
                                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                        @csrf
                                                    </form>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ui-theme-settings">
            {{-- <button type="button" id="TooltipDemo" class="btn-open-options btn btn-warning">
                <i class="fa fa-cog fa-w-16 fa-spin fa-2x"></i>
            </button> --}}
            <div class="theme-settings__inner">
                <div class="scrollbar-container">
                    <div class="theme-settings__options-wrapper">
                        <h3 class="themeoptions-heading">Layout Options
                        </h3>
                        <div class="p-3">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <div class="widget-content p-0">
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left mr-3">
                                                <div class="switch has-switch switch-container-class"
                                                    data-class="fixed-header">
                                                    <div class="switch-animate switch-on">
                                                        <input type="checkbox" checked data-toggle="toggle"
                                                            data-onstyle="success">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="widget-content-left">
                                                <div class="widget-heading">Fixed Header
                                                </div>
                                                <div class="widget-subheading">Makes the header top fixed, always
                                                    visible!
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="widget-content p-0">
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left mr-3">
                                                <div class="switch has-switch switch-container-class"
                                                    data-class="fixed-sidebar">
                                                    <div class="switch-animate switch-on">
                                                        <input type="checkbox" checked data-toggle="toggle"
                                                            data-onstyle="success">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="widget-content-left">
                                                <div class="widget-heading">Fixed Sidebar
                                                </div>
                                                <div class="widget-subheading">Makes the sidebar left fixed, always
                                                    visible!
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="widget-content p-0">
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left mr-3">
                                                <div class="switch has-switch switch-container-class"
                                                    data-class="fixed-footer">
                                                    <div class="switch-animate switch-off">
                                                        <input type="checkbox" data-toggle="toggle"
                                                            data-onstyle="success">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="widget-content-left">
                                                <div class="widget-heading">Fixed Footer
                                                </div>
                                                <div class="widget-subheading">Makes the app footer bottom fixed, always
                                                    visible!
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <h3 class="themeoptions-heading">
                            <div>
                                Header Options
                            </div>
                            <button type="button"
                                class="btn-pill btn-shadow btn-wide ml-auto btn btn-focus btn-sm switch-header-cs-class"
                                data-class="">
                                Restore Default
                            </button>
                        </h3>
                        <div class="p-3">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <h5 class="pb-2">Choose Color Scheme
                                    </h5>
                                    <div class="theme-settings-swatches">
                                        <div class="swatch-holder bg-primary switch-header-cs-class"
                                            data-class="bg-primary header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-secondary switch-header-cs-class"
                                            data-class="bg-secondary header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-success switch-header-cs-class"
                                            data-class="bg-success header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-info switch-header-cs-class"
                                            data-class="bg-info header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-warning switch-header-cs-class"
                                            data-class="bg-warning header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-danger switch-header-cs-class"
                                            data-class="bg-danger header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-light switch-header-cs-class"
                                            data-class="bg-light header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-dark switch-header-cs-class"
                                            data-class="bg-dark header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-focus switch-header-cs-class"
                                            data-class="bg-focus header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-alternate switch-header-cs-class"
                                            data-class="bg-alternate header-text-light">
                                        </div>
                                        <div class="divider">
                                        </div>
                                        <div class="swatch-holder bg-vicious-stance switch-header-cs-class"
                                            data-class="bg-vicious-stance header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-midnight-bloom switch-header-cs-class"
                                            data-class="bg-midnight-bloom header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-night-sky switch-header-cs-class"
                                            data-class="bg-night-sky header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-slick-carbon switch-header-cs-class"
                                            data-class="bg-slick-carbon header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-asteroid switch-header-cs-class"
                                            data-class="bg-asteroid header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-royal switch-header-cs-class"
                                            data-class="bg-royal header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-warm-flame switch-header-cs-class"
                                            data-class="bg-warm-flame header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-night-fade switch-header-cs-class"
                                            data-class="bg-night-fade header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-sunny-morning switch-header-cs-class"
                                            data-class="bg-sunny-morning header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-tempting-azure switch-header-cs-class"
                                            data-class="bg-tempting-azure header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-amy-crisp switch-header-cs-class"
                                            data-class="bg-amy-crisp header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-heavy-rain switch-header-cs-class"
                                            data-class="bg-heavy-rain header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-mean-fruit switch-header-cs-class"
                                            data-class="bg-mean-fruit header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-malibu-beach switch-header-cs-class"
                                            data-class="bg-malibu-beach header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-deep-blue switch-header-cs-class"
                                            data-class="bg-deep-blue header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-ripe-malin switch-header-cs-class"
                                            data-class="bg-ripe-malin header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-arielle-smile switch-header-cs-class"
                                            data-class="bg-arielle-smile header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-plum-plate switch-header-cs-class"
                                            data-class="bg-plum-plate header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-happy-fisher switch-header-cs-class"
                                            data-class="bg-happy-fisher header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-happy-itmeo switch-header-cs-class"
                                            data-class="bg-happy-itmeo header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-mixed-hopes switch-header-cs-class"
                                            data-class="bg-mixed-hopes header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-strong-bliss switch-header-cs-class"
                                            data-class="bg-strong-bliss header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-grow-early switch-header-cs-class"
                                            data-class="bg-grow-early header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-love-kiss switch-header-cs-class"
                                            data-class="bg-love-kiss header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-premium-dark switch-header-cs-class"
                                            data-class="bg-premium-dark header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-happy-green switch-header-cs-class"
                                            data-class="bg-happy-green header-text-light">
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <h3 class="themeoptions-heading">
                            <div>Sidebar Options</div>
                            <button type="button"
                                class="btn-pill btn-shadow btn-wide ml-auto btn btn-focus btn-sm switch-sidebar-cs-class"
                                data-class="">
                                Restore Default
                            </button>
                        </h3>
                        <div class="p-3">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <h5 class="pb-2">Choose Color Scheme
                                    </h5>
                                    <div class="theme-settings-swatches">
                                        <div class="swatch-holder bg-primary switch-sidebar-cs-class"
                                            data-class="bg-primary sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-secondary switch-sidebar-cs-class"
                                            data-class="bg-secondary sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-success switch-sidebar-cs-class"
                                            data-class="bg-success sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-info switch-sidebar-cs-class"
                                            data-class="bg-info sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-warning switch-sidebar-cs-class"
                                            data-class="bg-warning sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-danger switch-sidebar-cs-class"
                                            data-class="bg-danger sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-light switch-sidebar-cs-class"
                                            data-class="bg-light sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-dark switch-sidebar-cs-class"
                                            data-class="bg-dark sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-focus switch-sidebar-cs-class"
                                            data-class="bg-focus sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-alternate switch-sidebar-cs-class"
                                            data-class="bg-alternate sidebar-text-light">
                                        </div>
                                        <div class="divider">
                                        </div>
                                        <div class="swatch-holder bg-vicious-stance switch-sidebar-cs-class"
                                            data-class="bg-vicious-stance sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-midnight-bloom switch-sidebar-cs-class"
                                            data-class="bg-midnight-bloom sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-night-sky switch-sidebar-cs-class"
                                            data-class="bg-night-sky sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-slick-carbon switch-sidebar-cs-class"
                                            data-class="bg-slick-carbon sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-asteroid switch-sidebar-cs-class"
                                            data-class="bg-asteroid sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-royal switch-sidebar-cs-class"
                                            data-class="bg-royal sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-warm-flame switch-sidebar-cs-class"
                                            data-class="bg-warm-flame sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-night-fade switch-sidebar-cs-class"
                                            data-class="bg-night-fade sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-sunny-morning switch-sidebar-cs-class"
                                            data-class="bg-sunny-morning sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-tempting-azure switch-sidebar-cs-class"
                                            data-class="bg-tempting-azure sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-amy-crisp switch-sidebar-cs-class"
                                            data-class="bg-amy-crisp sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-heavy-rain switch-sidebar-cs-class"
                                            data-class="bg-heavy-rain sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-mean-fruit switch-sidebar-cs-class"
                                            data-class="bg-mean-fruit sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-malibu-beach switch-sidebar-cs-class"
                                            data-class="bg-malibu-beach sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-deep-blue switch-sidebar-cs-class"
                                            data-class="bg-deep-blue sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-ripe-malin switch-sidebar-cs-class"
                                            data-class="bg-ripe-malin sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-arielle-smile switch-sidebar-cs-class"
                                            data-class="bg-arielle-smile sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-plum-plate switch-sidebar-cs-class"
                                            data-class="bg-plum-plate sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-happy-fisher switch-sidebar-cs-class"
                                            data-class="bg-happy-fisher sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-happy-itmeo switch-sidebar-cs-class"
                                            data-class="bg-happy-itmeo sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-mixed-hopes switch-sidebar-cs-class"
                                            data-class="bg-mixed-hopes sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-strong-bliss switch-sidebar-cs-class"
                                            data-class="bg-strong-bliss sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-grow-early switch-sidebar-cs-class"
                                            data-class="bg-grow-early sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-love-kiss switch-sidebar-cs-class"
                                            data-class="bg-love-kiss sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-premium-dark switch-sidebar-cs-class"
                                            data-class="bg-premium-dark sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-happy-green switch-sidebar-cs-class"
                                            data-class="bg-happy-green sidebar-text-light">
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <h3 class="themeoptions-heading">
                            <div>Main Content Options</div>
                            <button type="button"
                                class="btn-pill btn-shadow btn-wide ml-auto active btn btn-focus btn-sm">Restore Default
                            </button>
                        </h3>
                        <div class="p-3">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <h5 class="pb-2">Page Section Tabs
                                    </h5>
                                    <div class="theme-settings-swatches">
                                        <div role="group" class="mt-2 btn-group">
                                            <button type="button"
                                                class="btn-wide btn-shadow btn-primary btn btn-secondary switch-theme-class"
                                                data-class="body-tabs-line">
                                                Line
                                            </button>
                                            <button type="button"
                                                class="btn-wide btn-shadow btn-primary active btn btn-secondary switch-theme-class"
                                                data-class="body-tabs-shadow">
                                                Shadow
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-main">
            <div class="app-sidebar sidebar-shadow">
                <div class="app-header__logo">
                    {{-- <div class="logo-src"></div> --}}
                    <div class="header__pane ml-auto">
                        <div>
                            <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                                data-class="closed-sidebar">
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="app-header__mobile-menu">
                    <div>
                        <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="app-header__menu">
                    <span>
                        <button type="button"
                            class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                            <span class="btn-icon-wrapper">
                                <i class="fa fa-ellipsis-v fa-w-6"></i>
                            </span>
                        </button>
                    </span>
                </div>
                <div class="scrollbar-sidebar" style="background: -webkit-linear-gradient(rgb(0, 191, 255), rgb(153, 204, 255));">
                    <div class="app-sidebar__inner">
                        <ul class="vertical-nav-menu">
                            @php
                                $user = Auth::user();
                                $permission = App\Model\Permission::where('emp_id',$user->employee_id)->first();
                            @endphp
                            @if ($user->employee->emp_branch_id != null)
                                @if ($permission->daily_summaries_menu == 0 || $permission->parcel_care_menu == 0 || $permission->receive_parcel_menu == 0)
                                    <li class="app-sidebar__heading">Dashboards</li>
                                    @if ($user->employee->emp_position == 'เจ้าของกิจการ(Owner)' || $user->employee->emp_position == 'ผู้จัดการเขตพื้นที่(Area Manager)')
                                        <li>
                                            <a href="/getUser/{{$user->employee->id}}" class="mm-active">
                                                <i class="metismenu-icon pe-7s-home"></i>
                                                หน้าหลักผู้บริหาร
                                            </a>
                                        </li>
                                    @endif
                                    @if ($permission->daily_summaries_menu == 0)
                                        <li>
                                            <a href="/dashboard" class="mm-active">
                                                <i class="metismenu-icon pe-7s-graph1"></i>
                                                สรุปยอดประจำวัน
                                            </a>
                                        </li>
                                    @endif
                                    @if ($permission->parcel_care_menu == 0)
                                        <li>
                                            <a href="/parcel_care" class="mm-active">
                                                <i class="metismenu-icon pe-7s-map-2"></i>
                                                ตรวจสอบสถานะพัสดุ
                                            </a>
                                        </li>
                                    @endif
                                    @if ($permission->receive_parcel_menu == 0)
                                        <li>
                                            <a href="/bookingList/{{ $user->employee->emp_branch_id }}" class="mm-active">
                                                <i class="metismenu-icon pe-7s-download"></i>
                                                รับพัสดุใหม่
                                            </a>
                                        </li> 
                                    @endif
                                @endif
                                @if ($permission->all_parcel_menu == 0 || $permission->parcel_cls_menu == 0 || $permission->parcel_send_menu == 0 || $permission->parcel_call_recive_menu == 0 || $permission->recive_parcel_from_dc_menu == 0 || $permission->orther_report_menu == 0)
                                    <li class="app-sidebar__heading">DC Management</li>

                                    @if ($permission->parcel_cls_menu == 0)
                                        <li>
                                            <a href="/getclsList">
                                                <i class="metismenu-icon pe-7s-box1"> </i>
                                                พัสดุ CLS
                                            </a>
                                        </li>
                                    @endif

                                    @if ($permission->all_parcel_menu == 0)
                                        <li>
                                            <a href="/tracking_list/{{$user->employee->emp_branch_id}}">
                                                <i class="metismenu-icon pe-7s-attention"></i>
                                                พัสดุ เลื่อนรับ/ติดปัญหา
                                            </a>
                                        </li>
                                    @endif

                                    @if ($permission->parcel_send_menu == 0)
                                        <li>
                                            <a href="#">
                                                <i class="metismenu-icon pe-7s-bicycle"></i>
                                                จ่ายพัสดุ
                                                <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                                            </a>
                                            <ul>
                                                <li>
                                                    <a href="/getDropCenterList">
                                                        <i class="metismenu-icon">
                                                        </i>จ่ายให้ DC ปลายทาง/ต้นทาง
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="/getCurierList/{{$user->employee->emp_branch_id}}">
                                                        <i class="metismenu-icon"></i>
                                                        จ่ายให้ Courier
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="/Courier_cod_closing/{{$user->employee->emp_branch_id}}">
                                                        <i class="metismenu-icon"></i>
                                                        บิลนำส่ง และปิดยอด(COD)
                                                    </a>
                                                </li>                                               
                                                <li>
                                                    <a href="/Tranfer_tracking_list/{{$user->employee->emp_branch_id}}">
                                                        <i class="metismenu-icon"></i>
                                                        รายการนำส่ง
                                                    </a>
                                                </li>                                               
                                                <li>
                                                    <a href="/Tranfer_call_return/{{$user->employee->emp_branch_id}}">
                                                        <i class="metismenu-icon"></i>
                                                        เรียกคืนพัสดุ
                                                    </a>
                                                </li>                                               
                                                <li>
                                                    <a href="/Tranfer_pod_closing/{{$user->employee->emp_branch_id}}">
                                                        <i class="metismenu-icon"></i>
                                                        ปิด POD หน้าร้าน
                                                    </a>
                                                </li>                                               
                                                {{-- <li>
                                                    <a href="/Courier_cod_closing/{{$user->employee->emp_branch_id}}">
                                                        <i class="metismenu-icon"></i>
                                                        รับคืนจาก Courier
                                                    </a>
                                                </li>                                                --}}
                                            </ul>
                                        </li>
                                    @endif
                                    @if ($permission->parcel_call_recive_menu == 0)
                                        <li>
                                            <a href="/getRequestServiceList/{{$user->employee->emp_branch_id}}">
                                                <i class="metismenu-icon pe-7s-pin"></i>
                                                เรียกรถเข้ารับพัสดุ
                                            </a>
                                        </li>
                                    @endif

                                    @if ($permission->recive_parcel_from_dc_menu == 0)
                                        <li>
                                            <a href="/getParcelListFromOtherDC/{{$user->employee->emp_branch_id}}">
                                                <i class="metismenu-icon pe-7s-download"></i>
                                                รับพัสดุจาก DC ต้นทาง
                                            </a>
                                        </li>
                                    @endif
                                    
                                    @if ($permission->orther_report_menu == 0)
                                        <li>
                                            <a href="#">
                                                <i class="metismenu-icon pe-7s-display2"></i>

                                                รายงานต่างๆ
                                                <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                                            </a>
                                            <ul>
                                                {{-- <li>
                                                    <a href="#" data-toggle="modal" data-target="#Income_summary">
                                                        <i class="metismenu-icon"></i> สรุปรายรับประจำเดือน
                                                    </a>
                                                </li> --}}
                                                <li>
                                                    <a href="/report_form/1">
                                                        <i class="metismenu-icon"></i> รายการรับเข้า
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="/report_form/2">
                                                        <i class="metismenu-icon"></i> รายการนำส่ง DVL
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="/report_form/3">
                                                        <i class="metismenu-icon"></i> รายการส่งสำเร็จ
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="/report_form/4">
                                                        <i class="metismenu-icon"></i> รายการส่งไม่สำเร็จ
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="/report_form/5">
                                                        <i class="metismenu-icon"></i> รายการส่งขึ้น LH
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="/report_form/6">
                                                        <i class="metismenu-icon"></i> สรุปยอด COD
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="/report_form/7">
                                                        <i class="metismenu-icon"></i> สรุปรายการขายอื่นๆ
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="/report_form/8">
                                                        <i class="metismenu-icon"></i> ใบ DVL ย้อนหลัง
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="/report_form/9">
                                                        <i class="metismenu-icon"></i> ใบ LH ย้อนหลัง
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                    @endif
                                @endif
                            @else
                                <li class="app-sidebar__heading">Dashboards</li>
                                @if ($user->employee->emp_position == 'เจ้าของกิจการ(Owner)'  || $user->employee->emp_position == 'ผู้จัดการเขตพื้นที่(Area Manager)')
                                    <li>
                                        <a href="/getUser/{{$user->employee->id}}" class="mm-active">
                                            <i class="metismenu-icon pe-7s-home"></i>
                                            หน้าหลักผู้บริหาร
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/parcel_care" class="mm-active">
                                            <i class="metismenu-icon pe-7s-map-2"></i>
                                            ตรวจสอบสถานะพัสดุ
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/drop_center_list_owner" class="mm-active">
                                            <i class="metismenu-icon pe-7s-map-2"></i>
                                            Drop Centers
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="metismenu-icon pe-7s-display2"></i>

                                            รายงานต่างๆ
                                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                                        </a>
                                        <ul>
                                            {{-- <li>
                                                <a href="#" data-toggle="modal" data-target="#Income_summary">
                                                    <i class="metismenu-icon"></i> สรุปรายรับประจำเดือน
                                                </a>
                                            </li> --}}
                                            <li>
                                                <a href="/report_form/1">
                                                    <i class="metismenu-icon"></i> รายการรับเข้า
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/report_form/2">
                                                    <i class="metismenu-icon"></i> รายการนำส่ง DVL
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/report_form/3">
                                                    <i class="metismenu-icon"></i> รายการส่งสำเร็จ
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/report_form/4">
                                                    <i class="metismenu-icon"></i> รายการส่งไม่สำเร็จ
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/report_form/5">
                                                    <i class="metismenu-icon"></i> รายการส่งขึ้น LH
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/report_form/6">
                                                    <i class="metismenu-icon"></i> สรุปยอด COD
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/report_form/7">
                                                    <i class="metismenu-icon"></i> สรุปรายการขายอื่นๆ
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/report_form/8">
                                                    <i class="metismenu-icon"></i> ใบ DVL ย้อนหลัง
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/report_form/9">
                                                    <i class="metismenu-icon"></i> ใบ LH ย้อนหลัง
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                            @endif

                            @if ($permission->customer_menu == 0 || $permission->employ_menu == 0 || $permission->permiss_menu == 0 || $permission->dropcenter_menu == 0 || $permission->orther_sale_menu == 0 || $permission->service_price_menu == 0 || $permission->parcel_type_menu == 0)
                                <li class="app-sidebar__heading">Parcel Management</li>
                                {{-- @if ($permission->parcel_status_wrong_menu == 0) --}}
                                    {{-- <li>
                                        <a href="/getParcelWrongList">
                                            <i class="metismenu-icon pe-7s-mouse">
                                            </i>พัสดุติดปัญหา
                                        </a>
                                    </li> --}}
                                {{-- @endif --}}
                                <li>
                                    <a href="#">
                                        <i class="metismenu-icon pe-7s-tools">
                                        </i>
                                        กำหนดข้อมูลพื้นฐาน  
                                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                                    </a>
                                    <ul>
                                        @if ($permission->customer_menu == 0)
                                            <li>
                                                <a href="/get_customer_list/{{$user->employee->emp_branch_id}}">
                                                    <i class="metismenu-icon"></i>
                                                    ข้อมูลลูกค้า
                                                </a>
                                            </li>
                                        @endif

                                        @if ($permission->employ_menu == 0)
                                            <li>
                                                <a href="/employee_list/{{$user->employee->emp_branch_id}}">
                                                    <i class="metismenu-icon"></i>
                                                    ข้อมูลพนักงาน
                                                </a>
                                            </li>
                                            
                                            <li>
                                                <a href="/courier_login_his/{{$user->employee->emp_branch_id}}">
                                                    <i class="metismenu-icon"></i>
                                                    การล็อกอินของ Courier & LineHaull
                                                </a>
                                            </li>
                                            
                                            <li>
                                                <a href="/requerest_password/{{$user->employee->emp_branch_id}}">
                                                    <i class="metismenu-icon"></i>
                                                    การขอ Reset Password
                                                </a>
                                            </li>
                                        @endif

                                        @if ($permission->permiss_menu == 0)
                                            <li>
                                                <a href="/permission_get_list">
                                                    <i class="metismenu-icon"></i>
                                                    กำหนดสิทธิ์การเข้าถึง
                                                </a>
                                            </li>
                                        @endif

                                        @if ($permission->dropcenter_menu == 0)
                                            <li>
                                                <a href="/dropcenter_get_list/{{$user->employee->emp_branch_id}}">
                                                    <i class="metismenu-icon">
                                                    </i>ข้อมูล DropCenter
                                                </a>
                                            </li>
                                        @endif

                                        @if ($permission->orther_sale_menu == 0)
                                            <li>
                                                <a href="/product_price_get_list/{{$user->employee->emp_branch_id}}">
                                                    <i class="metismenu-icon">
                                                    </i>ราคากล่องพัสดุ
                                                </a>
                                            </li>
                                        @endif

                                        @if ($permission->service_price_menu == 0)
                                            <li>  
                                                <a href="/parcel_price_get_list/{{$user->employee->emp_branch_id}}">
                                                    <i class="metismenu-icon">
                                                    </i>อัตราค่าบริการ
                                                </a>
                                            </li>
                                        @endif

                                        @if ($permission->parcel_type_menu == 0)
                                            <li>
                                                <a href="/parceltype_get_list/{{$user->employee->emp_branch_id}}">
                                                    <i class="metismenu-icon">
                                                    </i>ประเภทพัสดุและเงื่อนไข
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="app-main__outer">
                <div class="app-main__inner">
                    <div class="app-page-title">
                        <div class="page-title-wrapper">
                            <div class="page-title-heading"> 
                                <div class="page-title-icon">
                                    <i class="pe-7s-car icon-gradient bg-mean-fruit">
                                    </i>
                                </div>
                                <div>
                                    @if ($user->employee->emp_branch_id != "")
                                        {{$user->employee->dropCenter->drop_center_name_initial}} {{$user->employee->dropCenter->drop_center_name}}
                                    @else
                                        ระบบจัดการพัสดุแบบออนไลน์
                                    @endif
                                    <div class="page-title-subheading"> SERVICE EXPRESS SYSTEM</div>
                                </div>
                            </div>
                            <div class="page-title-actions">
                                @if ($user->employee->emp_branch_id != null && $user->employee->emp_position != 'พนักงานจัดส่งพัสดุ(Courier)' && $user->employee->emp_position != 'พนักงานส่งพัสดุ(Line Haul)')
                                    {{-- <a href="/input" class="nav-link">
                                        <button type="button" class="btn-shadow btn btn-info">
                                            <span class="btn-icon-wrapper pr-2 opacity-7">
                                                <i class="metismenu-icon pe-7s-download"></i>
                                            </span>
                                            รับพัสดุใหม่
                                        </button>
                                    </a> --}}
                                    <a href="/create_recive_from_request/{{ $user->employee->emp_branch_id }}">
                                        <button type="button" class="btn-shadow btn btn-success">
                                            <span class="btn-icon-wrapper pr-2 opacity-7">
                                                <i class="metismenu-icon pe-7s-search"></i>
                                            </span>
                                            ยืนยันรับพัสดุจากCourier
                                        </button>
                                    </a>
                                    <a href="/input">
                                        <button type="button" class="btn-shadow btn btn-info">
                                            <span class="btn-icon-wrapper pr-2 opacity-7">
                                                <i class="metismenu-icon pe-7s-download"></i>
                                            </span>
                                            รับพัสดุใหม่
                                        </button>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @include('sweetalert::alert', ['cdn' => "https://cdn.jsdelivr.net/npm/sweetalert2@9"])
                        @yield("content")
                    </div>
                </div>
            </div>
            <script src="http://maps.google.com/maps/api/js?sensor=true"></script>
        </div>
    </div>
    <script type="text/javascript" src="/architectui-html-free/architectui-html-free/assets/scripts/main.js"></script>
</body>

</html>

{{-- <div class="modal fade" id="Income_summary" tabindex="-1" role="dialog" aria-labelledby="Income_summaryTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header text-white bg-success">
          <h5 class="modal-title" id="exampleModalLongTitle">สรุปรายการประจำเดือน</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                        <label for="datefrom" class="col-2 col-form-label">From</label>
                        <div class="col-10">
                        <input class="form-control" type="date" value="" id="datefrom" name="datefrom" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <label for="dateto" class="col-2 col-form-label">To</label>
                        <div class="col-10">
                        <input class="form-control" type="date" value="" id="dateto" name="dateto" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary pull-right" onclick="Income_summarymount()">ค้นหา</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div style="margin-bottom: -10px;">
                        <b>รายการ</b> 
                    </div>
                    <hr>
                    <div id="mountlist"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-dismiss="modal">ปิด</button>
        </div>
      </div>
    </div>
</div> --}}
<script>
    var month= [
                "January","February","March","April","May","June","July",
                "August","September","October","November","December"
            ];
    // BookmountList();
    // function BookmountList(){
    //     $.ajax({
    //         method:"POST",
    //         url:"{{url('find_mountgroup')}}",
    //         dataType: 'json',
    //         data:{"_token": "{{ csrf_token() }}",},
    //         success:function(data){
    //             content = "<ul class='list-group'>";
    //             $.each(data, function(i, item){
    //                 mount = item.mounth.substring(5, 7);
    //                 year = item.mounth.substring(0, 4);
    //                 $.each(month, function(monthnum, monthitem){
    //                     if(mount == monthnum+1){
    //                         content += "<a href='/Income_summarymount/{{$user->employee->emp_branch_id}}/"+item.mounth+"' target='blank'><li class='list-group-item'>"+monthitem+" "+year+"</li></a>";
    //                     }
    //                 });
    //             });
    //             content += "</ul>";

    //             $("#mountlist").html(content);
    //         }
    //     });
    // }

    // function Income_summarymount(){
    //     datefrom = $("#datefrom").val();
    //     dateto = $("#dateto").val();
    //     if(datefrom != "" && dateto != ""){
    //         window.open("/Income_summarymount/{{$user->employee->emp_branch_id}}/"+datefrom+"/"+dateto, "_blank")
    //     }else{
    //         alert('โปรดเลือกช่วงเวลาให้ถูกต้อง');
    //     }
        
    // }

    Webcam.set({
        width: 320,
        height: 240,
        crop_width: 240,
        crop_height: 240
    });

    function ShowImgProfile(){
        imgProfile = '{{$employee->emp_image}}';
        if(imgProfile == ''){
            imgProfile = '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';
        }
        Swal.fire({
            showCancelButton: false,
            showConfirmButton: false,
            reverseButtons: false,
            html:   '<div class="row" style="margin-left: -58px !important; margin-right: -58px !important; margin-top: -15px !important;">'+
                        '<div class="col-lg-12 col-md-12">'+
                            ''+
                            '<img id="profileshow"><button class="btn btn-success" style="position: absolute; top:2px; right:13px;" onclick="call_EditProfileform()"><i class="fa fa-pencil"></i> Edit</button>'+
                            '<div class="row" id="Edit_PF_form" >'+
                                '<div class="col-2"></div>'+
                                '<div class="col-8"><br>'+
                                    '<form id="UpdateProfileForm" action="{{url('update_img_profile')}}" method="post">'+
                                        '<input type="hidden" name="new_photo" id="new_photo" />'+
                                        '<div class="input-group mb-3">'+
                                            '{{csrf_field()}}'+
                                            
                                            '<input type="file" class="form-control" onchange="readURLProFile(this, \'#profileshow\')" />'+
                                            '<div class="input-group-append">'+
                                                '<button class="btn btn-success">Upload</button>'+
                                            '</div>'+
                                        '</div>'+
                                    '</form>'+
                                '</div>'+
                                '<div class="col-2"></div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'
        });
        $("#Edit_PF_form").hide();
        $("#profileshow").attr('src', 'data:image/jpeg;base64,'+imgProfile).width('100%');
        
    }

    function call_EditProfileform(){
        $("#Edit_PF_form").show();
    }

    function readURLProFile(input, id){
        $(id).attr('src', '').width('100%');
        if(input.files && input.files[0]){
            var r = new FileReader();
            r.onload = function(e){
                $(id).attr('src', e.target.result).width('100%');
                base64 = e.target.result;
                n = base64.search("jpeg");
                if(n > 0){
                    new_base64 = base64.replace("data:image/jpeg;base64,", "");
                }else{
                    new_base64 = base64.replace("data:image/png;base64,", "");
                }
                $("#new_photo").val(new_base64);
            }
            r.readAsDataURL(input.files[0]);
        }
    }

</script>