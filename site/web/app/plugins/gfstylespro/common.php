<?php
if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class GFSPCommon
 *
 * Includes common methods accessed throughout Gravity Forms and add-ons.
 */
class GFSPCommon {

    /**
     * Get array of Google fonts choices for dropdown
     * @return array
     */
    public static function get_font_choices() {
        $g_fonts = array( "Open Sans","Roboto","Lato","Slabo 27px","Roboto Condensed","Oswald","Montserrat","Source Sans Pro",
        "Lora","Raleway","PT Sans","Droid Sans","Open Sans Condensed","Ubuntu","Roboto Slab","Droid Serif","Arimo","Fjalla One",
        "Merriweather","PT Sans Narrow","Noto Sans","PT Serif","Titillium Web","Alegreya Sans","Passion One","Poiret One","Candal",
        "Playfair Display","Bitter","Indie Flower","Dosis","Yanone Kaffeesatz","Cabin","Oxygen","Lobster","Nunito","Fira Sans",
        "Hind","Arvo","Inconsolata","Noto Serif","Anton","Abel","Bree Serif","Muli","Ubuntu Condensed","Josefin Sans","Crimson Text"
        ,"Pacifico","Libre Baskerville","Exo 2","Francois One","Shadows Into Light","Asap","Play","Archivo Narrow","Signika",
        "Quicksand","Vollkorn","Merriweather Sans","Cuprum","Alegreya","Cinzel","Varela Round","Amatic SC","Maven Pro","Orbitron",
        "Karla","Dancing Script","Questrial","Righteous","Rokkitt","PT Sans Caption","Bangers","Architects Daughter","Exo","Chewy",
        "Patua One","Pathway Gothic One","BenchNine","Abril Fatface","Quattrocento Sans","Monda","Crete Round","Sigmar One","Josefin Slab"
        ,"Ropa Sans","News Cycle","Russo One","Istok Web","Kaushan Script","Covered By Your Grace","Lobster Two","EB Garamond","Comfortaa"
        ,"Pontano Sans","Cantarell","Didact Gothic","Gudea","Gloria Hallelujah","Poppins","Noticia Text","Coming Soon","Rambla",
        "Fredoka One","Philosopher","Old Standard TT","Armata","Archivo Black","Satisfy","ABeeZee","Hammersmith One","Rubik",
        "Cabin Condensed","Source Code Pro","Sanchez","Tinos","Acme","Handlee","Economica","Domine","Voltaire","Alfa Slab One",
        "Quattrocento","Kreon","Courgette","Rock Salt","Ruda","Cookie","Work Sans","Permanent Marker","Roboto Mono","Varela","Jura",
        "Shadows Into Light Two","Gentium Book Basic","Pinyon Script","Cardo","Actor","Vidaloka","Bevan","Great Vibes","Fauna One",
        "Paytone One","Tangerine","Playball","Luckiest Guy","Antic Slab","Volkhov","Amaranth","Changa One","Droid Sans Mono","Nobile",
        "Sorts Mill Goudy","Audiowide","Sintony","Bad Script","Marck Script","Special Elite","Niconne","Enriqueta","Arapey","Basic","Amiri"
        ,"Oleo Script","Fugaz One","Damion","Chivo","Squada One","Playfair Display SC","Kanit","Molengo","Signika Negative","Boogaloo",
        "Source Serif Pro","Scada","Calligraffitti","Viga","Glegoo","Marmelad","Copse","Just Another Hand","Gochi Hand","Coda","Doppio One",
        "Antic","Limelight","Nixie One","Cantata One","Allerta Stencil","Black Ops One","Aldrich","Patrick Hand","Homemade Apple","Neuton",
        "Overlock","Alice","Nothing You Could Do","Crafty Girls","Alegreya Sans SC","Carme","Jockey One","Khula","Waiting for the Sunrise",
        "Michroma","Average","Gentium Basic","Share","Lusitana","Days One","Spinnaker","Ultra","Marcellus","Rajdhani","Khand","Sacramento",
        "Julius Sans One","Walter Turncoat","Cherry Cream Soda","Electrolize","Bubblegum Sans","Contrail One","Catamaran","Homenaje",
        "Allerta","Syncopate","Alex Brush","Radley","Magra","Teko","Ceviche One","Montserrat Alternates","Fontdiner Swanky",
        "Fredericka the Great","Ubuntu Mono","Puritan","Kameron","PT Mono","Kalam","Adamina","Allura","Grand Hotel","Quantico","Marvel",
        "Telex","Rancho","Unica One","Reenie Beanie","PT Serif Caption","Finger Paint","Average Sans","Oranienbaum","Freckle Face",
        "Six Caps","Coustard","Prata","Carrois Gothic","Advent Pro","Neucha","Yellowtail","Rochester","Chelsea Market","Annie Use Your Telescope",
        "Merienda","Sansita One","Cabin Sketch","Pragati Narrow","Cutive","Lekton","Forum","Parisienne","Marcellus SC","Aclonica","Denk One",
        "Goudy Bookletter 1911","Alef","Cinzel Decorative","Berkshire Swash","Rosario","Ek Mukta","Convergence","Montez","Cambay","Schoolbell",
        "Trocchi","Arbutus Slab","Port Lligat Slab","Halant","Kelly Slab","Corben","Alegreya SC","Cousine","Leckerli One","Yesteryear",
        "Short Stack","Press Start 2P","Lustria","Belleza","Caudex","Mako","Lemon","Timmana","Frijole","Merienda One","Tenor Sans","Inder"
        ,"Engagement","Metrophobic","Graduate","Alike","Slackey","Fenix","Capriola","Gruppo","Carter One","Duru Sans","VT323","Prosto One"
        ,"Unkempt","Tauri","Sue Ellen Francisco","Strait","Monoton","Lilita One","Baumans","Give You Glory","Petit Formal Script","Ovo",
        "Racing Sans One","Gilda Display","Creepster","UnifrakturMaguntia","Italianno","Anaheim","Headland One","Just Me Again Down Here",
        "Quando","The Girl Next Door","Bowlby One","Oxygen Mono","Mr De Haviland","Mouse Memoirs","Allan","Lateef","Londrina Solid",
        "Hind Siliguri","Skranji","Delius","Anonymous Pro","Imprima","Sarala","Brawler","Nova Square","Wire One","Rufina",
        "Oleo Script Swash Caps","Averia Sans Libre","Cutive Mono","IM Fell DW Pica","Andika","Bowlby One SC","Sofia","Gravitas One","Crushed",
        "Judson","La Belle Aurore","Fanwood Text","Oregano","Bentham","Mr Dafoe","Herr Von Muellerhoff","Andada","IM Fell English SC","Knewave",
        "Arizonia","Loved by the King","Slabo 13px","Mate","Kadwa","Love Ya Like A Sister","Oldenburg","Kotta One","Megrim","Pompiere",
        "Happy Monkey","Yantramanav","Englebert","Yeseva One","Orienta","Belgrano","Simonetta","Patrick Hand SC","Euphoria Script","Balthazar",
        "Gafata","Geo","Karma","Aladin","Lily Script One","Kranky","Qwigley","Fjord One","Rationale","Norican","Podkova","Seaweed Script",
        "Mountains of Christmas","Caesar Dressing","Gabriela","Griffy","Poly","Quintessential","Dorsa","IM Fell English","Averia Serif Libre",
        "Shanti","Over the Rainbow","Stardos Stencil","Carrois Gothic SC","Expletus Sans","Life Savers","Mate SC","Bilbo Swash Caps","Fondamento",
        "Chau Philomene One","Share Tech","Salsa","Inika","Meddon","Italiana","Federo","Stalemate","Tienne","Buenard","Fira Mono",
        "Holtwood One SC","Hind Vadodara","Voces","Delius Swash Caps","Nova Mono","Titan One","Kristi","Unna","Tulpen One","Share Tech Mono",
        "Dawning of a New Day","Coda Caption","Clicker Script","Itim","Sniglet","Galindo","Maiden Orange","Sail","Kite One",
        "Stint Ultra Condensed","Poller One","Concert One","Codystar","Cambo","IM Fell Double Pica","Amethysta","Shojumaru","Cedarville Cursive"
        ,"Rouge Script","Ledger","Mallanna","NTR","Cantora One","Amarante","Cherry Swash","Raleway Dots","Nosifer","IM Fell French Canon",
        "Milonga","Sancreek","Ruslan Display","Vast Shadow","Medula One","Aguafina Script","Rubik One","Swanky and Moo Moo","Metamorphous",
        "Zeyada","Gurajada","Flamenco","Biryani","Sumana","Condiment","Kurale","Iceland","Martel","IM Fell Double Pica SC","Rammetto One","Junge",
        "Esteban","McLaren","Palanquin","Overlock SC","Rye","Martel Sans","Monofett","Caveat Brush","Cagliostro","Butcherman","Prociono",
        "Jolly Lodger","Donegal One","Dynalight","Habibi","IM Fell DW Pica SC","Artifika","Henny Penny","IM Fell French Canon SC","Ramabhadra",
        "Stint Ultra Expanded","Rosarivo","Sarina","Jacques Francois","Nova Round","Redressed","Trade Winds","Ruthie","Stoke","Kavoon","Paprika",
        "Averia Libre","Ruluko","Krona One","Buda","Sunshiney","Montserrat Subrayada","Piedra","Vibur","Wendy One","IM Fell Great Primer SC",
        "Delius Unicase","Wallpoet","Numans","Text Me One","IM Fell Great Primer","Pirata One","Bilbo","Irish Grover","Chango","Caveat",
        "Wellfleet","Glass Antiqua","Sonsie One","Smythe","Port Lligat Sans","Rubik Mono One","Linden Hill","Asul","Alike Angular","Offside",
        "Scheherazade","Mrs Saint Delafield","Ribeye","Snippet","New Rocker","Elsie","Nova Slim","League Script","Julee","Antic Didone",
        "Della Respira","Mystery Quest","Peralta","Trochut","MediSharp","UnifrakturCook","Dr Sugiyama","Palanquin Dark","Trykker","Bigshot One",
        "Snowburst One","Germania One","Kenia","Iceberg","Miniver","Joti One","Lovers Quarrel","Suranna","Autour One","Astloch","Keania One",
        "Londrina Shadow","Ribeye Marrow","Combo","Bubbler One","Akronim","Sura","Miltonian Tattoo","Croissant One","Elsie Swash Caps",
        "Jacques Francois Shadow","Fresca","Gorditas","Nova Flat","Modern Antiqua","Original Surfer","Spicy Rice","Purple Purse","Petrona",
        "Monsieur La Doulaise","Emilys Candy","Sarpanch","Faster One","Devonshire","Galdeano","Fascinate","Rozha One","Ranchers","Montaga",
        "Passero One","Jaldi","Lancelot","Atomic Age","Eagle Lake","Underdog","Diplomata","Averia Gruesa Libre","Londrina Sketch","Almendra",
        "Asset","Aubrey","Warnes","Laila","Geostar","Geostar Fill","Margarine","Sofadi One","Vampiro One","Mrs Sheppards","Miltonian","Ewert",
        "Dekko","Meie Script","Goblin One","Smokum","Mandali","Butterfly Kids","Chicle","Vesper Libre","Seymour One","Almendra SC","Chela One",
        "Felipa","Nova Script","Rum Raisin","Ria","Metal Mania","Princess Sofia","Spirax","Marko One","Romanesco","Nova Oval","Almendra Display",
        "Nova Cut","Federant","Uncial Antiqua","Plaster","Chonburi","Molle","Londrina Outline","Ramaraja","Supermercado One","Ranga",
        "Macondo Swash Caps","Dhurjati","Diplomata SC","Miss Fajardose","Eater","Arbutus","Fascinate Inline","Rhodium Libre","Stalinist One",
        "Arya","Sirin Stencil","Bigelow Rules","Macondo","Sevillana","Mr Bedfort","Gidugu","Risque","Amita","Ruge Boogie","Erica One","Flavors",
        "Jim Nightshade","Emblema One","Bonbon","Tenali Ramakrishna","Tillana","Suravaram","Sree Krushnadevaraya","Sahitya","Unlock",
        "Hanalei Fill","Hanalei","Eczar","Lakki Reddy","Inknut Antiqua","Asar","Fruktur","Peddana","Modak","Ravi Prakash"
        );

        $safe_fonts = array(
            "Arial",
            "Georgia",
            "Times New Roman",
            "Courier New",
            "Sans Serif",
            "Verdana",
            "Trebuchet MS",
            "Tahoma"
        );

        foreach ($g_fonts as $i) {
            $g_font_list[] = array(
            "label" => $i,
            "value" => $i
            );
        }

        foreach ($safe_fonts as $i) {
            $safe_font_list[] = array(
            "label" => $i,
            "value" => $i . '/Native'
            );
        }

        $choices = array(
            array(
                'label'   => esc_html__( 'No change', 'gf_supercharge' ),
                'value'  => '/Native',
            ),
            array(
                'label'   => esc_html__( 'Inherit from parent', 'gf_supercharge' ),
                'value'  => 'inherit/Native',
            ),
            array(
                'label'   => esc_html__( 'Custom font', 'gf_supercharge' ),
                'value'  => 'custom/Native',
            ),
            array(
                'label'   => esc_html__( 'Web safe/Native fonts', 'gf_supercharge' ),
                'disabled'  => 'disabled',
                'choices' => $safe_font_list
            ),
            array(
                'label' => esc_html__( 'Google Fonts', 'gf_supercharge' ),
                'disabled' => 'disabled',
                'choices' => $g_font_list
            )
        );

        return $choices;
    }

    
    /*
    * Reads data from all all theme files, return array
    */
    public static function get_themes() {

        $all_themes = array();
        
        $themes_data = array(
            'label' => 'Theme Name',
            'value' => 'Theme Slug',
            'font'  => 'Font',
            'font_label' => 'Font Label',
            'field_margin' => 'Field Margin',
            'field_icon_color' => 'Icon',
            'choice_style_color' => 'Choice Style Color',
            'desc'  => 'Description',
            'scripts' => "Features",
            'scripts_desc' => "Features Description"
            );

        foreach ( glob( plugin_dir_path( __FILE__ ) . "themes/*.css" ) as $file ) {
            $gfsp_themes = get_file_data($file, $themes_data, $context='');
            // print_r( $gfsp_themes );
            if ( $gfsp_themes['value'] != '' ){
                    $all_themes[] = $gfsp_themes;
            }

        }
        // Sort theme list
        asort($all_themes);

        return $all_themes;
    }

}
