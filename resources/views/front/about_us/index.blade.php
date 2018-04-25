@extends('front.template.master')

@section('title')
	About Us
@endsection

@section('meta')
    <meta name="description" content="{{$setting->about_us_meta_desc}}">
@endsection

@section('head_additional')
    <script type="text/javascript">
        // Find all YouTube videos
        var $allVideos = $("iframe[src^='//www.youtube.com']"),

            // The element that is fluid width
            $fluidEl = $("body");

        // Figure out and save aspect ratio for each video
        $allVideos.each(function() {

          $(this)
            .data('aspectRatio', this.height / this.width)

            // and remove the hard coded width/height
            .removeAttr('height')
            .removeAttr('width');

        });

        // When the window is resized
        $(window).resize(function() {

          var newWidth = $fluidEl.width();

          // Resize all videos according to their own aspect ratio
          $allVideos.each(function() {

            var $el = $(this);
            $el
              .width(newWidth)
              .height(newWidth * $el.data('aspectRatio'));

          });

        // Kick off one resize to fix all videos on page load
        }).resize();
    </script>
@endsection

@section('content')
    <section class="about">
        <div class="about-header">{{HTML::image('img/front/about-us.png')}}</div>
        <div class="about-content">
            <span>
                {!!$setting->about_us!!}
            </span>
        </div>
    </section>
@endsection