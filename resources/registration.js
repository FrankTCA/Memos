$(document).ready(function() {
    $('#status').hide();
    $('#success').hide();
    $('#unwarning').hide();
    $('#pwarning').hide();
    $('#cpwarning').hide();

    $('#registrationSubmitBtn').click(function () {
        var username = $('#un').val();
        var pass = $('#pw').val();
        var cpass = $('#cpw').val();
        if (pass !== cpass) {
            $("#status").show();
            $("#status").text("Password does not equal confirmation!");
            return;
        }
        $.post("php/action_register.php", {
            un: username,
            pw: pass
        }, function(data, status) {
            if (data.endsWith("success")) {
                $("#register").hide();
                $("#success").show();
            } else {
                $("#status").show();
                $("#status").html(data);
            }
        });
    });
});

function checkPassword(inputtxt) {
    var decimal=  /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,15}$/;
    if (inputtxt.value.match(decimal)) {
        return true;
    }
    console.log("Password failed validation check!");
    return false;
}

var mostCommonPasswordsList = "";

function checkPasswordWithCommonPasswords(inputpass) {
    if (!mostCommonPasswordsList.startsWith("123456")) {
        $.get('resources/most-common-passwords.txt', function(data, status) {
            mostCommonPasswordsList = data;
        })
    }
    console.log(mostCommonPasswordsList);
    if (!mostCommonPasswordsList.startsWith("123456")) {
        console.log("Impossible!");
        return false;
    }
    var thePasswords = mostCommonPasswordsList.split('\n');
    for (var i = 0; i < thePasswords.length; i++) {
        if (inputpass == thePasswords[i]) {
            console.log("Password is on no-no list!");
            return false;
        }
    }
    return true;
}