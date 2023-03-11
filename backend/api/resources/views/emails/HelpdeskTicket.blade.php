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
        <p>An issue has been reported by <strong>{{ $mail_data['firstname'] }} {{ $mail_data['lastname'] }}</strong>. See details below.</p>
        <p>Reference No. <strong>{{ $mail_data[0]['ref_no'] }} </p>
        <p></p>
        <table style="font-family:Arial,sans-serif;width:600px;border-collapse:collapse;">
            @if($mail_data['Need_WP']=='Yes')
                <tr>
                    <td style="text-align:right; background-color:#b1b3d6"><strong>{{ ucfirst($mail_data['code_category']) }}</strong></td>
                    <td style="text-align:left; background-color:#cecee1">{{ $mail_data['code_desc'] }}</td>
                </tr>
                <tr>
                    <td style="text-align:right; background-color:#b1b3d6"><strong>URL</strong></td>
                    <td style="text-align:left; background-color:#cecee1">{{ $mail_data['selected_host'] }}</td>
                </tr>
                <tr>
                    <td style="text-align:right; background-color:#b1b3d6"><strong>Application IP</strong></td>
                    <td style="text-align:left; background-color:#cecee1">{{ $mail_data['selected_ip'] }}</td>
                </tr>
                    <tr>
                    <td style="text-align:right; background-color:#b1b3d6"><strong>Host IP</strong></td>
                    <td style="text-align:left; background-color:#cecee1">{{ $mail_data['host_ip_address'] }}</td>

                </tr>
                    <tr>
                    <td style="text-align:right; background-color:#b1b3d6"><strong>Subnet</strong></td>
                    <td style="text-align:left; background-color:#cecee1">{{ $mail_data['subnet'] }}</td>

                </tr>
                    <tr>
                    <td style="text-align:right; background-color:#b1b3d6"><strong>Gateway</strong></td>
                    <td style="text-align:left; background-color:#cecee1">{{ $mail_data['gateway'] }}</td>

                </tr>
                    <tr>
                    <td style="text-align:right; background-color:#b1b3d6"><strong>VLAN</strong></td>
                    <td style="text-align:left; background-color:#cecee1">401</td>
                </tr>
                    <tr>
                    <td style="text-align:right; background-color:#b1b3d6"><strong>DNS 1</strong></td>
                    <td style="text-align:left; background-color:#cecee1">{{ $mail_data['DNS_1'] }}</td>

                </tr>
                    <tr>
                    <td style="text-align:right; background-color:#b1b3d6"><strong>DNS 2</strong></td>
                    <td style="text-align:left; background-color:#cecee1">{{ $mail_data['DNS_2'] }}</td>

                </tr>
                    <tr>
                    <td style="text-align:right; background-color:#b1b3d6"><strong>Station #</strong></td>
                    <td style="text-align:left; background-color:#cecee1">{{ $mail_data['station_number'] }}</td>
                </tr>
            @else
                <tr>
                    <td style="text-align:right; background-color:#b1b3d6"><strong>{{  ucfirst($mail_data['code_category']) }}</strong></td>
                    <td style="text-align:left; background-color:#cecee1">{{ $mail_data['code_desc'] }}</td>
                </tr>
            @endif
        </table>
        <br>
        <p class="justify">Thanks,
            <br><br><strong>SentryCX™ Administrator</strong>
        </p>
        <p class="justify"><i><strong>Note:</strong> This is a system generated email. Please do not reply to this.</i></p>
    </body>
</html>
