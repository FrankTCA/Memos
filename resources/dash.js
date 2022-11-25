$(document).ready(function() {
    $('.invalidWarning').hide();
    $('#status').hide();

    $('#frmSubmitBtn').click(function() {
        let name = $('#frmNameBox').val();
        let title = $('#frmTitleBox').val();
        let url = $('#frmUrlBox').val();

        var data;

        if (name.length > 10) {
            $('#frmNameWarning').show();
            return;
        }

        if (title.length > 60) {
            $("#frmTitleWarning").show();
            return;
        }

        console.log(url);

        if (/https:\/\/gist\.github\.com\/[A-Za-z]+\/[A-Za-z0-9]+/.test(url)) {
            $("#frmUrlWarning").show();
            return;
        }

        data = {
            name: name,
            title: title,
            url: url
        }

        console.log(data);
        console.log(url);

        $.post("php/action_mkmemo.php", data, function(data, status) {
            if (data.endsWith("success")) {
                prompt("Memo created successfully and is available at https://infotoast.org/memos/" + name);
            } else {
                $('#status').show();
                $('#status').html(data);
            }
        });
    });
});