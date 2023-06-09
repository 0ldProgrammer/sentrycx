<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('accounts')->insert(
            ["id" => "1", "name" =>  "Acer"],
            ["id" => "2", "name" =>  "Acnpacificltd"],
            ["id" => "3", "name" =>  "Activant"],
            ["id" => "4", "name" =>  "Aetna"],
            ["id" => "5", "name" =>  "Amazon"],
            ["id" => "6", "name" =>  "Amexexpress"],
            ["id" => "7", "name" =>  "Anthem"],
            ["id" => "8", "name" =>  "Aptosinc"],
            ["id" => "9", "name" =>  "Arlo"],
            ["id" => "10", "name" =>  "Att"],
            ["id" => "11", "name" =>  "Avalon"],
            ["id" => "12", "name" =>  "Avaya"],
            ["id" => "13", "name" =>  "Bankofphilippin"],
            ["id" => "14", "name" =>  "Bcbs Nc"],
            ["id" => "15", "name" =>  "Bed Bath And Beyond"],
            ["id" => "16", "name" =>  "Belkin"],
            ["id" => "17", "name" =>  "Benefitfocus"],
            ["id" => "18", "name" =>  "Bookingcom"],
            ["id" => "19", "name" =>  "Bosch"],
            ["id" => "20", "name" =>  "Bosley"],
            ["id" => "21", "name" =>  "Brocade"],
            ["id" => "22", "name" =>  "Canoninc"],
            ["id" => "23", "name" =>  "Canva"],
            ["id" => "24", "name" =>  "Capitalone"],
            ["id" => "25", "name" =>  "Catalina"],
            ["id" => "26", "name" =>  "Cdw"],
            ["id" => "27", "name" =>  "Ceaa"],
            ["id" => "28", "name" =>  "Cemex"],
            ["id" => "29", "name" =>  "Centurylink"],
            ["id" => "30", "name" =>  "Charter"],
            ["id" => "31", "name" =>  "Cigna"],
            ["id" => "32", "name" =>  "Cis"],
            ["id" => "33", "name" =>  "Cisco"],
            ["id" => "34", "name" =>  "Citibank"],
            ["id" => "35", "name" =>  "Cnx University"],
            ["id" => "36", "name" =>  "Colonialpenn"],
            ["id" => "37", "name" =>  "Comcast Corp"],
            ["id" => "38", "name" =>  "Concentrix"],
            ["id" => "39", "name" =>  "Dell"],
            ["id" => "40", "name" =>  "Dish"],
            ["id" => "41", "name" =>  "Dji"],
            ["id" => "42", "name" =>  "Dreamlines"],
            ["id" => "43", "name" =>  "Dunbradstreet"],
            ["id" => "44", "name" =>  "Ebay"],
            ["id" => "45", "name" =>  "Endurance"],
            ["id" => "46", "name" =>  "Enterprise"],
            ["id" => "47", "name" =>  "Everquote"],
            ["id" => "48", "name" =>  "Express Scripts"],
            ["id" => "49", "name" =>  "Federal Express"],
            ["id" => "50", "name" =>  "Foxsymes"],
            ["id" => "51", "name" =>  "General Motors"],
            ["id" => "52", "name" =>  "Genex Services"],
            ["id" => "53", "name" =>  "Getaroom"],
            ["id" => "54", "name" =>  "Globetelecom"],
            ["id" => "55", "name" =>  "Gojek"],
            ["id" => "56", "name" =>  "Google"],
            ["id" => "57", "name" =>  "Gopro"],
            ["id" => "58", "name" =>  "Grabtaxi"],
            ["id" => "59", "name" =>  "H&R Block"],
            ["id" => "60", "name" =>  "Handy"],
            ["id" => "61", "name" =>  "Healthfirst"],
            ["id" => "62", "name" =>  "Hennes & Mauritz Gbc Ab"],
            ["id" => "63", "name" =>  "Hii"],
            ["id" => "64", "name" =>  "Hpinc"],
            ["id" => "65", "name" =>  "Htc"],
            ["id" => "66", "name" =>  "Hyatt"],
            ["id" => "67", "name" =>  "Ibmcommer"],
            ["id" => "68", "name" =>  "Imvu"],
            ["id" => "69", "name" =>  "Inspirato"],
            ["id" => "70", "name" =>  "Intuit"],
            ["id" => "71", "name" =>  "Ironmountain"],
            ["id" => "72", "name" =>  "Keep Truckin"],
            ["id" => "73", "name" =>  "Keybank"],
            ["id" => "74", "name" =>  "Klook"],
            ["id" => "75", "name" =>  "Lazada"],
            ["id" => "76", "name" =>  "Liberty Global"],
            ["id" => "77", "name" =>  "Lineplus"],
            ["id" => "78", "name" =>  "Lloyds"],
            ["id" => "79", "name" =>  "Lorex"],
            ["id" => "80", "name" =>  "Luxottica"],
            ["id" => "81", "name" =>  "Macys"],
            ["id" => "82", "name" =>  "Mastercard"],
            ["id" => "83", "name" =>  "Mediacom"],
            ["id" => "84", "name" =>  "Medimpact"],
            ["id" => "85", "name" =>  "Microsoft"],
            ["id" => "86", "name" =>  "Mitac"],
            ["id" => "87", "name" =>  "Mondelez"],
            ["id" => "88", "name" =>  "Netgear"],
            ["id" => "89", "name" =>  "Nike"],
            ["id" => "90", "name" =>  "Oceanx"],
            ["id" => "91", "name" =>  "Oculus Vr"],
            ["id" => "92", "name" =>  "Omahasteak"],
            ["id" => "93", "name" =>  "Oneplus"],
            ["id" => "94", "name" =>  "Opexother"],
            ["id" => "95", "name" =>  "Optus"],
            ["id" => "96", "name" =>  "Orcon"],
            ["id" => "97", "name" =>  "Originenergy"],
            ["id" => "98", "name" =>  "Paciolan"],
            ["id" => "99", "name" =>  "Paypal"],
            ["id" => "100", "name" =>  "Pearson"],
            ["id" => "101", "name" =>  "Pomeroy"],
            ["id" => "102", "name" =>  "Premier Connect"],
            ["id" => "103", "name" =>  "Rakuten"],
            ["id" => "104", "name" =>  "Razer"],
            ["id" => "105", "name" =>  "Razor"],
            ["id" => "106", "name" =>  "Realdefense"],
            ["id" => "107", "name" =>  "Receiptbank"],
            ["id" => "108", "name" =>  "Redbox"],
            ["id" => "109", "name" =>  "Sams Club"],
            ["id" => "110", "name" =>  "Samsung"],
            ["id" => "111", "name" =>  "Singtel"],
            ["id" => "112", "name" =>  "Siriusxm"],
            ["id" => "113", "name" =>  "Solutran"],
            ["id" => "114", "name" =>  "Spotify"],
            ["id" => "115", "name" =>  "Sprint"],
            ["id" => "116", "name" =>  "Suntrust"],
            ["id" => "117", "name" =>  "Supportrix"],
            ["id" => "118", "name" =>  "Synchrony"],
            ["id" => "119", "name" =>  "Synnex"],
            ["id" => "120", "name" =>  "Taboola"],
            ["id" => "121", "name" =>  "Thewarehouseltd"],
            ["id" => "122", "name" =>  "Ticketmaster"],
            ["id" => "123", "name" =>  "Transurban"],
            ["id" => "124", "name" =>  "Tripadvisor"],
            ["id" => "125", "name" =>  "Tufts Health"],
            ["id" => "126", "name" =>  "Uber"],
            ["id" => "127", "name" =>  "Unilever"],
            ["id" => "128", "name" =>  "United Airlines"],
            ["id" => "129", "name" =>  "Us Cellular"],
            ["id" => "130", "name" =>  "Visa"],
            ["id" => "131", "name" =>  "Walmart"],
            ["id" => "132", "name" =>  "Westpac"],
            ["id" => "133", "name" =>  "Windstream"],
            ["id" => "134", "name" =>  "Xerox"],
            ["id" => "135", "name" =>  "Xiaomi"],
            ["id" => "136", "name" =>  "Yapstone"],
            ["id" => "137", "name" =>  "Yrudefault"]
        );
    }
}
