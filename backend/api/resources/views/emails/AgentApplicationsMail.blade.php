<!DOCTYPE html>
<html>
    <head>
        <style>
            table {
                font-family: arial, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }

            td, th {
                border: 1px solid #dddddd;
                text-align: left;
                padding: 8px;
            }

            tr:nth-child(even) {
                background-color: #dddddd;
            }
        </style>
    </head>
    <body>
        <p>Hi Team,</p>
        <br>

        <p>Please Check information below for <b>{{ $data['firstname'] }}</b> <b>{{ $data['lastname'] }}</b>:</p>
        <br>
        <p>Required Applications : <b>{{ $data['required_apps'] }}</b></p>
        <p>Restricted Applications : <b>{{ $data['restricted_apps'] }}</b></p>
        <p>Account : <b>{{ $data['account'] }}</b></p>
        <p>Location : <b>{{ $data['app_location'] }}</b></p>

        <br>
        <p class="justify">Thanks,
            <br><br><strong>SentryCX™ Administrator</strong>
        </p>
        <p class="justify"><i><strong>Note:</strong> This is a system generated email. Please do not reply to this.</i></p>
    </body>
</html>
