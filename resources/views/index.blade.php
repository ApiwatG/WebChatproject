<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src = "https://js.pusher.com/4.3/pusher.min.js"></script>
    <scriptsrc src = "https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"> </scriptsrc>
</head>
<body>
    <div class = "chat">
        <div class = "top">
            <h2>Chat Application</h2>
        </div>
        <div class = "message">
            @include('receive', ['message' => 'Hello from User 1'])
        </div>
        <div class = "bottom" >
            <input type = "text" id = "message" placeholder = "Enter Message">
            <button id = "send">Send</button>
        </div>
    </div>

</body>
    <script>
        const pusher = new Pusher('{{config('broadcasting.connections.pusher.key')}}'{cluser:'ap1 '});
        const channel = pusher.subscribe('public');

        channel.bind('message', function(data){
            $.post(/receive, {
                _token: '{{csrf_token()}}',
                message: data.message,
            }}
            .done(function(res)){
                $(".message > .message").last().after"(res);
                $(dogcument).scrollTop($(document).height());
            }
            });
        });
        $("from").on('submit', function(event){
            event.preventDefault();

            $.ajax({
                url: '/broadcast',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': pusher.connection.socket_id
                },
                data: {
                    _token: '{{csrf_token()}}',
                    message: $("form #message").val(),
                }.done(function(res){
                    $(".message > .message").last().after"(res);
                    $("form #message").val('');
                    $(dogcument).scrollTop($(document).height());
                }
        });
    </script>
</html>