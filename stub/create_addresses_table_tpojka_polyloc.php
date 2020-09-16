<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTableTpojkaPolyloc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // If some of these tables exist - do not execute
        if (Schema::hasTable('geo_locations') || Schema::hasTable('countries') || Schema::hasTable('addresses') || Schema::hasTable('phones')) {
            Log::debug('Some of intended tables {countries or addresses or phones} already exist which collides with this package concept.');
            return;
        }

        Schema::create('countries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedSmallInteger('num_code')->default(0);
            $table->string('alpha_2_code', 2)->nullable();
            $table->string('alpha_3_code', 3)->nullable();
            $table->string('en_short_name', 63)->nullable();
            $table->string('nationality', 63)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('geo_locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->geometry('geometry')->nullable();
            $table->point('point')->nullable();
            $table->lineString('line_string')->nullable();
            $table->polygon('polygon')->nullable();
            $table->multiPoint('multi_point')->nullable();
            $table->multiLineString('multi_line_string')->nullable();
            $table->multiPolygon('multi_polygon')->nullable();
            $table->geometryCollection('geometry_collection')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('addressable');
            $table->string('label');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('geo_location_id')->nullable();
            $table->string('line_1');
            $table->string('line_2')->nullable();
            $table->string('post_code');
            $table->string('city');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['addressable_id', 'addressable_type', 'label'], 'unique_address_label');

            $table
                ->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('geo_location_id')
                ->references('id')
                ->on('geo_locations')
                ->onUpdate('CASCADE')
                ->onDelete('SET NULL');
        });

        Schema::create('phones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('phoneable');
            $table->string('label');
            $table->string('phone_number');
            $table->unsignedTinyInteger('phone_number_type')->default(10);
            $table->unsignedTinyInteger('default')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['phoneable_id', 'phoneable_type', 'label'], 'unique_phone_label');
        });

        $this->seedCountries();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('phones', function (Blueprint $table) {
            $table->dropUnique('unique_phone_label');
        });
        Schema::dropIfExists('phones');

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign(['geo_location_id']);
            $table->dropForeign(['country_id']);
            $table->dropUnique('unique_address_label');
        });
        Schema::dropIfExists('addresses');

        Schema::dropIfExists('geo_locations');
        Schema::dropIfExists('countries');
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Seeding country table in this very step
     */
    private function seedCountries()
    {
        DB::insert('INSERT INTO `countries` (`num_code`, `alpha_2_code`, `alpha_3_code`, `en_short_name`, `nationality`, `created_at`, `updated_at`) VALUES
("4", "AF", "AFG", "Afghanistan", "Afghan", NOW(), NOW()),
("248", "AX", "ALA", "Åland Islands", "Åland Island", NOW(), NOW()),
("8", "AL", "ALB", "Albania", "Albanian", NOW(), NOW()),
("12", "DZ", "DZA", "Algeria", "Algerian", NOW(), NOW()),
("16", "AS", "ASM", "American Samoa", "American Samoan", NOW(), NOW()),
("20", "AD", "AND", "Andorra", "Andorran", NOW(), NOW()),
("24", "AO", "AGO", "Angola", "Angolan", NOW(), NOW()),
("660", "AI", "AIA", "Anguilla", "Anguillan", NOW(), NOW()),
("10", "AQ", "ATA", "Antarctica", "Antarctic", NOW(), NOW()),
("28", "AG", "ATG", "Antigua and Barbuda", "Antiguan or Barbudan", NOW(), NOW()),
("32", "AR", "ARG", "Argentina", "Argentine", NOW(), NOW()),
("51", "AM", "ARM", "Armenia", "Armenian", NOW(), NOW()),
("533", "AW", "ABW", "Aruba", "Aruban", NOW(), NOW()),
("36", "AU", "AUS", "Australia", "Australian", NOW(), NOW()),
("40", "AT", "AUT", "Austria", "Austrian", NOW(), NOW()),
("31", "AZ", "AZE", "Azerbaijan", "Azerbaijani, Azeri", NOW(), NOW()),
("44", "BS", "BHS", "Bahamas", "Bahamian", NOW(), NOW()),
("48", "BH", "BHR", "Bahrain", "Bahraini", NOW(), NOW()),
("50", "BD", "BGD", "Bangladesh", "Bangladeshi", NOW(), NOW()),
("52", "BB", "BRB", "Barbados", "Barbadian", NOW(), NOW()),
("112", "BY", "BLR", "Belarus", "Belarusian", NOW(), NOW()),
("56", "BE", "BEL", "Belgium", "Belgian", NOW(), NOW()),
("84", "BZ", "BLZ", "Belize", "Belizean", NOW(), NOW()),
("204", "BJ", "BEN", "Benin", "Beninese, Beninois", NOW(), NOW()),
("60", "BM", "BMU", "Bermuda", "Bermudian, Bermudan", NOW(), NOW()),
("64", "BT", "BTN", "Bhutan", "Bhutanese", NOW(), NOW()),
("68", "BO", "BOL", "Bolivia (Plurinational State of)", "Bolivian", NOW(), NOW()),
("535", "BQ", "BES", "Bonaire, Sint Eustatius and Saba", "Bonaire", NOW(), NOW()),
("70", "BA", "BIH", "Bosnia and Herzegovina", "Bosnian or Herzegovinian", NOW(), NOW()),
("72", "BW", "BWA", "Botswana", "Motswana, Botswanan", NOW(), NOW()),
("74", "BV", "BVT", "Bouvet Island", "Bouvet Island", NOW(), NOW()),
("76", "BR", "BRA", "Brazil", "Brazilian", NOW(), NOW()),
("86", "IO", "IOT", "British Indian Ocean Territory", "BIOT", NOW(), NOW()),
("96", "BN", "BRN", "Brunei Darussalam", "Bruneian", NOW(), NOW()),
("100", "BG", "BGR", "Bulgaria", "Bulgarian", NOW(), NOW()),
("854", "BF", "BFA", "Burkina Faso", "Burkinabé", NOW(), NOW()),
("108", "BI", "BDI", "Burundi", "Burundian", NOW(), NOW()),
("132", "CV", "CPV", "Cabo Verde", "Cabo Verdean", NOW(), NOW()),
("116", "KH", "KHM", "Cambodia", "Cambodian", NOW(), NOW()),
("120", "CM", "CMR", "Cameroon", "Cameroonian", NOW(), NOW()),
("124", "CA", "CAN", "Canada", "Canadian", NOW(), NOW()),
("136", "KY", "CYM", "Cayman Islands", "Caymanian", NOW(), NOW()),
("140", "CF", "CAF", "Central African Republic", "Central African", NOW(), NOW()),
("148", "TD", "TCD", "Chad", "Chadian", NOW(), NOW()),
("152", "CL", "CHL", "Chile", "Chilean", NOW(), NOW()),
("156", "CN", "CHN", "China", "Chinese", NOW(), NOW()),
("162", "CX", "CXR", "Christmas Island", "Christmas Island", NOW(), NOW()),
("166", "CC", "CCK", "Cocos (Keeling) Islands", "Cocos Island", NOW(), NOW()),
("170", "CO", "COL", "Colombia", "Colombian", NOW(), NOW()),
("174", "KM", "COM", "Comoros", "Comoran, Comorian", NOW(), NOW()),
("178", "CG", "COG", "Congo (Republic of the)", "Congolese", NOW(), NOW()),
("180", "CD", "COD", "Congo (Democratic Republic of the)", "Congolese", NOW(), NOW()),
("184", "CK", "COK", "Cook Islands", "Cook Island", NOW(), NOW()),
("188", "CR", "CRI", "Costa Rica", "Costa Rican", NOW(), NOW()),
("384", "CI", "CIV", "Côte d\'Ivoire", "Ivorian", NOW(), NOW()),
("191", "HR", "HRV", "Croatia", "Croatian", NOW(), NOW()),
("192", "CU", "CUB", "Cuba", "Cuban", NOW(), NOW()),
("531", "CW", "CUW", "Curaçao", "Curaçaoan", NOW(), NOW()),
("196", "CY", "CYP", "Cyprus", "Cypriot", NOW(), NOW()),
("203", "CZ", "CZE", "Czech Republic", "Czech", NOW(), NOW()),
("208", "DK", "DNK", "Denmark", "Danish", NOW(), NOW()),
("262", "DJ", "DJI", "Djibouti", "Djiboutian", NOW(), NOW()),
("212", "DM", "DMA", "Dominica", "Dominican", NOW(), NOW()),
("214", "DO", "DOM", "Dominican Republic", "Dominican", NOW(), NOW()),
("218", "EC", "ECU", "Ecuador", "Ecuadorian", NOW(), NOW()),
("818", "EG", "EGY", "Egypt", "Egyptian", NOW(), NOW()),
("222", "SV", "SLV", "El Salvador", "Salvadoran", NOW(), NOW()),
("226", "GQ", "GNQ", "Equatorial Guinea", "Equatorial Guinean, Equatoguinean", NOW(), NOW()),
("232", "ER", "ERI", "Eritrea", "Eritrean", NOW(), NOW()),
("233", "EE", "EST", "Estonia", "Estonian", NOW(), NOW()),
("231", "ET", "ETH", "Ethiopia", "Ethiopian", NOW(), NOW()),
("238", "FK", "FLK", "Falkland Islands (Malvinas)", "Falkland Island", NOW(), NOW()),
("234", "FO", "FRO", "Faroe Islands", "Faroese", NOW(), NOW()),
("242", "FJ", "FJI", "Fiji", "Fijian", NOW(), NOW()),
("246", "FI", "FIN", "Finland", "Finnish", NOW(), NOW()),
("250", "FR", "FRA", "France", "French", NOW(), NOW()),
("254", "GF", "GUF", "French Guiana", "French Guianese", NOW(), NOW()),
("258", "PF", "PYF", "French Polynesia", "French Polynesian", NOW(), NOW()),
("260", "TF", "ATF", "French Southern Territories", "French Southern Territories", NOW(), NOW()),
("266", "GA", "GAB", "Gabon", "Gabonese", NOW(), NOW()),
("270", "GM", "GMB", "Gambia", "Gambian", NOW(), NOW()),
("268", "GE", "GEO", "Georgia", "Georgian", NOW(), NOW()),
("276", "DE", "DEU", "Germany", "German", NOW(), NOW()),
("288", "GH", "GHA", "Ghana", "Ghanaian", NOW(), NOW()),
("292", "GI", "GIB", "Gibraltar", "Gibraltar", NOW(), NOW()),
("300", "GR", "GRC", "Greece", "Greek, Hellenic", NOW(), NOW()),
("304", "GL", "GRL", "Greenland", "Greenlandic", NOW(), NOW()),
("308", "GD", "GRD", "Grenada", "Grenadian", NOW(), NOW()),
("312", "GP", "GLP", "Guadeloupe", "Guadeloupe", NOW(), NOW()),
("316", "GU", "GUM", "Guam", "Guamanian, Guambat", NOW(), NOW()),
("320", "GT", "GTM", "Guatemala", "Guatemalan", NOW(), NOW()),
("831", "GG", "GGY", "Guernsey", "Channel Island", NOW(), NOW()),
("324", "GN", "GIN", "Guinea", "Guinean", NOW(), NOW()),
("624", "GW", "GNB", "Guinea-Bissau", "Bissau-Guinean", NOW(), NOW()),
("328", "GY", "GUY", "Guyana", "Guyanese", NOW(), NOW()),
("332", "HT", "HTI", "Haiti", "Haitian", NOW(), NOW()),
("334", "HM", "HMD", "Heard Island and McDonald Islands", "Heard Island or McDonald Islands", NOW(), NOW()),
("336", "VA", "VAT", "Vatican City State", "Vatican", NOW(), NOW()),
("340", "HN", "HND", "Honduras", "Honduran", NOW(), NOW()),
("344", "HK", "HKG", "Hong Kong", "Hong Kong, Hong Kongese", NOW(), NOW()),
("348", "HU", "HUN", "Hungary", "Hungarian, Magyar", NOW(), NOW()),
("352", "IS", "ISL", "Iceland", "Icelandic", NOW(), NOW()),
("356", "IN", "IND", "India", "Indian", NOW(), NOW()),
("360", "ID", "IDN", "Indonesia", "Indonesian", NOW(), NOW()),
("364", "IR", "IRN", "Iran", "Iranian, Persian", NOW(), NOW()),
("368", "IQ", "IRQ", "Iraq", "Iraqi", NOW(), NOW()),
("372", "IE", "IRL", "Ireland", "Irish", NOW(), NOW()),
("833", "IM", "IMN", "Isle of Man", "Manx", NOW(), NOW()),
("376", "IL", "ISR", "Israel", "Israeli", NOW(), NOW()),
("380", "IT", "ITA", "Italy", "Italian", NOW(), NOW()),
("388", "JM", "JAM", "Jamaica", "Jamaican", NOW(), NOW()),
("392", "JP", "JPN", "Japan", "Japanese", NOW(), NOW()),
("832", "JE", "JEY", "Jersey", "Channel Island", NOW(), NOW()),
("400", "JO", "JOR", "Jordan", "Jordanian", NOW(), NOW()),
("398", "KZ", "KAZ", "Kazakhstan", "Kazakhstani, Kazakh", NOW(), NOW()),
("404", "KE", "KEN", "Kenya", "Kenyan", NOW(), NOW()),
("296", "KI", "KIR", "Kiribati", "I-Kiribati", NOW(), NOW()),
("0", "XK", "UNK", "Kosovo", "Kosovar", NOW(), NOW()),
("408", "KP", "PRK", "Korea (Democratic People\'s Republic of)", "North Korean", NOW(), NOW()),
("410", "KR", "KOR", "Korea (Republic of)", "South Korean", NOW(), NOW()),
("414", "KW", "KWT", "Kuwait", "Kuwaiti", NOW(), NOW()),
("417", "KG", "KGZ", "Kyrgyzstan", "Kyrgyzstani, Kyrgyz, Kirgiz, Kirghiz", NOW(), NOW()),
("418", "LA", "LAO", "Lao People\'s Democratic Republic", "Lao, Laotian", NOW(), NOW()),
("428", "LV", "LVA", "Latvia", "Latvian", NOW(), NOW()),
("422", "LB", "LBN", "Lebanon", "Lebanese", NOW(), NOW()),
("426", "LS", "LSO", "Lesotho", "Basotho", NOW(), NOW()),
("430", "LR", "LBR", "Liberia", "Liberian", NOW(), NOW()),
("434", "LY", "LBY", "Libya", "Libyan", NOW(), NOW()),
("438", "LI", "LIE", "Liechtenstein", "Liechtenstein", NOW(), NOW()),
("440", "LT", "LTU", "Lithuania", "Lithuanian", NOW(), NOW()),
("442", "LU", "LUX", "Luxembourg", "Luxembourg, Luxembourgish", NOW(), NOW()),
("446", "MO", "MAC", "Macao", "Macanese, Chinese", NOW(), NOW()),
("807", "MK", "MKD", "Macedonia (the former Yugoslav Republic of)", "Macedonian", NOW(), NOW()),
("450", "MG", "MDG", "Madagascar", "Malagasy", NOW(), NOW()),
("454", "MW", "MWI", "Malawi", "Malawian", NOW(), NOW()),
("458", "MY", "MYS", "Malaysia", "Malaysian", NOW(), NOW()),
("462", "MV", "MDV", "Maldives", "Maldivian", NOW(), NOW()),
("466", "ML", "MLI", "Mali", "Malian, Malinese", NOW(), NOW()),
("470", "MT", "MLT", "Malta", "Maltese", NOW(), NOW()),
("584", "MH", "MHL", "Marshall Islands", "Marshallese", NOW(), NOW()),
("474", "MQ", "MTQ", "Martinique", "Martiniquais, Martinican", NOW(), NOW()),
("478", "MR", "MRT", "Mauritania", "Mauritanian", NOW(), NOW()),
("480", "MU", "MUS", "Mauritius", "Mauritian", NOW(), NOW()),
("175", "YT", "MYT", "Mayotte", "Mahoran", NOW(), NOW()),
("484", "MX", "MEX", "Mexico", "Mexican", NOW(), NOW()),
("583", "FM", "FSM", "Micronesia (Federated States of)", "Micronesian", NOW(), NOW()),
("498", "MD", "MDA", "Moldova (Republic of)", "Moldovan", NOW(), NOW()),
("492", "MC", "MCO", "Monaco", "Monégasque, Monacan", NOW(), NOW()),
("496", "MN", "MNG", "Mongolia", "Mongolian", NOW(), NOW()),
("499", "ME", "MNE", "Montenegro", "Montenegrin", NOW(), NOW()),
("500", "MS", "MSR", "Montserrat", "Montserratian", NOW(), NOW()),
("504", "MA", "MAR", "Morocco", "Moroccan", NOW(), NOW()),
("508", "MZ", "MOZ", "Mozambique", "Mozambican", NOW(), NOW()),
("104", "MM", "MMR", "Myanmar", "Burmese", NOW(), NOW()),
("516", "NA", "NAM", "Namibia", "Namibian", NOW(), NOW()),
("520", "NR", "NRU", "Nauru", "Nauruan", NOW(), NOW()),
("524", "NP", "NPL", "Nepal", "Nepali, Nepalese", NOW(), NOW()),
("528", "NL", "NLD", "Netherlands", "Dutch, Netherlandic", NOW(), NOW()),
("540", "NC", "NCL", "New Caledonia", "New Caledonian", NOW(), NOW()),
("554", "NZ", "NZL", "New Zealand", "New Zealand, NZ", NOW(), NOW()),
("558", "NI", "NIC", "Nicaragua", "Nicaraguan", NOW(), NOW()),
("562", "NE", "NER", "Niger", "Nigerien", NOW(), NOW()),
("566", "NG", "NGA", "Nigeria", "Nigerian", NOW(), NOW()),
("570", "NU", "NIU", "Niue", "Niuean", NOW(), NOW()),
("574", "NF", "NFK", "Norfolk Island", "Norfolk Island", NOW(), NOW()),
("580", "MP", "MNP", "Northern Mariana Islands", "Northern Marianan", NOW(), NOW()),
("578", "NO", "NOR", "Norway", "Norwegian", NOW(), NOW()),
("512", "OM", "OMN", "Oman", "Omani", NOW(), NOW()),
("586", "PK", "PAK", "Pakistan", "Pakistani", NOW(), NOW()),
("585", "PW", "PLW", "Palau", "Palauan", NOW(), NOW()),
("275", "PS", "PSE", "Palestine, State of", "Palestinian", NOW(), NOW()),
("591", "PA", "PAN", "Panama", "Panamanian", NOW(), NOW()),
("598", "PG", "PNG", "Papua New Guinea", "Papua New Guinean, Papuan", NOW(), NOW()),
("600", "PY", "PRY", "Paraguay", "Paraguayan", NOW(), NOW()),
("604", "PE", "PER", "Peru", "Peruvian", NOW(), NOW()),
("608", "PH", "PHL", "Philippines", "Philippine, Filipino", NOW(), NOW()),
("612", "PN", "PCN", "Pitcairn", "Pitcairn Island", NOW(), NOW()),
("616", "PL", "POL", "Poland", "Polish", NOW(), NOW()),
("620", "PT", "PRT", "Portugal", "Portuguese", NOW(), NOW()),
("630", "PR", "PRI", "Puerto Rico", "Puerto Rican", NOW(), NOW()),
("634", "QA", "QAT", "Qatar", "Qatari", NOW(), NOW()),
("638", "RE", "REU", "Réunion", "Réunionese, Réunionnais", NOW(), NOW()),
("642", "RO", "ROU", "Romania", "Romanian", NOW(), NOW()),
("643", "RU", "RUS", "Russian Federation", "Russian", NOW(), NOW()),
("646", "RW", "RWA", "Rwanda", "Rwandan", NOW(), NOW()),
("652", "BL", "BLM", "Saint Barthélemy", "Barthélemois", NOW(), NOW()),
("654", "SH", "SHN", "Saint Helena, Ascension and Tristan da Cunha", "Saint Helenian", NOW(), NOW()),
("659", "KN", "KNA", "Saint Kitts and Nevis", "Kittitian or Nevisian", NOW(), NOW()),
("662", "LC", "LCA", "Saint Lucia", "Saint Lucian", NOW(), NOW()),
("663", "MF", "MAF", "Saint Martin (French part)", "Saint-Martinoise", NOW(), NOW()),
("666", "PM", "SPM", "Saint Pierre and Miquelon", "Saint-Pierrais or Miquelonnais", NOW(), NOW()),
("670", "VC", "VCT", "Saint Vincent and the Grenadines", "Saint Vincentian, Vincentian", NOW(), NOW()),
("882", "WS", "WSM", "Samoa", "Samoan", NOW(), NOW()),
("674", "SM", "SMR", "San Marino", "Sammarinese", NOW(), NOW()),
("678", "ST", "STP", "Sao Tome and Principe", "São Toméan", NOW(), NOW()),
("682", "SA", "SAU", "Saudi Arabia", "Saudi, Saudi Arabian", NOW(), NOW()),
("686", "SN", "SEN", "Senegal", "Senegalese", NOW(), NOW()),
("688", "RS", "SRB", "Serbia", "Serbian", NOW(), NOW()),
("690", "SC", "SYC", "Seychelles", "Seychellois", NOW(), NOW()),
("694", "SL", "SLE", "Sierra Leone", "Sierra Leonean", NOW(), NOW()),
("702", "SG", "SGP", "Singapore", "Singaporean", NOW(), NOW()),
("534", "SX", "SXM", "Sint Maarten (Dutch part)", "Sint Maarten", NOW(), NOW()),
("703", "SK", "SVK", "Slovakia", "Slovak", NOW(), NOW()),
("705", "SI", "SVN", "Slovenia", "Slovenian, Slovene", NOW(), NOW()),
("90", "SB", "SLB", "Solomon Islands", "Solomon Island", NOW(), NOW()),
("706", "SO", "SOM", "Somalia", "Somali, Somalian", NOW(), NOW()),
("710", "ZA", "ZAF", "South Africa", "South African", NOW(), NOW()),
("239", "GS", "SGS", "South Georgia and the South Sandwich Islands", "South Georgia or South Sandwich Islands", NOW(), NOW()),
("728", "SS", "SSD", "South Sudan", "South Sudanese", NOW(), NOW()),
("724", "ES", "ESP", "Spain", "Spanish", NOW(), NOW()),
("144", "LK", "LKA", "Sri Lanka", "Sri Lankan", NOW(), NOW()),
("729", "SD", "SDN", "Sudan", "Sudanese", NOW(), NOW()),
("740", "SR", "SUR", "Suriname", "Surinamese", NOW(), NOW()),
("744", "SJ", "SJM", "Svalbard and Jan Mayen", "Svalbard", NOW(), NOW()),
("748", "SZ", "SWZ", "Swaziland", "Swazi", NOW(), NOW()),
("752", "SE", "SWE", "Sweden", "Swedish", NOW(), NOW()),
("756", "CH", "CHE", "Switzerland", "Swiss", NOW(), NOW()),
("760", "SY", "SYR", "Syrian Arab Republic", "Syrian", NOW(), NOW()),
("158", "TW", "TWN", "Taiwan, Province of China", "Chinese, Taiwanese", NOW(), NOW()),
("762", "TJ", "TJK", "Tajikistan", "Tajikistani", NOW(), NOW()),
("834", "TZ", "TZA", "Tanzania, United Republic of", "Tanzanian", NOW(), NOW()),
("764", "TH", "THA", "Thailand", "Thai", NOW(), NOW()),
("626", "TL", "TLS", "Timor-Leste", "Timorese", NOW(), NOW()),
("768", "TG", "TGO", "Togo", "Togolese", NOW(), NOW()),
("772", "TK", "TKL", "Tokelau", "Tokelauan", NOW(), NOW()),
("776", "TO", "TON", "Tonga", "Tongan", NOW(), NOW()),
("780", "TT", "TTO", "Trinidad and Tobago", "Trinidadian or Tobagonian", NOW(), NOW()),
("788", "TN", "TUN", "Tunisia", "Tunisian", NOW(), NOW()),
("792", "TR", "TUR", "Turkey", "Turkish", NOW(), NOW()),
("795", "TM", "TKM", "Turkmenistan", "Turkmen", NOW(), NOW()),
("796", "TC", "TCA", "Turks and Caicos Islands", "Turks and Caicos Island", NOW(), NOW()),
("798", "TV", "TUV", "Tuvalu", "Tuvaluan", NOW(), NOW()),
("800", "UG", "UGA", "Uganda", "Ugandan", NOW(), NOW()),
("804", "UA", "UKR", "Ukraine", "Ukrainian", NOW(), NOW()),
("784", "AE", "ARE", "United Arab Emirates", "Emirati, Emirian, Emiri", NOW(), NOW()),
("826", "GB", "GBR", "United Kingdom of Great Britain and Northern Ireland", "British, UK", NOW(), NOW()),
("581", "UM", "UMI", "United States Minor Outlying Islands", "American", NOW(), NOW()),
("840", "US", "USA", "United States of America", "American", NOW(), NOW()),
("858", "UY", "URY", "Uruguay", "Uruguayan", NOW(), NOW()),
("860", "UZ", "UZB", "Uzbekistan", "Uzbekistani, Uzbek", NOW(), NOW()),
("548", "VU", "VUT", "Vanuatu", "Ni-Vanuatu, Vanuatuan", NOW(), NOW()),
("862", "VE", "VEN", "Venezuela (Bolivarian Republic of)", "Venezuelan", NOW(), NOW()),
("704", "VN", "VNM", "Vietnam", "Vietnamese", NOW(), NOW()),
("92", "VG", "VGB", "Virgin Islands (British)", "British Virgin Island", NOW(), NOW()),
("850", "VI", "VIR", "Virgin Islands (U.S.)", "U.S. Virgin Island", NOW(), NOW()),
("876", "WF", "WLF", "Wallis and Futuna", "Wallis and Futuna, Wallisian or Futunan", NOW(), NOW()),
("732", "EH", "ESH", "Western Sahara", "Sahrawi, Sahrawian, Sahraouian", NOW(), NOW()),
("887", "YE", "YEM", "Yemen", "Yemeni", NOW(), NOW()),
("894", "ZM", "ZMB", "Zambia", "Zambian", NOW(), NOW()),
("716", "ZW", "ZWE", "Zimbabwe", "Zimbabwean", NOW(), NOW());');
    }
}
