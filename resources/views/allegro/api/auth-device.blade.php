<span>device_code</span>:{{$device_code}} <br/>
<span>expires_in</span>:{{$expires_in}}<br/>
<span>user_code</span>:{{$user_code}}<br/>
<span>interval</span>:{{$interval}}<br/>

<span>verification_uri</span>:<a href="{{$verification_uri}}" target="_blank">{{$verification_uri}}</a><br/>
<span>verification_uri_complete</span>:<a href="{{$verification_uri_complete}}" target="_blank">{{$verification_uri_complete}}</a><br/>
<br/>
<a href="{{url()->current()}}/{{$device_code}}" target="_blank">After verification click this link</a><br/>
