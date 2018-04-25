<?php
    use Illuminate\Support\Str;
    
    use App\Models\Setting;
    use App\Models\Category;
    use App\Models\Faq;
    use App\Models\Shipping;
    use App\Models\News;
    use App\Models\Product;
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <?php 
            $setting = Setting::first(); 
            $categories = Category::where('level', '=', 1)->where('is_active', '=', 1)->orderBy('created_at', 'asc')->get();
            $faq = Faq::first();
            $shippings = Shipping::where('is_active', '=', 1)->orderBy('created_at', 'asc')->take(5)->get();
            $news = News::where('is_active', '=', 1)->count();
            $sales = Product::where('is_active', '=', 1)->where('discount', '!=', 0)->count();
            $no = 0;
        ?>

        <title>Remax Indonesia | @yield('title')</title>
        <link rel="shortcut icon" href="{{URL::to('img/front/remax_fav_icon.png')}}" />
        
        @yield('meta')
        <meta name="author" content="Remax Indonesia">
        <meta name="robot" content="index,follow">
        <meta name="copyright" content="Copyright © 2015 CREIDS">
        <meta name="generator" content="iremax.id">
        <meta name="language" content="id">
        <meta name="revisit-after" content="1"> 
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Style -->
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
        <!-- Style -->
        {!!HTML::style('css/style.css')!!}

        @if (isset($styles))
            @foreach ($styles as $key => $style)
                {{HTML::style($style)}}
            @endforeach
        @endif

        <!-- JS -->
        {{HTML::script('js/jquery-1.8.3.min.js')}}
        {{HTML::script('js/jquery-ui.min.js')}}
        {{HTML::script('js/jquery.easing.1.3.js')}}

        @if (isset($scripts))
            @foreach ($scripts as $key => $script)
                {{HTML::script($script)}}
            @endforeach
        @endif

        <script type="text/javascript">
            $(document).ready(function() {
                cek = 0;
                $('.nav-toggle').click(function(){
                    if (cek == 0)
                    {
                        cek = 1;
                        
                        $('.line1').stop().animate({
                            'top': '4px',
                            'myRotationProperty': -40
                        },
                        {
                            step: function(now, tween) {
                                if (tween.prop === "myRotationProperty") {
                                    $(this).css('-webkit-transform','rotate('+now+'deg)');
                                    $(this).css('-moz-transform','rotate('+now+'deg)'); 
                                    // add Opera, MS etc. variants
                                    $(this).css('transform','rotate('+now+'deg)');  
                                    $(this).css('-backface-visibility','hidden');  
                                }
                            }
                        });
                        $('.line2').animate({
                            'opacity': 0
                        });
                        $('.line3').stop().animate({
                            'top': '-8px',
                            'myRotationProperty': 40
                        },
                        {
                            step: function(now, tween) {
                                if (tween.prop === "myRotationProperty") {
                                    $(this).css('-webkit-transform','rotate('+now+'deg)');
                                    $(this).css('-moz-transform','rotate('+now+'deg)'); 
                                    // add Opera, MS etc. variants
                                    $(this).css('transform','rotate('+now+'deg)');  
                                    $(this).css('-backface-visibility','hidden');  
                                }
                            }
                        });

                        $('.nav-toggle#nav-toggle2').show();
                        $('.header-left').animate({'right': '0', 'opacity': 1}, 600, 'easeInOutCubic');
                        $('.nav-toggle#nav-toggle2').animate({'opacity': '1'}, 600, 'easeInOutCubic');
                       
                    }
                    else
                    {
                        cek = 0;

                        $('.line1').stop().animate({
                            'top': '0px',
                            'myRotationProperty': 0
                        },
                        {
                            step: function(now, tween) {
                                if (tween.prop === "myRotationProperty") {
                                    $(this).css('-webkit-transform','rotate('+now+'deg)');
                                    $(this).css('-moz-transform','rotate('+now+'deg)'); 
                                    // add Opera, MS etc. variants
                                    $(this).css('transform','rotate('+now+'deg)');  
                                    $(this).css('-backface-visibility','hidden');  
                                }
                            }
                        });
                        $('.line2').animate({
                            'opacity': 1
                        });
                        $('.line3').stop().animate({
                            'top': '0px',
                            'myRotationProperty': 0
                        },
                        {
                            step: function(now, tween) {
                                if (tween.prop === "myRotationProperty") {
                                    $(this).css('-webkit-transform','rotate('+now+'deg)');
                                    $(this).css('-moz-transform','rotate('+now+'deg)'); 
                                    // add Opera, MS etc. variants
                                    $(this).css('transform','rotate('+now+'deg)');  
                                    $(this).css('-backface-visibility','hidden');  
                                }
                            }
                        });

                        $('.header-left').animate({'right': '-100%', 'opacity': 0}, 600, 'easeInOutCubic');
                        $('.nav-toggle#nav-toggle2').animate({'opacity': '0'}, 600, 'easeInOutCubic');
                        $('.nav-toggle#nav-toggle2').delay(600).hide();
                    }
                });
                
                var after = 0;
                var after1 = 0;
                var after2 = 0;
                $('.nav-cart').click(function(e){
                    e.stopPropagation();
                    @if(Auth::guest() != null)
                        $('.header-login').slideUp();
                    @else
                        $('.menu-small').slideUp();
                    @endif
                    $('.header-search').slideUp();
                    after1 = 0;
                    after2 = 0;
                    $('.header-right .nav-item').removeAttr('style');
                    $('.header-right .nav-item').find('.nav-icon-black').show();

                    if(after == 0)
                    {
                        $(this).css({'background': '#fff'});
                        $(this).find('.nav-icon-black').hide();
                        $('.loader-nav-cart').show();
                        $.ajax({
                            type: "GET",
                            url: "{{URL::to('small-cart')}}",
                            success: function(msg){
                                $('.loader-nav-cart').hide();
                                $('.cart-small').html(msg);
                                $('.cart-small').slideDown();
                            },
                            error: function(msg) {
                                $('body').html(msg.responseText);
                            }
                        });
                        after = 1;
                    }
                    else
                    {
                        $('.cart-small').slideUp();
                        $('.header-right .nav-item').removeAttr('style');
                        $('.header-right .nav-item').find('.nav-icon-black').show();
                        after = 0;
                    }
                });

                
                $('.nav-login').click(function(e){
                    e.stopPropagation();
                    $('.cart-small').slideUp();
                    $('.header-search').slideUp();

                    after = 0;
                    after2 = 0;

                    $('.header-right .nav-item').removeAttr('style');
                    $('.header-right .nav-item').find('.nav-icon-black').show();

                    if(after1 == 0)
                    {
                        $(this).css({'background': '#fff'});
                        $(this).find('.nav-icon-black').hide();
                        @if(Auth::guest() != null)
                            $('.header-login').slideDown();
                        @else
                            $('.menu-small').slideDown();
                        @endif
                        after1 = 1;
                    }
                    else
                    {
                        @if(Auth::guest() != null)
                            $('.header-login').slideUp();
                        @else
                            $('.menu-small').slideUp();
                        @endif
                        $('.header-right .nav-item').removeAttr('style');
                        $('.header-right .nav-item').find('.nav-icon-black').show();
                        after1 = 0;
                    }
                });

                $('.nav-search').click(function(e){
                    e.stopPropagation();
                    @if(Auth::guest() != null)
                        $('.header-login').slideUp();
                    @else
                        $('.menu-small').slideUp();
                    @endif
                    $('.cart-small').slideUp();

                    after = 0;
                    after1 = 0;

                    $('.header-right .nav-item').removeAttr('style');
                    $('.header-right .nav-item').find('.nav-icon-black').show();

                    if(after2 == 0)
                    {
                        $(this).css({'background': '#fff'});
                        $(this).find('.nav-icon-black').hide();
                        $('.header-search').slideDown();
                        after2 = 1;
                    }
                    else
                    {
                        $('.header-search').slideUp();
                        $('.header-right .nav-item').removeAttr('style');
                        $('.header-right .nav-item').find('.nav-icon-black').show();
                        after2 = 0;
                    }
                });

                $('.cart-small, .header-login, .header-search').click(function(e){
                    e.stopPropagation();
                });

                $('body').click(function(){
                    $('.cart-small').slideUp();
                    $('.header-right .nav-item').removeAttr('style');
                    $('.header-right .nav-item').find('.nav-icon-black').show();

                    @if(Auth::guest() != null)
                        $('.header-login').slideUp();
                    @else
                        $('.menu-small').slideUp();
                    @endif
                    $('.header-search').slideUp();
                    after = 0;
                    after1 = 0;
                    after2 = 0;
                });

                $('.nav-parent').click(function(){
                    $('.nav-subnav').hide();

                    var parent_id = $(this).attr('dataId');
                    $('.nav-subnav-' + parent_id).stop().slideDown();
                });

                $('.validation-message').click(function(){
                    $(this).hide();
                });

                $('.scrolltop').click(function(){
                    $("html, body").animate({ scrollTop: 0 }, 600);
                    return false;
                });

                $('.success-message').delay(3000).fadeOut(700);

                $('.notification-form img').click(function(){
                    $('.notification').fadeOut(400);
                    $('.notification-product-id').val('');
                });

                $('.warning-close').click(function(){
                    $('.warning').fadeOut(400);
                });

                $(window).scroll(function(){
                    if ($(this).scrollTop() > 200) {
                        $('.scrolltop').css({'display': 'block'});
                    } else {
                        $('.scrolltop').fadeOut();
                    }
                });
            });
        </script>

        
        @yield('head_additional')
    
        <script>
            // !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            // n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            // n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            // t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
            // document,'script','https://connect.facebook.net/en_US/fbevents.js');

            // fbq('init', '1558716754427455');
            // fbq('track', "PageView");
        </script>
        <noscript>
            {{-- <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1558716754427455&ev=PageView&noscript=1" /> --}}
        </noscript>
    </head>
    <body>
        @if($setting->google_analytics != null)
            <script>
                {{$setting->google_analytics}}
            </script>
        @endif
        
        <!-- Header -->
        <header class="header">
            <a href="{{URL::to('/')}}">{!!HTML::image('img/front/logo.png', 'Remax', array('class'=>'logo'))!!}</a>
            <div class="header-left">
                @foreach($categories as $category)
                    <?php 
                        $sub_cats = Category::where('parent_id', '=', $category->id)->where('is_active', '=', true)->get();
                        $no++; 
                    ?>
                    @if($sub_cats->isEmpty())
                        <a href="{{URL::to('product/category/' . $category->id . '/' . Str::slug($category->name, '-'))}}">
                            <nav class="nav-item nav-item-{{$category->id}}">
                                {{$category->name}}
                                @if($sales != 0)
                                    <div class="nav-item-line"></div>
                                @else
                                    @if($no != count($categories))
                                        <div class="nav-item-line"></div>
                                    @endif
                                @endif
                            </nav>
                        </a>
                    @else
                        <nav class="nav-item nav-item-{{$category->id}} nav-parent" dataId="{{$category->id}}">
                            {{$category->name}}
                            <div class="nav-item-line"></div>
                        </nav>
                        <div class="nav-subnav nav-subnav-{{$category->id}}">
                            @foreach($sub_cats as $sub_cat)
                                 <a href="{{URL::to('product/category/' . $sub_cat->id . '/' . Str::slug($sub_cat->name, '-'))}}">
                                    <span>{{$sub_cat->name}}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                @endforeach
                @if($sales != 0)
                    <a href="{{URL::to('product/category/sale/all-product')}}">
                        <nav class="nav-item nav-item-sale">Sale!</nav>
                    </a>
                @endif
            </div>
            <div class="header-right">
                <nav class="nav-item nav-search">
                    {{HTML::image('img/front/icon-glass-yellow.png', '', array('class'=>'nav-icon-yellow'))}}
                    {{HTML::image('img/front/icon-glass-black.png', '', array('class'=>'nav-icon-black'))}}
                </nav>
                <nav class="nav-item nav-login">
                    {{HTML::image('img/front/icon-member-yellow.png', '', array('class'=>'nav-icon-yellow'))}}
                    {{HTML::image('img/front/icon-member-black.png', '', array('class'=>'nav-icon-black'))}}
                </nav>
                <nav class="nav-item nav-cart">
                    {{HTML::image('img/front/icon-cart-yellow.png', '', array('class'=>'nav-icon-yellow'))}}
                    {{HTML::image('img/front/icon-cart-black.png', '', array('class'=>'nav-icon-black'))}}
                    {{HTML::image('img/loader.gif', '', array('class'=>'loader-nav-cart'))}}
                </nav>
                <nav class="nav-item nav-toggle" id="nav-toggle1">
                    <div class="header-line-group">
                        <div class="header1-line line1"></div>
                        <div class="header1-line line2"></div>
                        <div class="header1-line line3"></div>
                    </div>
                </nav>
            </div>

            <div class="nav-toggle" id="nav-toggle2">
                <div class="header1-line line1"></div>
                <div class="header1-line line2"></div>
                <div class="header1-line line3"></div>
            </div>

            <div class="menu-small">
                <a href="{{URL::to('member/profile')}}">
                    <span>My Profile</span>
                </a>
                <a href="{{URL::to('member/my-transaction')}}">
                    <span>My Transaction</span>
                </a>
                <a href="{{URL::to('logout')}}">
                    <span class="no-border">Sign Out</span>
                </a>
            </div>

            <div class="cart-small">
                
            </div>

            <div class="header-login">
                {!!Form::open(array('url'=>URL::to('login'), 'method'=>'POST'))!!}
                    {!!Form::email('email', '', array('class'=>'login-textfield', 'placeholder'=>'Email'))!!}
                    {!!Form::password('password', array('class'=>'login-textfield', 'placeholder'=>'Password'))!!}
                    {!!Form::submit('SIGN IN', array('class'=>'login-submit'))!!}
                    <span>
                        New Member<br>
                        <a href="{{URL::to('sign-up')}}">Register Now</a>
                    </span>
                    <span class="forgot">
                        Forgot your password?<br>
                        <a href="{{URL::to('password/remind')}}"> Click here</a>
                    </span>
                {!!Form::close()!!}
            </div>

            <div class="header-search">
                {!!Form::open(array('url'=>URL::to('product-search'), 'method'=>'GET'))!!}
                    {{Form::text('src_name', '', array('class'=>'login-textfield text-search', 'placeholder'=>'Keyword'))}}
                    {!!Form::submit('SEARCH', array('class'=>'login-submit'))!!}
                {!!Form::close()!!}
            </div>
        </header>

        <div class="notification">
            {!!Form::open(array('url'=>URL::to('notification'), 'method'=>'POST', 'class'=>'notification-form'))!!}
                {{HTML::image('img/front/cart-delete-yellow.png')}}
                {{Form::label('notify_email', 'Notify me when product is back in stock', array('class'=>'notification-label'))}}
                {{Form::email('notify_email', '', array('class'=>'notification-email', 'placeholder'=>'Your email', 'required'))}}
                {{Form::hidden('product_id', '', array('class'=>'notification-product-id'))}}
                {!!Form::submit('SEND', array('class'=>'notification-submit'))!!}
            {!!Form::close()!!}
        </div>

        <!-- Content -->
        <div class="content">
            @yield('content')
        </div>
        <div class="cart-loader">{!!HTML::image('img/loading.gif')!!}</div>

        <div class="warning">
            <div class="warning-content">
                <h2>
                    HATI-HATI PRODUK PALSU!!!
                    <span><strong>www.iremax.id</strong> hanya menjual produk asli Remax. Untuk segala pembelian diluar <strong>www.iremax.id</strong> kami tidak bertanggung jawab atas keasliannya.</span>
                </h2>
                {{HTML::image('img/front/close-black.png', 'x', array('class'=>'warning-close'))}}
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            {{HTML::image('img/front/scroll-top.jpg', '', array('class'=>'scrolltop'))}}
            <div class="footer-border"></div>
            <div class="footer-left">
                <div class="footer-navigation">
                    <h5>ABOUT REMAX</h5>
                    <a href="{{URL::to('about-us')}}">
                        <nav class="footer-nav">About Us</nav>
                    </a>
                    @if($news != 0)
                        <a href="{{URL::to('news')}}">
                            <nav class="footer-nav">News</nav>
                        </a>
                    @endif
                    <a href="{{URL::to('contact-us')}}">
                        <nav class="footer-nav">Contact Us</nav>
                    </a>
                </div>
                <div class="footer-navigation">
                    <h5>SHIPPING &amp; POLICIES</h5>
                    @foreach($shippings as $shipping)
                        <a href="{{URL::to('shipping-and-policies/' . $shipping->id . '/' . Str::slug($shipping->title, '-'))}}">
                            <nav class="footer-nav">{{$shipping->title}}</nav>
                        </a>
                    @endforeach
                </div>
                <div class="footer-navigation">
                    <h5>SHOP</h5>
                    @if($setting->how_to_buy != null)
                        <a href="{{URL::to('how-to-buy')}}">
                            <nav class="footer-nav">How to Buy</nav>
                        </a>
                    @endif
                    @if($faq != null)
                        <a href="{{URL::to('faq/' . $faq->id . '/' . Str::slug($faq->title, '-'))}}">
                            <nav class="footer-nav">FAQ</nav>
                        </a>
                    @endif
                    <a href="{{URL::to('payment-confirmation')}}">
                        <nav class="footer-nav">Payment Confirmation</nav>
                    </a>
                </div>
            </div><!--
         --><div class="footer-right">
                {!!Form::open(array('url'=>URL::to('register-newsletter'), 'method'=>'POST', 'class'=>'footer-from'))!!}
                    {!!Form::email('email', '', array('class'=>'footer-textfield', 'placeholder'=>'Sign-up for newsletter', 'required'))!!}
                    {!!Form::image('img/front/footer-submit.jpg', '', array('class'=>'footer-submit'))!!}
                {!!Form::close()!!}
                <div class="footer-right-top">
                    @if(($setting->facebook != null) OR ($setting->twitter != null) OR ($setting->instagram != null))
                        <span>Keep in touch with us: </span>
                        @if($setting->facebook != null)
                            <a href="http://facebook.com/{{$setting->facebook}}" class="footer-facebook" target="_blank"></a>
                        @endif
                        @if($setting->twitter != null)
                            <a href="http://twitter.com/{{$setting->twitter}}" class="footer-twitter" target="_blank"></a>
                        @endif
                        @if($setting->instagram != null)
                            <a href="http://instagram.com/{{$setting->instagram}}" class="footer-instagram" target="_blank"></a>
                        @endif
                    @endif
                </div>
                <div class="footer-right-bottom"> 
                    <div class="footer-contact">
                        <span class="footer-costumer">Customer Service:</span>
                        @if($setting->phone != null)
                            {!!HTML::image('img/front/phone.png', '', array('class'=>'phone'))!!} {{$setting->phone}}
                        @endif
                        @if($setting->bbm != null)
                            {!!HTML::image('img/front/bbm.png', '', array('class'=>'bbm'))!!} {{$setting->bbm}}
                        @endif
                        @if($setting->whatsapp != null)
                            {!!HTML::image('img/front/whatsapp.png', '', array('class'=>'whatsapp'))!!} {{$setting->whatsapp}}
                        @endif
                        @if($setting->line != null)
                            {!!HTML::image('img/front/line.png', '', array('class'=>'line'))!!} {{$setting->line}}
                        @endif
                    </div>
                    © 2015 Remax Indonesia - My Device My Life.<span> Website developed by {{HTML::link('http://creids.net', 'Creids', array('target'=>'_blank'))}}</span>
                </div>
            </div>
        </footer>
    </body>
</html>
