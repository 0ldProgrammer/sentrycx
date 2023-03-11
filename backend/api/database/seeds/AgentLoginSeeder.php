<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;


class AgentLoginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $country_ref = ['US', 'PHILIPPINES', 'KOREA', 'INDIA', 'CHINA'];

        $location_ref = [
            'US'    =>  ["US-ALA-CLIEN-01","US-ALL-GMCLI-01","US-APP-AEROT-Y0","US-ARN-MEYER-Y0","US-AUB-EXECU-01","US-AUS-PARME-Y0"],
            'CHINA' =>  ["CN-BEI-YUMIN-00","CN-CHQ-ISEPK-01", "CN-DAL-SOFTW-01","CN-DAL-SOFTW-02"],
            'INDIA' =>  ["IN-AUR-GOLDE-01","IN-BAN-AXISB-01","IN-BAN-CLIEN-01","IN-BAN-DIVYA-Y0","IN-BAN-ECOCO-01"],
            'KOREA' =>  ['KR-SEO-CENTR-01','KR-SEO-CLIEN-01','KR-SEO-CLIEN-02','KR-SEO-GEUMB-01','KR-SEO-KUKJE-01'],
            'PHILIPPINES' => ["PH-CAG-TRADE-00","PH-CUB-SPARK-06","PH-CUB-SPARK-07","PH-DAV-ABREE-Y0","PH-DAV-ABREE-Y1"]
        ];

        date_default_timezone_set('Asia/Manila');
        $timestamp_submitted = date("Y-m-d H:i:s");

        $mtr_result = "<table class='table table-bordered'><tr><th>Hostname</th><th>NR</th><th>Loss %</th><th>Sent</th><th>Recieve</th><th>Best</th><th>Average</th><th>Worst</th><th>Last</th></tr><tr><td>2001:4455:1b5:1400:bec0:fff:fe7f:c940</td><td>1</td><td>0</td><td>10</td><td>10</td><td>3</td><td>4.4</td><td>8</td><td>3</td></tr><tr><td>2001:4450:10:302::1</td><td>2</td><td>0</td><td>10</td><td>10</td><td>9</td><td>12.1</td><td>19</td><td>13</td></tr><tr><td>2001:4450:10:303::</td><td>3</td><td>0</td><td>10</td><td>10</td><td>10</td><td>10.8</td><td>14</td><td>14</td></tr><tr><td>2001:4450:24:6000::7</td><td>4</td><td>0</td><td>10</td><td>10</td><td>28</td><td>30.3</td><td>37</td><td>31</td></tr><tr><td>2001:4450:10:6000::4</td><td>5</td><td>0</td><td>10</td><td>10</td><td>28</td><td>33.8</td><td>41</td><td>29</td></tr><tr><td>2001:4860:1:1::512</td><td>6</td><td>0</td><td>10</td><td>10</td><td>81</td><td>89</td><td>112</td><td>83</td></tr><tr><td>2404:6800:8042::1</td><td>7</td><td>90</td><td>10</td><td>1</td><td>107</td><td>107</td><td>107</td><td>107</td></tr><tr><td>2001:4860:0:1::812</td><td>8</td><td>0</td><td>10</td><td>10</td><td>79</td><td>82.1</td><td>93</td><td>82</td></tr><tr><td>Request timed out.</td><td>9</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr><td>2001:4860::c:4001:38a</td><td>10</td><td>0</td><td>10</td><td>10</td><td>80</td><td>82.6</td><td>94</td><td>80</td></tr><tr><td>2001:4860::c:4001:35d</td><td>11</td><td>0</td><td>10</td><td>10</td><td>82</td><td>90.1</td><td>127</td><td>85</td></tr><tr><td>2001:4860::c:4001:36fe</td><td>12</td><td>0</td><td>10</td><td>10</td><td>90</td><td>92.6</td><td>97</td><td>92</td></tr><tr><td>2001:4860::1:0:ca31</td><td>13</td><td>0</td><td>10</td><td>10</td><td>89</td><td>90</td><td>92</td><td>90</td></tr><tr><td>2001:4860:0:1::1d95</td><td>14</td><td>0</td><td>10</td><td>10</td><td>92</td><td>92.8</td><td>95</td><td>95</td></tr><tr><td>2404:6800:4005:807::200e</td><td>15</td><td>0</td><td>10</td><td>10</td><td>94</td><td>95.5</td><td>97</td><td>97</td></tr></table>";

        for($i = 0 ; $i < 1; $i++ ){
            $faker = Faker\Factory::create();
            $country = Arr::random( $country_ref );
            $username   = "{$faker -> firstName}.{$faker -> lastName}";
            $agent_name = "{$faker -> firstName} {$faker -> lastName}";
            $worker_id  = $faker -> randomNumber;
            $workstation_name = "CNX".$faker -> regexify('[^A-Za-z0-9]+');
            $dl_speed   = rand(10,30);
            $ul_speed   = rand(5,10);

            DB::table('agent_connections')->insert([
                'agent_name'            => $agent_name,
                'agent_email'           => "{$username}@concentrix.com",
                'created_at'            => $timestamp_submitted,
                'worker_id'             => $worker_id,
                'station_name'          => $workstation_name,
                'account'               => Arr::random(['Apple','Supportrix','Handy']),
                'location'              => Arr::random( $location_ref[$country]),
                'country'               => $country,
                'mtr_host'              => "google.com",
                'mtr_highest_avg'       => rand(0,20),
                'mtr_highest_loss'      => rand(0,10),
                'mtr_result'            => $mtr_result,
                'session_id'            => $faker -> regexify('[A-Z0-9._%+-]+@[A-Z0-9.-]+'),
                'is_active'             => TRUE,
            ]);


            DB::table('workstation_profile')->insert(
                [
                    'redflag_id' => 0,
                    'worker_id' => $worker_id,
                    'selected_host' => 'google.com',
                    'selected_ip'   => '216.58.220.206',
                    'host_name' => "google.com",
                    'host_ip_address' => $faker -> ipv4,
                    'subnet'  => "255.255.255.0",
                    'gateway' => "192.168.1.1",
                    'VLAN'    => "401",
                    'DNS_1'   => "10.5.11.5",
                    'DNS_2'   => "10.5.11.16",
                    'station_number' => $workstation_name,
                    'ping'      => "Ping to  [216.58.200.78] Success Response delay = 78 ms

                    Ping to  [216.58.200.78] Success Response delay = 78 ms

                    Ping to  [216.58.200.78] Success Response delay = 78 ms

                    Ping to  [216.58.200.78] Success Response delay = 78 ms

                    ",
                    'ping_ref'  => '',
                    'tracecert' => "1 | 10.52.121.252 | 4 ms
                            2 | 10.52.8.4 | 2 ms
                            210.213.138.222.static.pldt.net
                            210.213.143.253.static.pldt.net
                            210.213.134.130.static.pldt.net
                            210.213.134.150.static.pldt.net
                            7 | 74.125.118.24 | 45 ms
                            8 | 209.85.244.77 | 40 ms
                            9 | 108.170.226.115 | 49 ms
                            del01s08-in-f206.1e100.net
                     ",
                    'tracecert_ref' => '',
                    'host_file' => "
                        127.0.0.1	helpdesk-local.concentrix.com
                        127.0.0.1	project-one.com

                    ",
                    'ISP'       => "Philippine Long Distance Telephone Co.",
                    'download_speed' => "Download speed: $dl_speed Mbps",
                    'upload_speed'   => "Upload speed: $ul_speed Mbps",
                    'mtr'            => $mtr_result,
                    'network_adapter'       => "Realtek PCIe GBE Family Controller"
                ]
            );
            DB::table('hardware_info')->updateOrInsert([
                'worker_id'  => $worker_id,
            ],[
                'redflag_id' => 0,
                'worker_id'  => $worker_id,
                'gpu'        => "Name  -  NVIDIA GeForce RTX 2060 \r\n".
                    "Status  -  OK \r\n".
                    "Caption  -  NVIDIA GeForce RTX 2060 \r\n".
                    "DeviceID  -  VideoController1 \r\n".
                    "AdapterRAM  -  4293918720 \r\n".
                    "AdapterRAM  -  4.0 GB \r\n".
                    "AdapterDACType  -  Integrated RAMDAC \r\n".
                    "Monochrome  -  False \r\n".
                    "InstalledDisplayDrivers  -  C:\WINDOWS\System32\DriverStore\FileRepository\nvami.inf_amd64_c1be3fe4a5f7f580\nvldumdx.dll,C:\WINDOWS\System32\DriverStore\FileRepository\nvami.inf_amd64_c1be3fe4a5f7f580\nvldumdx.dll,C:\WINDOWS\"System32\DriverStore\FileRepository\nvami.inf_amd64_c1be3fe4a5f7f580\nvldumdx.dll,C:\WINDOWS\System32\DriverStore\FileRepository\nvami.inf_amd64_c1be3fe4a5f7f580\nvldumdx.dll \r\n".
                    "DriverVersion  -  27.21.14.5638 \r\n".
                    "VideoProcessor  -  GeForce RTX 2060 \r\n".
                    "VideoArchitecture  -  5 \r\n".
                    "VideoMemoryType  -  2 \r\n".
                    " \r\n".
                    "Name  -  AMD Radeon(TM) Graphics \r\n".
                    "Status  -  OK \r\n".
                    "Caption  -  AMD Radeon(TM) Graphics \r\n".
                    "DeviceID  -  VideoController2 \r\n".
                    "AdapterRAM  -  536870912 \r\n".
                    "AdapterRAM  -  512.0 MB \r\n".
                    "AdapterDACType  -  Internal DAC(400MHz) \r\n".
                    "Monochrome  -  False \r\n".
                    "InstalledDisplayDrivers  -  C:\WINDOWS\System32\DriverStore\FileRepository\u0354666.inf_amd64_492cadbdcc598f9a\B354599\aticfx64.dll,C:\WINDOWS\System32\DriverStore\FileRepository\u0354666.inf_amd64_492cadbdcc598f9a\B354599\aticfx64.dll,C:\WINDOWS\System32\DriverStore\FileRepository\u0354666.inf_amd64_492cadbdcc598f9a\B354599\aticfx64.dll,C:\WINDOWS\System32\DriverStore\FileRepository\u0354666.inf_amd64_492cadbdcc598f9a\B354599\amdxc64.dll \r\n".
                    "DriverVersion  -  26.20.14048.2 \r\n".
                    "VideoProcessor  -  AMD Radeon Graphics Processor (0x1636) \r\n".
                    "VideoArchitecture  -  5 \r\n".
                    "VideoMemoryType  -  2 \r\n",
                "disk_drive" => "(-Drive {0}, C:\) \r\n".
                    "(  Drive type: {0}, Fixed) \r\n".
                    "(  Volume label: {0}, ) \r\n".
                    "(  File system: {0}, NTFS) \r\n".
                    "(  Available space to current user:{0, 15}, 66.4 GB) \r\n".
                    "(  Total available space:          {0, 15}, 66.4 GB) \r\n".
                    "(  Total size of drive:            {0, 15} , 378.3 GB) \r\n".
                    "(  Root directory:            {0, 12}, C:\) \r\n".
                    " \r\n".
                    "(-Drive {0}, D:\) \r\n".
                    "(  Drive type: {0}, Fixed) \r\n".
                    "(  Volume label: {0}, New Volume) \r\n".
                    "(  File system: {0}, NTFS) \r\n".
                    "(  Available space to current user:{0, 15}, 25.0 GB) \r\n".
                    "(  Total available space:          {0, 15}, 25.0 GB) \r\n".
                    "(  Total size of drive:            {0, 15} , 97.7 GB) \r\n".
                    "(  Root directory:            {0, 12}, D:\) \r\n",
                "processor" => "Name  -  AMD Ryzen 7 4800H with Radeon Graphics          \r\n".
                    "DeviceID  -  CPU0 \r\n".
                    "Manufacturer  -  AuthenticAMD \r\n".
                    "CurrentClockSpeed  -  2900 \r\n".
                    "Caption  -  AMD64 Family 23 Model 96 Stepping 1 \r\n".
                    "NumberOfCores  -  8 \r\n".
                    "NumberOfEnabledCore  -  8 \r\n".
                    "NumberOfLogicalProcessors  -  16 \r\n".
                    "Architecture  -  9 \r\n".
                    "Family  -  107 \r\n".
                    "ProcessorType  -  3 \r\n".
                    "Characteristics  -  252 \r\n".
                    "AddressWidth  -  64 \r\n",
                "os" => "Caption  -  Microsoft Windows 10 Pro \r\n".
                    "WindowsDirectory  -  C:\WINDOWS \r\n".
                    "ProductType  -  1 \r\n".
                    "SerialNumber  -  00331-10000-00001-AA545 \r\n".
                    "SystemDirectory  -  C:\WINDOWS\system32 \r\n".
                    "CountryCode  -  1 \r\n".
                    "CurrentTimeZone  -  480 \r\n".
                    "EncryptionLevel  -  256 \r\n".
                    "OSType  -  18 \r\n".
                    "Version  -  10.0.19041 \r\n",
                "network_interface" => "(Interface information for {0}.{1}     , DESKTOP-S73AVUV, ) \r\n".
                    "(  Number of interfaces .................... : {0}, 7) \r\n".
                    "Fortinet SSL VPN Virtual Ethernet Adapter \r\n".
                    "========================================= \r\n".
                    "(  Interface type .......................... : {0}, Ethernet) \r\n".
                    "(  Physical Address ........................ : {0}, 00090FAA0001) \r\n".
                    "(  Operational status ...................... : {0}, Up) \r\n".
                    "(  IP version .............................. : {0}, IPv4 IPv6) \r\n".
                    "(  DNS suffix .............................. : {0}, ) \r\n".
                    "(  MTU...................................... : {0}, 1392) \r\n".
                    "(  DNS enabled ............................. : {0}, False) \r\n".
                    "(  Dynamically configured DNS .............. : {0}, True) \r\n".
                    "(  Receive Only ............................ : {0}, False) \r\n".
                    "(  Multicast ............................... : {0}, True) \r\n".
                    "Realtek PCIe GBE Family Controller \r\n".
                    "================================== \r\n".
                    "(  Interface type .......................... : {0}, Ethernet) \r\n".
                    "(  Physical Address ........................ : {0}, A85E453659A3) \r\n".
                    "(  Operational status ...................... : {0}, Down) \r\n".
                    "(  IP version .............................. : {0}, IPv4 IPv6) \r\n".
                    "(  DNS suffix .............................. : {0}, ) \r\n".
                    "(  MTU...................................... : {0}, 1500) \r\n".
                    "(  DNS enabled ............................. : {0}, False) \r\n".
                    "(  Dynamically configured DNS .............. : {0}, True) \r\n".
                    "(  Receive Only ............................ : {0}, False) \r\n".
                    "(  Multicast ............................... : {0}, True) \r\n".
                    "Microsoft Wi-Fi Direct Virtual Adapter \r\n".
                    "====================================== \r\n".
                    "(  Interface type .......................... : {0}, Wireless80211) \r\n".
                    "(  Physical Address ........................ : {0}, 7266555B5B95) \r\n".
                    "(  Operational status ...................... : {0}, Down) \r\n".
                    "(  IP version .............................. : {0}, IPv4 IPv6) \r\n".
                    "(  DNS suffix .............................. : {0}, ) \r\n".
                    "(  MTU...................................... : {0}, 1500) \r\n".
                    "(  DNS enabled ............................. : {0}, False) \r\n".
                    "(  Dynamically configured DNS .............. : {0}, True) \r\n".
                    "(  Receive Only ............................ : {0}, False) \r\n".
                    "(  Multicast ............................... : {0}, True) \r\n".
                    "Microsoft Wi-Fi Direct Virtual Adapter #2 \r\n".
                    "========================================= \r\n".
                    "(  Interface type .......................... : {0}, Wireless80211) \r\n".
                    "(  Physical Address ........................ : {0}, F266555B5B95) \r\n".
                    "(  Operational status ...................... : {0}, Down) \r\n".
                    "(  IP version .............................. : {0}, IPv4 IPv6) \r\n".
                    "(  DNS suffix .............................. : {0}, ) \r\n".
                    "(  MTU...................................... : {0}, 1500) \r\n".
                    "(  DNS enabled ............................. : {0}, False) \r\n".
                    "(  Dynamically configured DNS .............. : {0}, True) \r\n".
                    "(  Receive Only ............................ : {0}, False) \r\n".
                    "(  Multicast ............................... : {0}, True) \r\n".
                    "Fortinet Virtual Ethernet Adapter (NDIS 6.30) \r\n".
                    "============================================= \r\n".
                    "(  Interface type .......................... : {0}, Ethernet) \r\n".
                    "(  Physical Address ........................ : {0}, 00090FFE0001) \r\n".
                    "(  Operational status ...................... : {0}, Down) \r\n".
                    "(  IP version .............................. : {0}, IPv4 IPv6) \r\n".
                    "(  DNS suffix .............................. : {0}, ) \r\n".
                    "(  MTU...................................... : {0}, 1500) \r\n".
                    "(  DNS enabled ............................. : {0}, False) \r\n".
                    "(  Dynamically configured DNS .............. : {0}, True) \r\n".
                    "(  Receive Only ............................ : {0}, False) \r\n".
                    "(  Multicast ............................... : {0}, True) \r\n".
                    "Realtek 8822CE Wireless LAN 802.11ac PCI-E NIC \r\n".
                    "============================================== \r\n".
                    "(  Interface type .......................... : {0}, Wireless80211) \r\n".
                    "(  Physical Address ........................ : {0}, 7066555B5B95) \r\n".
                    "(  Operational status ...................... : {0}, Up) \r\n".
                    "(  IP version .............................. : {0}, IPv4 IPv6) \r\n".
                    "(  DNS suffix .............................. : {0}, ) \r\n".
                    "(  MTU...................................... : {0}, 1472) \r\n".
                    "(  DNS enabled ............................. : {0}, False) \r\n".
                    "(  Dynamically configured DNS .............. : {0}, True) \r\n".
                    "(  Receive Only ............................ : {0}, False) \r\n".
                    "(  Multicast ............................... : {0}, True) \r\n".
                    "Software Loopback Interface 1 \r\n".
                    "============================= \r\n".
                    "(  Interface type .......................... : {0}, Loopback) \r\n".
                    "(  Physical Address ........................ : {0}, ) \r\n".
                    "(  Operational status ...................... : {0}, Up) \r\n".
                    "(  IP version .............................. : {0}, IPv4 IPv6) \r\n",
                "sound_card" => "Name  -  NVIDIA High Definition Audio \r\n".
                    "ProductName  -  NVIDIA High Definition Audio \r\n".
                    "Availability  -   \r\n".
                    "DeviceID  -  HDAUDIO\FUNC_01&VEN_10DE&DEV_0093&SUBSYS_10431E21&REV_1001\5&386B8918&0&0001 \r\n".
                    "PowerManagementSupported  -  False \r\n".
                    "Status  -  OK \r\n".
                    "StatusInfo  -  3 \r\n".
                    "============================ \r\n".
                    " \r\n".
                    "Name  -  Realtek High Definition Audio \r\n".
                    "ProductName  -  Realtek High Definition Audio \r\n".
                    "Availability  -   \r\n".
                    "DeviceID  -  HDAUDIO\FUNC_01&VEN_10EC&DEV_0256&SUBSYS_10431E21&REV_1000\5&11F16BAA&0&0001 \r\n".
                    "PowerManagementSupported  -  False \r\n".
                    "Status  -  OK \r\n".
                    "StatusInfo  -  3 \r\n".
                    "============================= \r\n".
                    " \r\n".
                    "Name  -  NVIDIA Virtual Audio Device (Wave Extensible) (WDM) \r\n".
                    "ProductName  -  NVIDIA Virtual Audio Device (Wave Extensible) (WDM) \r\n".
                    "Availability  -   \r\n".
                    "DeviceID  -  ROOT\UNNAMED_DEVICE\0000 \r\n".
                    "PowerManagementSupported  -  False \r\n".
                    "Status  -  OK \r\n".
                    "StatusInfo  -  3 \r\n".
                    "=================================================== \r\n".
                    " \r\n".
                    "Name  -  AMD High Definition Audio Device \r\n".
                    "ProductName  -  AMD High Definition Audio Device \r\n".
                    "Availability  -   \r\n".
                    "DeviceID  -  HDAUDIO\FUNC_01&VEN_1002&DEV_AA01&SUBSYS_00AA0100&REV_1007\5&28A118E&0&0001 \r\n".
                    "PowerManagementSupported  -  False \r\n".
                    "Status  -  OK \r\n".
                    "StatusInfo  -  3 \r\n".
                    "================================ \r\n",
                "printer" => "Name  -  Send To OneNote 2016 \r\n".
                    "Network  -  False \r\n".
                    "Availability  -   \r\n".
                    "Is default printer  -  False \r\n".
                    "DeviceID  -  Send To OneNote 2016 \r\n".
                    "Status  -  Unknown \r\n".
                    "==================== \r\n".
                    " \r\n".
                    "Name  -  OneNote for Windows 10 \r\n".
                    "Network  -  False \r\n".
                    "Availability  -   \r\n".
                    "Is default printer  -  False \r\n".
                    "DeviceID  -  OneNote for Windows 10 \r\n".
                    "Status  -  Unknown \r\n".
                    "====================== \r\n".
                    " \r\n".
                    "Name  -  Microsoft XPS Document Writer \r\n".
                    "Network  -  False \r\n".
                    "Availability  -   \r\n".
                    "Is default printer  -  False \r\n".
                    "DeviceID  -  Microsoft XPS Document Writer \r\n".
                    "Status  -  Unknown \r\n".
                    "============================= \r\n".
                    " \r\n".
                    "Name  -  Microsoft Print to PDF \r\n".
                    "Network  -  False \r\n".
                    "Availability  -   \r\n".
                    "Is default printer  -  True \r\n".
                    "DeviceID  -  Microsoft Print to PDF \r\n".
                    "Status  -  Unknown \r\n".
                    "====================== \r\n".
                    " \r\n".
                    "Name  -  Fax \r\n".
                    "Network  -  False \r\n".
                    "Availability  -   \r\n".
                    "Is default printer  -  False \r\n".
                    "DeviceID  -  Fax \r\n".
                    "Status  -  Unknown \r\n".
                    "=== \r\n",
                "installed_apps" => "7-Zip 19.00 (x64) \r\n".
                    "Vuze \r\n".
                    "Beyond Compare 4.3.4 \r\n".
                    "CCleaner \r\n".
                    "CPUID CPU-Z 1.92 \r\n".
                    "Firefox Developer Edition 82.0 (x64 en-US) \r\n".
                    "Git version 2.27.0 \r\n".
                    "Microsoft Azure Compute Emulator - v2.9.6 \r\n".
                    "Mozilla Maintenance Service \r\n".
                    "Notepad++ (64-bit x64) \r\n".
                    "Microsoft Office 365 ProPlus - en-us \r\n".
                    "Riot Vanguard \r\n".
                    "Screen+ version Screen+ 1.4.2 \r\n".
                    "SQLyog 13.1.1 (64 bit) \r\n".
                    "Dota Underlords \r\n".
                    "NBA 2K20 \r\n".
                    "Among Us \r\n".
                    "Sublime Text 3 \r\n".
                    "Symfony version 1.1.5 \r\n".
                    "VLC media player \r\n".
                    "XAMPP \r\n".
                    "ARMOURY CRATE Service \r\n".
                    "ASUS Aac_NBDT HAL \r\n".
                    "Microsoft .NET Core 2.1 Templates 3.1.300 (x64) \r\n".
                    "IIS Express Application Compatibility Database for x64 \r\n".
                    "Microsoft ASP.NET Core 3.1.3 Targeting Pack (x64) \r\n".
                    "ASUS Keyboard HAL \r\n".
                    "Application Verifier x64 External Package \r\n".
                    "Microsoft Azure Compute Emulator - v2.9.6 \r\n".
                    "ASUS Mouse HAL \r\n".
                    "IntelliTraceProfilerProxy \r\n".
                    "Microsoft Visual C++ 2010  x64 Redistributable - 10.0.40219 \r\n".
                    "DiagnosticsHub_CollectionService \r\n".
                    "Windows App Certification Kit Native Components \r\n".
                    "IIS 10.0 Express \r\n".
                    "Microsoft .NET Core Targeting Pack - 3.1.0 (x64) \r\n".
                    "Microsoft Visual C++ 2012 x64 Additional Runtime - 11.0.61030 \r\n".
                    "Microsoft Command Line Utilities 15 for SQL Server \r\n".
                    "FortiClient VPN \r\n".
                    "Microsoft .NET Core 3.1 Templates 3.1.301 (x64) \r\n".
                    "Microsoft .NET Core SDK 3.1.301 (x64) from Visual Studio \r\n".
                    "SSMS Post Install Tasks \r\n".
                    "Universal CRT Tools x64 \r\n".
                    "Microsoft .NET Core Runtime - 3.1.5 (x64) \r\n".
                    "SQL Server Management Studio for Reporting Services \r\n".
                    "Microsoft Visual C++ 2008 Redistributable - x64 9.0.30729.6161 \r\n".
                    "Microsoft .NET Core AppHost Pack - 3.1.5 (x64_x86) \r\n".
                    "Active Directory Authentication Library for SQL Server \r\n".
                    "Node.js \r\n".
                    "Windows SDK DirectX x64 Remote \r\n".
                    "Microsoft .NET Core Runtime - 2.1.19 (x64) \r\n".
                    "Microsoft .NET Core Host FX Resolver - 3.1.5 (x64) \r\n".
                    "Microsoft Visual Studio Installer \r\n".
                    "SQL Server Management Studio for Analysis Services \r\n".
                    "Microsoft OLE DB Driver for SQL Server \r\n".
                    "Microsoft Windows Desktop Targeting Pack - 3.1.0 (x64) \r\n".
                    "Microsoft .NET Core AppHost Pack - 3.1.5 (x64_arm64) \r\n".
                    "Microsoft ASP.NET Core 2.1.19 Shared Framework (x64) \r\n".
                    "Microsoft .NET Core AppHost Pack - 3.1.5 (x64_arm) \r\n".
                    "Microsoft Visual C++ 2008 Redistributable - x64 9.0.30729.17 \r\n".
                    "SQL Server Management Studio \r\n".
                    "SQL Server Management Studio \r\n".
                    "NetSpeedMonitor 2.5.4.0 x64 \r\n".
                    "Microsoft System CLR Types for SQL Server 2019 CTP2.2 \r\n".
                    "Office 16 Click-to-Run Licensing Component \r\n".
                    "Office 16 Click-to-Run Extensibility Component \r\n".
                    "Office 16 Click-to-Run Localization Component \r\n".
                    "Microsoft .NET Core Host - 3.1.5 (x64) \r\n".
                    "Microsoft SQL Server 2016 LocalDB  \r\n".
                    "Microsoft Visual C++ 2013 x64 Additional Runtime - 12.0.21005 \r\n".
                    "Microsoft SQL Server 2012 Native Client  \r\n".
                    "ASUS AURA Headset Component \r\n".
                    "VS Script Debugging Common \r\n".
                    "Microsoft .NET Standard Targeting Pack - 2.1.0 (x64) \r\n".
                    "Microsoft Visual C++ 2013 x64 Minimum Runtime - 12.0.21005 \r\n".
                    "Microsoft Windows Desktop Runtime - 3.1.5 (x64) \r\n".
                    "IIS Express Application Compatibility Database for x86 \r\n".
                    "ASUS AURA Display Component \r\n".
                    "Microsoft Visual Studio Tools for Applications 2017 x64 Hosting Support \r\n".
                    "NVIDIA Graphics Driver 456.38 \r\n".
                    "NVIDIA GeForce Experience 3.20.4.14 \r\n".
                    "NVIDIA Optimus Update 38.0.5.0 \r\n".
                    "NVIDIA PhysX System Software 9.19.0218 \r\n".
                    "NVIDIA Update 38.0.5.0 \r\n".
                    "NVIDIA SHIELD Streaming \r\n".
                    "NVIDIA HD Audio Driver 1.3.38.35 \r\n".
                    "NVIDIA Install Application \r\n".
                    "NVIDIA ABHub \r\n".
                    "NVIDIA Backend \r\n".
                    "NVIDIA Container \r\n".
                    "NVIDIA TelemetryApi helper for NvContainer \r\n".
                    "NVIDIA LocalSystem Container \r\n".
                    "NVIDIA Message Bus for NvContainer \r\n".
                    "NVAPI Monitor plugin for NvContainer \r\n".
                    "NVIDIA NetworkService Container \r\n".
                    "NVIDIA Session Container \r\n".
                    "NVIDIA User Container \r\n".
                    "NvModuleTracker \r\n".
                    "NVIDIA NodeJS \r\n".
                    "NVIDIA Watchdog Plugin for NvContainer \r\n".
                    "NVIDIA Telemetry Client \r\n".
                    "NVIDIA Virtual Host Controller \r\n".
                    "Nvidia Share \r\n".
                    "NVIDIA ShadowPlay 3.20.4.14 \r\n".
                    "NVIDIA SHIELD Wireless Controller Driver \r\n".
                    "NVIDIA Update Core \r\n".
                    "NVIDIA USBC Driver 1.45.831.832 \r\n".
                    "NVIDIA Virtual Audio 4.13.0.0 \r\n".
                    "Redis version 2.4.6.0 \r\n".
                    "Microsoft Web Deploy 4.0 \r\n".
                    "Microsoft .NET Core Toolset 3.1.301 (x64) \r\n".
                    "icecap_collection_x64 \r\n".
                    "ASUS MB Peripheral Products \r\n".
                    "Microsoft Analysis Services OLE DB Provider \r\n".
                    "AURA lighting effect add-on x64 \r\n".
                    "Microsoft Azure Libraries for .NET – v2.9 \r\n".
                    "VS JIT Debugger \r\n".
                    "Microsoft Visual C++ 2019 X64 Additional Runtime - 14.26.28720 \r\n".
                    "Microsoft Visual C++ 2012 x64 Minimum Runtime - 11.0.61030 \r\n".
                    "ASUS Aura SDK \r\n".
                    "Samsung USB Driver for Mobile Phones \r\n".
                    "PowerToys (Preview) \r\n".
                    "Microsoft ASP.NET Core 3.1.5 Shared Framework (x64) \r\n".
                    "Microsoft .NET Core AppHost Pack - 3.1.5 (x64) \r\n".
                    "Microsoft Visual C++ 2019 X64 Minimum Runtime - 14.26.28720 \r\n".
                    "Microsoft ODBC Driver 17 for SQL Server \r\n".
                    "Microsoft Azure Authoring Tools - v2.9.6 \r\n".
                    "Epic Games Launcher Prerequisites (x64) \r\n".
                    "Microsoft ASP.NET Core Module for IIS Express \r\n".
                    "Microsoft ASP.NET Core Module V2 for IIS Express \r\n",
            ]);

        }
    }
}
