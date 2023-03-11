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
        <p>Hi {{ $firstname }},</p>
        <p>Please see attached file generated for <b>{{ $list_of_accounts }}</b> account(s) on <b>{{ $current_date }}</b> for the SentryCX Daily Report on the following: </p>
        <ul>
            <li><b>Speedtest</b> - below threshold for upload speed and download speed for the last 7 days
                <ul type="circle">
                    @if(empty($upload_speed_threshold))
                    @else
                        <li>Upload Speed threshold - 
                            @foreach ($upload_speed_threshold as $val)
                                <span>{{ $val->account_name }}: {{ $val->threshold }} Mbps</span>
                                @if(!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        </li>
                    @endif
                    @if(empty($download_speed_threshold))
                    @else
                        <li>Download Speed threshold - 
                            @foreach ($download_speed_threshold as $val)
                                <span>{{ $val->account_name }}: {{ $val->threshold }} Mbps</span>
                                @if(!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        </li>
                    @endif
                </ul>
            </li>
            <li><b>Application URLs</b> - above ping threshold for the last 7 days
                <ul type="circle">
                    @if(empty($ping_threshold))
                    @else
                        <li>Ping Rate threshold - 
                            @foreach ($ping_threshold as $val)
                                <span>{{ $val->account_name }}: {{ $val->threshold }} ms</span>
                                    @if(!$loop->last)
                                        ,
                                    @endif
                            @endforeach
                        </li>
                    @endif
                </ul>
            </li>
            <li><b>Utilization</b> - above threshold for CPU, Disk and RAM
                <ul type="circle">
                    @if(empty($cpu_threshold))
                    @else
                        <li>CPU threshold - 
                            @foreach ($cpu_threshold as $val)
                                <span>{{ $val->account_name }}: {{ $val->threshold }} %</span>
                                @if(!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        </li>
                    @endif
                    @if(empty($disk_threshold))
                    @else
                        <li>Disk threshold - 
                            @foreach ($disk_threshold as $val)
                                <span>{{ $val->account_name }}: {{ $val->threshold }} GB</span>
                                @if(!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        </li>
                    @endif
                    @if(empty($ram_threshold))
                    @else
                        <li>RAM threshold - 
                            @foreach ($ram_threshold as $val)
                                <span>{{ $val->account_name }}: {{ $val->threshold }} GB</span>
                                @if(!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        </li>
                    @endif
                </ul>
            </li>
            <li><b>Offline Workstations</b> - not connected for 3 days or more</li>
            <li><b>Required and Restricted Applications</b> - workstations non-compliant to permitted applications</li>
        </ul>
        <p>You are receiving this email because you have been registered to the SentryCX Portal. If you want to choose what reports you want to receive, <a href="{{ $link }}">click here</a> or if you want to extract specific report, <a href="{{ $sharepoint_link }}">click here</a>.</p>
        <p class="justify">Thanks,
            <br><br><strong>SentryCX™</strong>
        </p>
        <hr>
        <p>For any questions or concerns, feel free to send an email to <a href="mailto:SentryCX@concentrix.com">SentryCX@concentrix.com</a>.</p>
        <p class="justify" style="color:#808080"><i><strong>Note:</strong> This mail is system-generated and is not monitored. Please do not reply.</i></p>
    </body>
</html>
