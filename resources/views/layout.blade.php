<!DOCTYPE html>
<html>
<head>
    <title>GeoTweet - A project by Duc Nguyen</title>
    <link rel="stylesheet" href="<?=asset('/css/app.css') ?>" />
</head>
<body>
@yield('content')
<script type="text/javascript" src="<?=asset('/js/vendor.js')?>"></script>
<script type="text/javascript" src="<?=asset('/js/main.js')?>"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAI3xUmiLOCDMjrRSUAFf23G9D7lOVHqmA&callback=initMap">
</script>
</body>
</html>
