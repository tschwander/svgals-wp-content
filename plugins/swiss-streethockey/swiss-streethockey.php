<?php
/*
Plugin Name:  Swiss-Streethockey
Version: 1.0
Description: Spielplan, Tabelle oder Scorerliste anzeigen
Author: Thomas Schwander
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: swissstreethockey
*/

// [ssha_spielplan id="1597" spiel_link="http://www.swiss-streethockey.ch/Meisterschaft/NLB/tabid/122/Token_2468/resultate_detail/spielid/" link="https://www.swiss-streethockey.ch/de-de/meisterschaft/nlb.aspx"]
function ssha_spielplan($atts) {
	extract(shortcode_atts(array('id' => 0,
								 'spiel_link' => "",
								 'link' => ""), $atts));
	
	$xml = simplexml_load_file('http://liga.ssha.ch/xml/spielplan.aspx?id='.$id.'&typ=team&list=all');  
	$trans = array(
		'Mon'       => 'Mo',
		'Tue'       => 'Di',
		'Wed'       => 'Mi',
		'Thu'       => 'Do',
		'Fri'       => 'Fr',
		'Sat'       => 'Sa',
		'Sun'       => 'So',
	);
	$content =  '<style>.tMobile{display:none;}
         .tMobile td, .tLarge td{border-top: 1px solid #ccc;}
         table.tMobile, table.tLarge{border-bottom:1px solid #ccc;}
         @media (max-width: 767px) {
             .tMobile{display:block;}
             .tLarge{display:none;}
         }</style>';
  
	$content .= '<table class="tLarge"><tr><td><strong>Datum/Zeit</strong></td><td><strong>Heimteam</strong></td><td><strong>Gastteam</strong></td><td style="width:80px"><strong>Ort</strong></td><td style="width:70px"><strong>Resultat</strong></td><td style="width:50px"><strong></strong></td></tr>';
	
	$nextGameSet = false;   
	foreach ( $xml->Spiel as $spiel )  
	{  
	   $timestamp = strtotime($spiel->datum);
	   $wochentag = date("D", $timestamp);
	   $wochentag = strtr($wochentag, $trans);
	   $nextGame = '';
	   if(strtotime("now") < $timestamp && !$nextGameSet){$nextGame= 'style="background:#eee"'; $nextGameSet = true;}; 
	   $gals = '';
	   $Datum = date('d.m.Y, ', $timestamp);
	   $Zeit = date('G:i', $timestamp); 
	   $content .=  '<tr ' . $nextGame .'><td>'. $Datum  .$wochentag.'.&nbsp;'. $Zeit . '</td>';  
	   if (strpos($spiel->heim,'SV Gals') !== false) {$gals = 'style="font-weight:bold"';};
	   $content .=  '<td '.$gals.'>' . $spiel->heim . '</td>';
	   if (strpos($spiel->gast,'SV Gals') !== false) {$gals = 'style="font-weight:bold"';} else {$gals='';};    
	   $content .=  '<td '.$gals.'>' . $spiel->gast . '</td>'; 
	   $content .=  '<td>' . $spiel->spielort . '</td>';
	   $content .=  '<td>' . $spiel->resultat . '</td><td>'; 
		$content .=  ($spiel->gespielt == '1') ? '<a href="'.$spiel_link. $spiel->spielnr . '" Titel="Details" target="_blank">Details</a></td></tr>' : '</td></tr>'; 
	}  
	$content .=  '</table>';
	$content .=  '<table class="tMobile">'; 
	$nextGameSet = false;  
	foreach ( $xml->Spiel as $spiel )  
	{  
	   $timestamp = strtotime($spiel->datum);
	   $wochentag = date("D", $timestamp);
	   $wochentag = strtr($wochentag, $trans);
	   $nextGame = '';
	   if(strtotime("now") < $timestamp && !$nextGameSet){$nextGame= 'style="background:#eee"'; $nextGameSet = true;}; 
	   $Datum = date('d.m.Y, ', $timestamp);
	   $Zeit = date('G:i', $timestamp); 
	   $content .= '<tr ' . $nextGame .'><td style="width:500px">'.$Datum .$wochentag.'.&nbsp;'. $Zeit . '<br />';  
	   if (strpos($spiel->heim,'SV Gals') !== false) {$content .= '<strong>' . $spiel->heim . '</strong>';} else {$content .= '' . $spiel->heim;}; 
	   $content .= ' - ';
	   if (strpos($spiel->gast,'SV Gals') !== false) {$content .= '<strong>' . $spiel->gast . '</strong> <br />';} else {$content .= '' . $spiel->gast . '<br />' ;};     
	   $content .= 'Ort: ' . $spiel->spielort . '<br />';
	   $content .= 'Resultat: ' . $spiel->resultat . '<br />'; 
	   $content .= ($spiel->gespielt == '1') ? '<a href="'.$spiel_link. $spiel->spielnr . '" Titel="Details" target="_blank">Details</a></td></tr>' : '</td></tr>'; 
	}  
	$content .= '</table>
	<a href="'.$link.'" target="_blank" title="Spielplan auf swiss-streethockey.ch">Link zum Spielplan auf swiss-streethockey.ch</a><br><br>';
	
	
	return $content;
}

add_shortcode('ssha_spielplan', 'ssha_spielplan');





// [ssha_tabelle id="120" link="https://www.swiss-streethockey.ch/Meisterschaft/NLB/tabid/122/Token_2468/tabelle/language/de-DE/Default.aspx"]
function ssha_tabelle($atts) {
	extract(shortcode_atts(array('id' => 0,
								 'link' => ""), $atts));

	$xml2 = simplexml_load_file('http://liga.ssha.ch/xml/tabelle.aspx?id='.$id.'&typ=liga');  
  
	$content = '<table class="tLarge"><tr>
	<td align="left" style="width:30px;" scope="col"><strong>Pos</strong></td>
	<td align="left" style="width:50%;" scope="col"><strong>Mannschaft</strong></td>
	<td align="left" scope="col"><strong>Sp</strong></td>
	<td align="left" scope="col"><strong>Si</strong></td>
	<td align="left" scope="col"><strong>Un</strong></td>
	<td scope="col"><strong>Ni</strong></td>
	<td align="left" scope="col"><strong>Ov</strong></td>
	<td align="left" scope="col"><strong>+</strong></td>
	<td scope="col"><strong>-</strong></td>
	<td align="left" scope="col"><strong>+/-</strong></td>
	<td align="left" scope="col"><strong>Pt</strong></td>
	</tr>';   
	foreach ( $xml2->Mannschaften->Mannschaft as $mannschaft )  
	{  
	   $gals2 = '';
	   if (strpos($mannschaft->Team,'SV Gals') !== false) {$gals2 = 'style="background:#eee"';};
	   $content .=  '<tr '. $gals2.'><td style="text-align:center">' .$mannschaft->Rang . '</td>';  
	   $content .=  '<td>' . $mannschaft->Team . '</td>';    
	   $content .=  '<td>' . $mannschaft->Spiele . '</td>'; 
	   $content .=  '<td>' . $mannschaft->Siege . '</td>';
	   $content .=  '<td>' . $mannschaft->Remis . '</td>'; 
	   $content .=  '<td>' . $mannschaft->Niederlagen . '</td>'; 
	   $content .=  '<td>' . $mannschaft->PlusPkt . '</td>'; 
	   $content .=  '<td>' . explode(":", $mannschaft->Tore)[0] . '</td>'; 
	   $content .=  '<td>' . explode(":", $mannschaft->Tore)[1] . '</td>'; 
	   $content .=  '<td>' . $mannschaft->Differenz . '</td>';  
	   $content .=  '<td>' . $mannschaft->Punkte . '</td></tr>'; 
	}  
	$content .=  '</table>';
	$content .=  '<table class="tMobile"><tr>
	<td align="left" style="width:10%;"><strong>Pos</strong></td>
	<td align="left" style="width:320px"><strong>Mannschaft</strong></td>
	<td align="left" style="width:10%;"><strong>Pt</strong></td>
	</tr>'; 
	foreach ( $xml2->Mannschaften->Mannschaft as $mannschaft )  
	{  
	   $gals3 = '';
	   if (strpos($mannschaft->Team,'SV Gals') !== false) {$gals3 = 'style="background:#eee"';};
	   $content .=  '<tr '.$gals3.'><td style="text-align:center">' .$mannschaft->Rang . '</td>';  
	   $content .=  '<td>' . $mannschaft->Team . '</td>';    
	   $content .=  '<td>' . $mannschaft->Punkte . '</td></tr>'; 
	}  
	$content .=  '</table>
	<a href="'.$link.'" target="_blank" title="Tabelle auf swiss-streethockey.ch">Link zur Tabelle auf swiss-streethockey.ch</a><br><br>';	
	return $content;
}
add_shortcode('ssha_tabelle', 'ssha_tabelle');


// [ssha_spieler id="1597" link="https://www.swiss-streethockey.ch/Meisterschaft/NLB/tabid/122/Token_2468/skorer/language/de-DE/Default.aspx"]
function ssha_spieler($atts) {
	extract(shortcode_atts(array('id' => 0,
								 'link' => ""), $atts));
	
	$xml3 = simplexml_load_file('http://liga.ssha.ch/xml/scorerliste.aspx?id='.$id.'&typ=team');  

	$content = '<table class="tLarge"><tr>
	<td align="left"><strong>Nachname</strong></td>
	<td align="left"><strong>Vorname</strong></td>
	<td align="left"><strong>Spiele</strong></td>
	<td align="left"><strong>Tore</strong></td>
	<td align="left"><strong>1. Ass.</strong></td>
	<td align="left"><strong>2. Ass.</strong></td>
	<td align="left"><strong>Pkt</strong></td>
	</tr>';   
	foreach ( $xml3->Spieler as $spieler )  
	{  
	   $content .=  '<tr><td>' .$spieler->nachname . '</td>';  
	   $content .=  '<td>' . $spieler->vorname . '</td>';    
	   $content .=  '<td>' . $spieler->anzahlspiele . '</td>'; 
	   $content .=  '<td>' . $spieler->totaltore . '</td>';
	   $content .=  '<td>' . $spieler->totalass1 . '</td>'; 
	   $content .=  '<td>' . $spieler->totalass2 . '</td>'; 
	   $content .=  '<td>' . $spieler->totalpkt . '</td></tr>'; 
	}  
	$content .=  '</table>';
	$content .=  '<table class="tMobile"><tr>
	<td align="left" style="width:200px;"><strong>Nachname</strong></td>
	<td align="left" style="width:40%;"><strong>Vorname</strong></td>
	<td align="left" style="width:20%;"><strong>Pkt</strong></td>
	</tr>'; 
	foreach ( $xml3->Spieler as $spieler  )  
	{  
	   $content .=  '<tr><td>' .$spieler->nachname . '</td>';  
	   $content .=  '<td>' . $spieler->vorname . '</td>';    
	   $content .=  '<td>' . $spieler->totalpkt . '</td></tr>';
	}  
	$content .=  '</table>
	<a href="'.$link.'" target="_blank" title="Skorerliste auf swiss-streethockey.ch">Link zur Skorerliste auf swiss-streethockey.ch</a>';

	return $content;
}
add_shortcode('ssha_spieler', 'ssha_spieler');


class TEC_API_Sync {
    protected $option_name = 'tec_sync_options';

    public function __construct() {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function register_menu() {
        add_menu_page('TEC Sync', 'TEC Sync', 'manage_options', 'tec-sync', [$this, 'settings_page'], 'dashicons-update', 20);
    }

    public function register_settings() {
        register_setting('tec_sync_group', $this->option_name);
    }

    public function settings_page() {
        $opts = get_option($this->option_name, [
            'api_endpoint' => '',
            'general_category' => '',
            'start_date' => '',
            'end_date' => '',
            'teams' => []
        ]);

        if (isset($_POST['tec_sync_preview'])) {
            $preview = $this->generate_preview($opts);
        }
        ?>
        <div class="wrap">
            <h1>TEC API Sync</h1>
            <form method="post" action="options.php">
                <?php settings_fields('tec_sync_group'); ?>

                <h2>Allgemeine Einstellungen</h2>
                <table class="form-table">
                    <tr>
                        <th><label for="api_endpoint">API Endpoint</label></th>
                        <td><input type="text" name="<?php echo $this->option_name; ?>[api_endpoint]" value="<?php echo esc_attr($opts['api_endpoint']); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="general_category">Allgemeine Kategorie</label></th>
                        <td>
                            <select name="<?php echo $this->option_name; ?>[general_category]">
                                <?php $cats = get_terms(['taxonomy'=>'tribe_events_cat','hide_empty'=>false]);
                                foreach($cats as $cat) : ?>
                                    <option value="<?php echo $cat->term_id; ?>" <?php selected($opts['general_category'], $cat->term_id); ?>><?php echo esc_html($cat->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="start_date">Startdatum</label></th>
                        <td><input type="date" name="<?php echo $this->option_name; ?>[start_date]" value="<?php echo esc_attr($opts['start_date']); ?>" /></td>
                    </tr>
                    <tr>
                        <th><label for="end_date">Enddatum</label></th>
                        <td><input type="date" name="<?php echo $this->option_name; ?>[end_date]" value="<?php echo esc_attr($opts['end_date']); ?>" /></td>
                    </tr>
                </table>

                <h2>Teams</h2>
                <table class="widefat" id="tec-sync-teams">
                    <thead><tr><th>Team ID</th><th>Titel-Präfix</th><th>Kategorie</th><th>Turnier</th><th></th></tr></thead>
                    <tbody>
                    <?php $teams = is_array($opts['teams']) ? $opts['teams'] : [];
                    foreach ($teams as $i => $team): ?>
                        <tr>
                            <td><input type="text" name="<?php echo $this->option_name; ?>[teams][<?php echo $i; ?>][id]" value="<?php echo esc_attr($team['id']); ?>" /></td>
                            <td><input type="text" name="<?php echo $this->option_name; ?>[teams][<?php echo $i; ?>][prefix]" value="<?php echo esc_attr($team['prefix']); ?>" /></td>
                            <td>
                                <select name="<?php echo $this->option_name; ?>[teams][<?php echo $i; ?>][category]">
                                    <?php foreach ($cats as $cat): ?>
                                        <option value="<?php echo $cat->term_id; ?>" <?php selected($team['category'], $cat->term_id); ?>><?php echo esc_html($cat->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="checkbox" name="<?php echo $this->option_name; ?>[teams][<?php echo $i; ?>][is_tournament]" value="1" <?php checked(!empty($team['is_tournament'])); ?> /></td>
                            <td><span class="delete-row" style="color:red;cursor:pointer;">✕</span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <p><button type="button" class="button" id="add-team">Team hinzufügen</button></p>
                <script>
                document.getElementById('add-team').addEventListener('click', function(){
                    const table = document.querySelector('#tec-sync-teams tbody');
                    const index = table.rows.length;
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                        <td><input type="text" name="<?php echo $this->option_name; ?>[teams][${index}][id]" /></td>
                        <td><input type="text" name="<?php echo $this->option_name; ?>[teams][${index}][prefix]" /></td>
                        <td><select name="<?php echo $this->option_name; ?>[teams][${index}][category]">
                            <?php foreach ($cats as $cat): ?>
                                <option value="<?php echo $cat->term_id; ?>"><?php echo esc_html($cat->name); ?></option>
                            <?php endforeach; ?>
                        </select></td>
                        <td><input type="checkbox" name="<?php echo $this->option_name; ?>[teams][${index}][is_tournament]" value="1" /></td>
                        <td><span class="delete-row" style="color:red;cursor:pointer;">✕</span></td>`;
                    table.appendChild(newRow);
                });
                document.addEventListener('click', e => {
                    if(e.target.classList.contains('delete-row')) e.target.closest('tr').remove();
                });
                </script>

                <?php submit_button(); ?>
            </form>

            <form method="post">
                <p><input type="submit" name="tec_sync_preview" class="button button-primary" value="Vorschau anzeigen"></p>
            </form>

            <?php if (!empty($preview)) echo $preview; ?>
        </div>
        <?php
    }

    private function generate_preview($opts) {
        $api_events = $this->fetch_api_events($opts);
        $team_cat_ids = array_map(fn($t)=>$t['category'], $opts['teams']);
        $local_events = $this->get_local_events($opts, $team_cat_ids);

        $rows = [];
        foreach ($api_events as $event) {
            $match = $this->find_match($event, $local_events);
            $rows[] = [
                'team' => $event['team_prefix'],
                'category' => $event['category_name'],
                'general_category' => get_term($opts['general_category'])->name ?? '',
                'is_tournament' => $event['is_tournament'],
                'api_title' => $event['title'],
                'local_title' => $match ? $match->post_title : '—',
                'status' => $match ? '✅ Match' : '⚠️ Fehlt lokal'
            ];
        }

        foreach ($local_events as $local) {
            $found = false;
            foreach ($api_events as $event) {
                if ($this->is_same_event($event, $local)) { $found = true; break; }
            }
            if (!$found) {
                $rows[] = [
                    'team' => '-',
                    'category' => '-',
                    'general_category' => '-',
                    'is_tournament' => false,
                    'api_title' => '—',
                    'local_title' => $local->post_title,
                    'status' => '🗑️ Nur lokal vorhanden'
                ];
            }
        }

        ob_start(); ?>
        <h2>Vorschau</h2>
        <table class="widefat">
            <thead><tr><th>Team</th><th>Kategorie</th><th>Allgemeine Kategorie</th><th>Turnier</th><th>API Event</th><th>Lokales Event</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?php echo esc_html($r['team']); ?></td>
                    <td><?php echo esc_html($r['category']); ?></td>
                    <td><?php echo esc_html($r['general_category']); ?></td>
                    <td><?php echo $r['is_tournament'] ? '✓' : '–'; ?></td>
                    <td><?php echo esc_html($r['api_title']); ?></td>
                    <td><?php echo esc_html($r['local_title']); ?></td>
                    <td><?php echo esc_html($r['status']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php return ob_get_clean();
    }

    protected function fetch_api_events($opts) {
        $events = [];
        if (empty($opts['api_endpoint']) || empty($opts['teams'])) return [];

        foreach ($opts['teams'] as $team) {
            $url = add_query_arg(['typ'=>'team','list'=>'all','id'=>$team['id']], $opts['api_endpoint']);
            $response = wp_remote_get($url);
            if (is_wp_error($response)) continue;
            $xml = simplexml_load_string(wp_remote_retrieve_body($response));
            if (!$xml || empty($xml->Spiel)) continue;

            $spiele = $xml->Spiel;
            if (!empty($team['is_tournament'])) {
                $grouped = [];
                foreach ($spiele as $spiel) {
                    $day = date('Y-m-d', strtotime((string)$spiel->datum));
                    $ort = (string)$spiel->spielort;
                    $key = $day.'|'.$ort;
                    if (!isset($grouped[$key])) $grouped[$key] = [];
                    $grouped[$key][] = $spiel;
                }
                foreach ($grouped as $key=>$spieleTag) {
                    [$day,$ort] = explode('|',$key);
                    $times = array_map(fn($s)=>strtotime((string)$s->datum), $spieleTag);
                    $start = min($times);
                    $end = max($times)+3600;
                    $events[] = [
                        'title'=>$team['prefix'].': Turnier in '.$ort,
                        'start'=>date('Y-m-d H:i:s',$start),
                        'end'=>date('Y-m-d H:i:s',$end),
                        'venue'=>$ort,
                        'team_prefix'=>$team['prefix'],
                        'category_id'=>$team['category'],
                        'category_name'=>get_term($team['category'])->name ?? '',
                        'is_tournament'=>true
                    ];
                }
            } else {
                foreach ($spiele as $spiel) {
                    $datum = (string)$spiel->datum;
                    $heim = (string)$spiel->heim;
                    $gast = (string)$spiel->gast;
                    $ort = (string)$spiel->spielort;
                    $liga = (string)$spiel->liga;

                    // Präfix erweitern, wenn 'cup' oder 'playoffs' vorkommt
                    $prefix = $team['prefix'];
                    if (stripos($liga,'cup')!==false) $prefix .= ' (Cup)';
                    if (stripos($liga,'playoffs')!==false) $prefix .= ' (Playoffs)';

                    $events[] = [
                        'title'=>$prefix.': '.$heim.' – '.$gast,
                        'start'=>date('Y-m-d H:i:s',strtotime($datum)),
                        'end'=>date('Y-m-d H:i:s',strtotime($datum)),
                        'venue'=>$ort,
                        'team_prefix'=>$prefix,
                        'category_id'=>$team['category'],
                        'category_name'=>get_term($team['category'])->name ?? '',
                        'is_tournament'=>false
                    ];
                }
            }
        }
        return $events;
    }

    protected function get_local_events($opts, $team_cat_ids) {
        $args = [
            'post_type'=>'tribe_events',
            'posts_per_page'=>-1,
            'post_status'=>'publish',
            'meta_query'=>[[
                'key'=>'_EventStartDate',
                'value'=>[$opts['start_date'].' 00:00:00',$opts['end_date'].' 23:59:59'],
                'compare'=>'BETWEEN','type'=>'DATETIME'
            ]],
            'tax_query'=>[[
                'taxonomy'=>'tribe_events_cat','field'=>'term_id','terms'=>$team_cat_ids,'operator'=>'IN'
            ]]
        ];
        return get_posts($args);
    }

    protected function find_match($api_event,$local_events){
        foreach($local_events as $local){
            if($this->is_same_event($api_event,$local)) return $local;
        }
        return null;
    }

    protected function is_same_event($api_event,$local){
        $local_date = get_post_meta($local->ID,'_EventStartDate',true);
        $local_day = date('Y-m-d',strtotime($local_date));
        $api_day = date('Y-m-d',strtotime($api_event['start']));
        if($api_event['is_tournament']){
            $cats = wp_get_post_terms($local->ID,'tribe_events_cat',['fields'=>'ids']);
            return ($local_day===$api_day && in_array($api_event['category_id'],$cats));
        }else{
            return (strcasecmp($local->post_title,$api_event['title'])===0 && $local_day===$api_day);
        }
    }

    public function tec_sync_run_comparison(){}
    public function tec_sync_apply_changes(){}
}

//new TEC_API_Sync();


class TEC_API_Sync_Cron extends TEC_API_Sync {
    private $log_option = 'tec_sync_log';

    public function __construct() {
        parent::__construct();
        add_action('tec_sync_cron_hook', [$this, 'run_cron_sync']);

        if (!wp_next_scheduled('tec_sync_cron_hook')) {
            wp_schedule_event(time(), 'hourly', 'tec_sync_cron_hook');
        }

        add_action('admin_menu', [$this,'log_page']);
    }

    public function run_cron_sync() {
        $log = get_option($this->log_option, []);

        // Fehler-Handler, der alles ins Log schreibt
        set_error_handler(function($errno, $errstr, $errfile, $errline) use (&$log){
            $log[] = "⚠️ PHP Fehler: [$errno] $errstr in $errfile:$errline";
            update_option('tec_sync_log', $log);
            return true; // verhindert Standardanzeige
        });

        // Optional auch Exceptions abfangen
        try {
            $opts = get_option($this->option_name, []);
            if (empty($opts['teams'])) throw new Exception('Keine Teams konfiguriert!');
            $api_events = $this->fetch_api_events($opts);
            if (is_wp_error($api_events)) throw new Exception('API Fehler: '.$api_events->get_error_message());

            $team_cat_ids = array_map(fn($t)=>$t['category'], $opts['teams']);
            $local_events = $this->get_local_events($opts, $team_cat_ids);

            // Deine Logik für neue Events und Papierkorb...
            // Neue Events anlegen
            foreach ($api_events as $event) {
                $match = $this->find_match($event, $local_events);

                if (!$match) {
                    $venue_name = ($event['venue'] === 'Gals') ? 'tschilar baut Arena' : $event['venue'];

                    $venue_id = 0;

                    // Suche nach vorhandener tribe_venue per WP_Query (suchen & exakter Titelabgleich)
                    $venue_query = new WP_Query([
                        'post_type'      => 'tribe_venue',
                        'post_status'    => 'any',
                        'posts_per_page' => 5,       // etwas größer falls fuzzy search, wir prüfen später exakt
                        's'              => $venue_name
                    ]);

                    if ($venue_query->have_posts()) {
                        foreach ($venue_query->posts as $vpost) {
                            // exakter Titelabgleich (case-insensitive, ohne HTML)
                            if (strcasecmp(trim($vpost->post_title), trim($venue_name)) === 0) {
                                $venue_id = (int) $vpost->ID;
                                break;
                            }
                        }
                    }
                    wp_reset_postdata();


                    
                    $post_id = wp_insert_post([
                        'post_title' => $event['title'],
                        'post_type' => 'tribe_events',
                        'post_status' => 'publish'
                    ]);
                    if ($post_id) {
                        $timezone = 'Europe/Zurich';
                        update_post_meta($post_id, '_EventStartDate', $event['start']);
                        update_post_meta($post_id, '_EventEndDate', $event['end']);
                        update_post_meta($post_id, '_EventTimezone', $timezone);

                        $start_utc = get_gmt_from_date($event['start']);
                        $end_utc   = get_gmt_from_date($event['end']);
                        update_post_meta($post_id, '_EventStartDateUTC', $start_utc);
                        update_post_meta($post_id, '_EventEndDateUTC', $end_utc);
                        
                        if (empty($venue_id)) {
                            $new_venue = wp_insert_post([
                                'post_title'  => wp_strip_all_tags($venue_name),
                                'post_status' => 'publish',
                                'post_type'   => 'tribe_venue',
                                'post_content'=> '', // optional: hier könntest du Adresse o.ä. anlegen, falls vorhanden
                            ]);

                            if (!is_wp_error($new_venue) && $new_venue) {
                                $venue_id = (int) $new_venue;
                            } else {
                                // Fehler beim Anlegen -> ins Log schreiben, aber weiterfahren
                                $log[] = "⚠️ Venue konnte nicht erstellt werden: " . (is_wp_error($new_venue) ? $new_venue->get_error_message() : 'unknown error');
                            }
                        }

                        // Venue-ID dem Event zuweisen, falls vorhanden
                        if (!empty($venue_id)) {
                            update_post_meta($post_id, '_EventVenueID', $venue_id);
                            $log[] = "📍 Venue zugewiesen: " . esc_html($venue_name) . " (ID: $venue_id) für Event " . esc_html($event['title']);
                        } else {
                            $log[] = "⚠️ Keine Venue-ID gefunden/erstellt für Event " . esc_html($event['title']) . " (Venue: " . esc_html($venue_name) . ")";
                        }

                        $term_ids = [];

                        // Team-Kategorie prüfen
                        if (term_exists((int)$event['category_id'], 'tribe_events_cat')) {
                            $term_ids[] = (int)$event['category_id'];
                        }

                        // Allgemeine Kategorie hinzufügen
                        if (!empty($opts['general_category']) && term_exists((int)$opts['general_category'], 'tribe_events_cat')) {
                            $term_ids[] = (int)$opts['general_category'];
                        }

                        if (!empty($term_ids)) {
                            wp_set_object_terms($post_id, $term_ids, 'tribe_events_cat');
                        }
                        $log[] = "✅ Neues Event erstellt: <a href='".get_edit_post_link($post_id)."'>".$event['title']."</a>";
                    }
                    
                }
            }

            // Lokale Events in Papierkorb verschieben
            foreach ($local_events as $local) {
                $found = false;
                foreach ($api_events as $event) {
                    if ($this->is_same_event($event,$local)) { $found=true; break; }
                }
                if (!$found) {
                    wp_trash_post($local->ID);
                    $log[] = "🗑️ Event verschoben in Papierkorb: <a href='".get_edit_post_link($local->ID)."'>".$local->post_title."</a>";
                }
            }
            
            //$log[] = '⏱ Cronjob erfolgreich ausgeführt am ' . current_time('Y-m-d H:i:s');

        } catch (Exception $e) {
            //$log[] = "⚠️ Exception: ".$e->getMessage();
        }

        update_option($this->log_option, $log);

        restore_error_handler();
    }



    public function log_page() {
        add_submenu_page('tec-sync','TEC Sync Log','Sync Log','manage_options', 'tec-sync-log', function(){
            if (isset($_POST['tec_sync_clear_log'])) {
                update_option($this->log_option, []);
                echo '<div class="updated"><p>Log geleert.</p></div>';
            }
            $log = get_option($this->log_option, []);
            ?>
            <div class="wrap">
                <h1>TEC Sync Log</h1>
                <form method="post"><p><input type="submit" name="tec_sync_clear_log" class="button button-secondary" value="Log leeren"></p></form>
                <ul><?php foreach ($log as $entry) echo '<li>'.$entry.'</li>'; ?></ul>
            </div>
            <?php
        });
    }
}

new TEC_API_Sync_Cron();
