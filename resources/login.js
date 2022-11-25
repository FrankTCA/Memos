$(document).ready(function() {
    $('#status').hide();

    $("#loginSubmitBtn").click(function() {
        var username = $("#un").val();
        var password = $("#pw").val();

        if (username === "" || password === "") {
            return;
        }
        $.post("php/action_login.php", {
            un: username,
            pw: password
        }, function (data, status) {
            if (data.endsWith("success")) {
                window.location.href = "dash.php";
            } else {
                $("#status").show();
                $("#status").html(data);
            }
        });
    });
});

function onSubmit() {
    console.log("Submit button clicked!");
}