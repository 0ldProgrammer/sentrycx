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
        <p>User {{ $mail_data['username'] }} has logged into device as local Administrator.</p>

        <p>Check details below:</p>
        <p>Date / Time : {{ $mail_data['date_time'] }}</p>
        <p>Workstation ID : {{ $mail_data['station_number'] }}</p>
        <p>IP Address : {{ $mail_data['ip_address']}}</p>
        <p>Worker ID : {{$mail_data['employee_id']}}</p>


        If this activity is not authorized, please contact julius.esclamado@concentrix.com
        <br>
        <p class="justify">Thanks,
            <br><br><strong>SentryCX™ Administrator</strong>
        </p>
        <p class="justify"><i><strong>Note:</strong> This is a system generated email. Please do not reply to this.</i></p>
    </body>
</html>
