
function aaqSendAnswer(question_id, n_answer, count_answers, cookie = 0, as_percent = 0){
    jQuery.ajax({
        type: "POST",
        url: wp_ajax_url,
        data: {
            question: question_id,
            action: 'question_set_answer',
            value: n_answer,
            count_answers: count_answers,
            cookie: cookie
        }, // serializes the form's elements.
        success: function(data){

            aaqDisableInputs(question_id)
            aaqRefreshResults(question_id, as_percent)

        }
    });
}

function aaqDisableInputs(question_id){
    jQuery("#" + question_id + " input").attr('disabled', 'disabled')
}

function aaqRefreshResults(question_id, as_percent = false){
    jQuery.ajax({
        type: "POST",
        url: wp_ajax_url,
        data: {
            question: question_id,
            action: 'question_answers'
        }, // serializes the form's elements.
        success: function(data)
        {
            data = JSON.parse(data)
            // console.log(data)

            var sum = 0;
            for(var i = 0; i < data.length; i++){
                sum = sum + data[i]
            }

            // Reset data
            jQuery("#" + question_id + "-progressbar-" + i).css("width", "0%")
            jQuery("#" + question_id + "-progresspercentage-" + i).html(0)

            for(var i = 0; i < data.length; i++){
                // console.log(data[i])

                let percentage = 0

                if( sum > 0 ){
                    percentage = data[i] / sum * 100.0
                }

                jQuery("#" + question_id + "-progressbar-" + i).css("width", percentage  +"%")
                if( as_percent ){
                    jQuery("#" + question_id + "-progresspercentage-" + i).html( parseInt(percentage) + "%")
                }else{
                    jQuery("#" + question_id + "-progresspercentage-" + i).html(data[i])
                }
            }
        }
    });
}
