var message_counter = {

    get_message_count: function(path, user_id) {
        var self = this;
        $.ajax({
            type: "GET",
            url: path,
            dataType: 'json',
            data: {'user_id': user_id},
            success: function (data) {
                var user_chats = new Array();
                $.each(data, function(i, item) {
                    user_chats.push(JSON.stringify(item));
                });

                if (localStorage.getItem('chat_counter_' + user_id) == null) {
                    var result = user_chats;

                    if (result.length > 0) {
                        for (var i = 0; i<result.length; i++) {
                            $('*[data-chat-id=' + JSON.parse(result[i]).chat_id + ']')
                                .attr('data-difference-count', JSON.parse(result[i]).count)
                                .html("<p>" + JSON.parse(result[i]).count + "</p>")
                                .css('visibility', 'visible');
                        }
                    }

                } else {
                    self.set_count_difference(localStorage.getItem('chat_counter_' + user_id), user_chats.join(', '));
                }
            },
            error: function () {
                console.log('error');
            }
        })
    },

    update_message_count: function(path, user_id) {
        var self = this;
        setInterval(function(){
        self.get_message_count(path, user_id);
        }, 10000)
    },

    set_count_difference: function (current_counter, update_counter) {
        var old_counter = current_counter.split(', ');
        var new_counter = update_counter.split(', ');
        var result = new Array();
        $.each(new_counter, function (k, val) {
            var coincidence_count = 0;
            $.each(old_counter, function (i, item) {

                if (JSON.parse(item).chat_id == JSON.parse(val).chat_id) {
                    coincidence_count ++;
                }

                if (JSON.parse(item).chat_id == JSON.parse(val).chat_id &&
                    parseInt(JSON.parse(item).count) != parseInt(JSON.parse(val).count)) {
                    result.push(
                        JSON.stringify(
                            {
                                chat_id: JSON.parse(item).chat_id,
                                difference: (
                                    parseInt(JSON.parse(val).count) - parseInt(JSON.parse(item).count)
                                )
                            }
                        )
                    )
                }
            })

            if (coincidence_count == 0) {
                result.push(
                    JSON.stringify(
                        {
                            chat_id: JSON.parse(val).chat_id,
                            difference: parseInt(JSON.parse(val).count)
                        }
                    )
                )
            }
        });


        if (result.length > 0) {
            for (var i = 0; i < result.length; i++) {
                $('*[data-chat-id=' + JSON.parse(result[i]).chat_id + ']')
                    .attr('data-difference-count', JSON.parse(result[i]).difference)
                    .html("<p>" + JSON.parse(result[i]).difference + "</p>")
                    .css('visibility', 'visible');
            }
        }

         return result;
    },

    update_storage: function (user_id, chat_id, path) {
        var final_counter = new Array();

        if (localStorage.getItem('chat_counter_' + user_id) != null) {
            var chat_counters = localStorage.getItem('chat_counter_' + user_id).split(', ');

            var update_counter = 0;
            $.each(chat_counters, function (i, item) {
                var current_chat = JSON.parse(item);

                if (current_chat.chat_id == chat_id) {
                    update_counter ++;

                    $.ajax({
                        type: "GET",
                        async: false,
                        url: path,
                        dataType: 'json',
                        data: {'user_id': user_id, 'chat_id': chat_id},
                        success: function (data) {
                            current_chat.count = data.count;
                            final_counter.push(JSON.stringify(current_chat));
                        },
                        error: function () {
                            console.log('error');
                        }
                    })
                } else {
                    final_counter.push(JSON.stringify(current_chat));
                }

            });

            if (update_counter == 0) {

                $.ajax({
                    type: "GET",
                    async: false,
                    url: path,
                    dataType: 'json',
                    data: {'user_id': user_id, 'chat_id': chat_id},
                    success: function (data) {
                        final_counter.push(JSON.stringify(
                            {
                                chat_id: data.chat_id,
                                count: data.count
                            }
                        ));
                    },
                    error: function () {
                        console.log('error');
                    }
                });

            }

            localStorage.setItem('chat_counter_' + user_id, final_counter.join(', '));

        } else {
            $.ajax({
                type: "GET",
                async: false,
                url: path,
                dataType: 'json',
                data: {'user_id': user_id, 'chat_id': chat_id},
                success: function (data) {
                    var storage_item = new Object();
                    storage_item.chat_id = data.chat_id;
                    storage_item.count = data.count;
                    final_counter.push(JSON.stringify(storage_item));

                    $('*[data-chat-id=' + storage_item.chat_id + ']')
                        .html("")
                        .css('visibility', 'hidden');

                },
                error: function () {
                    console.log('error');
                }
            });

            localStorage.setItem('chat_counter_' + user_id, final_counter.join(', '));
        }
    }
}
