function validURL(str) {
    const pattern = new RegExp('^(https?:\\/\\/)?'+
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+
        '((\\d{1,3}\\.){3}\\d{1,3}))'+
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+
        '(\\?[;&a-z\\d%_.~+=-]*)?'+
        '(\\#[-a-z\\d_]*)?$','i');
    return !!pattern.test(str);
}

$(document).ready(function () {
    $('form button').click(function () {
        const input = $('form #linkAddress');
        const host = $('form #hostName').val();
        const inputValue = input.val();
        const errorElement = $('form small');
        const responseElement = $('#response');

        if (!validURL(inputValue)) {
            if (errorElement.hasClass('d-none')) errorElement.removeClass('d-none');
            if (!errorElement.hasClass('d-block')) errorElement.addClass('d-block');
        } else {
            if (errorElement.hasClass('d-block')) errorElement.removeClass('d-block');
            if (!errorElement.hasClass('d-none')) errorElement.addClass('d-none');

            input.val(null);

            $.post(host + 'add', '{"url":"' + inputValue + '"}', function(response) {
                switch (response['code']) {
                    case 200:
                        responseElement.html('Link: <a class="text-light" href="' + response['data'] + '" target="_blank">' + response['data'] + '<a/>').removeClass('d-none');
                        break;
                    default:
                        responseElement.addClass('bg-danger ').text('Something goes wrong! Try again.').removeClass('d-none bg-success');
                }
            });
        }
    })
})