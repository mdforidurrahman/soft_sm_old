<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Index - SM Sunlight Group</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link href="{{ asset('assets/img/SM-Sunlight-group-logo.png') }}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">


</head>

<body class="index-page">

    <header id="header" class="header d-flex align-items-center fixed-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

            <a href="index.html" class="logo d-flex align-items-center">
                <!-- <img src="assets/img/logo.png" alt=""> -->
                <h1 class="sitename">SM Sunlight Group</h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="https://smsunlight.com/" class="active" target="_blank">Home</a></li>
                    <li><a href="https://smsunlight.com/about" target="_blank">About</a></li>
                    <li><a href="https://smsunlight.com/contact-us/" target="_blank">Contact</a></li>
                    @auth
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    @else
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('register') }}">Register</a></li>
                    @endauth
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

        </div>
    </header>









    <footer id="footer" class="footer dark-background" style="padding-top: 150px; ">

        <div style="padding-bottom: 50px;">
            <p style="text-align: center;"><img src="{{ asset('assets/img/SM-Sunlight-group-logo.png') }}" alt="Logo"
                    width="200"> </p>
            <p style="font-size: 40px;text-align: center;">
                SM Sunlight Group
            </p>
<div style="text-align: center; background:#08005E;padding:20px;margin-top:50px;">
                <section class="table-container">
                    <h2 class="text-center mb-4" style="font-size: 20px;color: white;">Unit Office Locations</h2>
                    <table
                        style="margin: 0 auto; border-collapse: collapse; font-size: 15px; width: 100%; text-align: center; border:2px solid #fff; color:white;">
                        <thead style="border:2px solid #fff;">
                            <tr style="background-color: #4CAF50; color: white;">
                                <th style="border: 1px solid #ddd; padding: 8px;">Serial No</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Unit Office Name</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Location</th>
                            </tr>
                        </thead>
                        <tbody >
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;">1</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">SM Sunlight Group</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">হেড-অফিস, সোহাগ ফিলিং স্টেশনের বিপরীত পাশে,
                                    নতুন শাহাপুর, নওগাঁ।</td>
                            </tr>
                            <tr style="background-color:rgb(2, 150, 249);">
                                <td style="border: 1px solid #ddd; padding: 8px;">2</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">সানলাইট ব্যাটারি হাউজ</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">বরুণকান্দি মোড়, বদলগাছি রোড, নওগাঁ।
                                </td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;">3</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">মায়ের দোয়া ব্যাটারি & IPS</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">আশিক প্লাজা, গোস্তহাটির মোড়, নওগাঁ।
                                </td>
                            </tr>
                            <tr style="background-color: rgb(2, 150, 249);">
                                <td style="border: 1px solid #ddd; padding: 8px;">4</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">সানলাইট ব্যটারি & IPS</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">মেইন রোড, দেলুয়াবাড়ি বাজার, মান্দা,
                                    নওগাঁ।</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;">5</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">রংধনু ব্যাটারি হাউজ</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">আত্রই রোড, রেল-স্টেশনের পাশে,
                                    রানীনগর, নওগাঁ।</td>
                            </tr>
                            <tr style="background-color: rgb(2, 150, 249);">
                                <td style="border: 1px solid #ddd; padding: 8px;">6</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">সানলাইট ব্যাটারি হাউজ-2</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">সেনপাড়া, নতুন ব্রিজ, বদলগাছি, নওগাঁ।</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;">7</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">সানলাট ব্যাটারি হাউজ-3</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">আলীমদীর মোড়, বান্দাইখাড়া রোড, ভবানীগঞ্জ, বাগমারা।</td>
                            </tr>
                            <tr style="background-color: rgb(2, 150, 249);">
                                <td style="border: 1px solid #ddd; padding: 8px;">8</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">সানলাইট ব্যাটারি হাউজ-4</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">সোহাগ ফিলিং স্টেশনের বিপরীত পাশে, নতুন শাহাপুর, নওগাঁ।
</td>
                            </tr>
                        </tbody>
                    </table>
                </section>
            </div>


        </div>

        <div class="container footer-top">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6 footer-about">
                    <a href="index.html" class="logo d-flex align-items-center">
                        <span class="sitename">SM Sunlight Group</span>
                    </a>
                    <div class="footer-contact pt-3">
                        <p>হেড-অফিস, সোহাগ ফিলিং স্টেশনের বিপরীত পাশে
                                    </p>
                        <p>নতুন শাহপুর, নওগাঁ।</p>
                        <p class="mt-3"><strong>Phone:</strong> <span>+880 1957 66 77 11</span></p>
                        <p><strong>Email:</strong> <span>info.smsunlight@gmail.com</span></p>
                    </div>
                    <div class="social-links d-flex mt-4">
                        <a href="https://x.com/smsunlightgroup"><i class="bi bi-twitter-x"></i></a>
                        <a href="https://www.facebook.com/smsunlightgroup"><i class="bi bi-facebook"></i></a>
                        <a href="https://www.instagram.com/sm_sunlight_group"><i class="bi bi-instagram"></i></a>
                        <a href="https://www.linkedin.com/company/99308584/admin/dashboard/"><i
                                class="bi bi-linkedin"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 footer-links">
                    <h4>Useful Links</h4>
                    <ul>
                        <li><a href="https://smsunlight.com/" target="_blank">Home</a></li>
                        <li><a href="https://smsunlight.com/about" target="_blank">About us</a></li>
                        <li><a href="https://smsunlight.com/services" target="_blank">Services</a></li>
                        <li><a href="https://smsunlight.com/contact-us" target="_blank">Contact</a></li>
                        <li><a href="#">Terms of service</a></li>
                        <li><a href="#">Privacy policy</a></li>
                    </ul>
                </div>



            </div>
        </div>

        <div class="container copyright text-center mt-4">
            <p>© 2007-2025 <span>Copyright</span> <strong class="px-1 sitename">SM Sunlight Group</strong> <span>All
                    Rights
                    Reserved</span>
            </p>
        </div>

    </footer>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    {{-- <div id="preloader"></div> --}}

    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
    <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Main JS File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

</body>

</html>