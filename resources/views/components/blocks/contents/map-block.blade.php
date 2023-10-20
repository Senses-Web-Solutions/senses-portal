<div @class(['mb-0' => $content['bottom_space'] == 'none', 'mb-10'=> $content['bottom_space'] == 'small', 'mb-16' => $content['bottom_space'] == 'medium', 'mb-24' => $content['bottom_space'] == 'large'])>
    <img class="mx-auto" src="https://api.mapbox.com/styles/v1/mapbox/outdoors-v11/static/pin-s+555555({{$content['longitude']}},{{$content['latitude']}})/{{$content['longitude']}},{{$content['latitude']}},13.75,0/{{$content['width'] == 'small' ? '960x330' : '1216x632'}}?access_token=pk.eyJ1IjoibWF0dGhpc2NvY2siLCJhIjoiY2szOGo1OGx6MDk5NTNncGlxYjNkbHk1cCJ9.YJP7ged0QX3mMwY4m8ERwg"
        alt="">
</div>
