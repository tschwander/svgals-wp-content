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
    private $option_name = 'tec_sync_options';

    public function __construct() {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'settings_init'));
    }

    public function admin_menu() {
        add_menu_page(
            'TEC Sync',
            'TEC Sync',
            'manage_options',
            'tec-sync',
            array($this, 'admin_page'),
            'dashicons-update',
            56
        );
    }

    public function settings_init() {
        register_setting('tec_sync_group', $this->option_name, array($this, 'sanitize_options'));

        add_settings_section('tec_sync_section_main', 'Haupteinstellungen', null, 'tec-sync');

        add_settings_field('api_endpoint', 'API Endpoint', array($this, 'field_api_endpoint'), 'tec-sync', 'tec_sync_section_main');
        add_settings_field('api_key', 'API Key (optional)', array($this, 'field_api_key'), 'tec-sync', 'tec_sync_section_main');
        add_settings_field('start_date', 'Startdatum', array($this, 'field_start_date'), 'tec-sync', 'tec_sync_section_main');
        add_settings_field('end_date', 'Enddatum', array($this, 'field_end_date'), 'tec-sync', 'tec_sync_section_main');
        add_settings_field('store_category', 'Kategorie (Term ID) zum Speichern', array($this, 'field_store_category'), 'tec-sync', 'tec_sync_section_main');

        add_settings_field('teams', 'Teams', array($this, 'field_teams'), 'tec-sync', 'tec_sync_section_main');
    }

    public function sanitize_options($input) {
        $out = get_option($this->option_name, array());
        $out['api_endpoint'] = isset($input['api_endpoint']) ? esc_url_raw($input['api_endpoint']) : '';
        $out['api_key'] = isset($input['api_key']) ? sanitize_text_field($input['api_key']) : '';
        $out['start_date'] = isset($input['start_date']) ? sanitize_text_field($input['start_date']) : '';
        $out['end_date'] = isset($input['end_date']) ? sanitize_text_field($input['end_date']) : '';
        $out['store_category'] = isset($input['store_category']) ? absint($input['store_category']) : 0;

        if (isset($input['teams_json'])) {
            $teams = json_decode(wp_unslash($input['teams_json']), true);
            if (!is_array($teams)) $teams = array();
            foreach ($teams as $k => $t) {
                $teams[$k]['id'] = isset($t['id']) ? sanitize_text_field($t['id']) : '';
                $teams[$k]['prefix'] = isset($t['prefix']) ? sanitize_text_field($t['prefix']) : '';
            }
            $out['teams'] = $teams;
        }

        return $out;
    }

    public function field_api_endpoint() {
        $opts = get_option($this->option_name, array());
        printf('<input type="text" name="%s[api_endpoint]" value="%s" style="width:100%%">', esc_attr($this->option_name), esc_attr(isset($opts['api_endpoint']) ? $opts['api_endpoint'] : ''));
    }

    public function field_api_key() {
        $opts = get_option($this->option_name, array());
        printf('<input type="text" name="%s[api_key]" value="%s" style="width:100%%">', esc_attr($this->option_name), esc_attr(isset($opts['api_key']) ? $opts['api_key'] : ''));
    }

    public function field_start_date() {
        $opts = get_option($this->option_name, array());
        printf('<input type="date" name="%s[start_date]" value="%s">', esc_attr($this->option_name), esc_attr(isset($opts['start_date']) ? $opts['start_date'] : ''));
        echo '<p class="description">Startdatum des Zeitraums, über den Events abgeglichen werden.</p>';
    }

    public function field_end_date() {
        $opts = get_option($this->option_name, array());
        printf('<input type="date" name="%s[end_date]" value="%s">', esc_attr($this->option_name), esc_attr(isset($opts['end_date']) ? $opts['end_date'] : ''));
        echo '<p class="description">Enddatum des Zeitraums, über den Events abgeglichen werden.</p>';
    }

    public function field_store_category() {
        $opts = get_option($this->option_name, array());
        printf('<input type="number" min="0" name="%s[store_category]" value="%s">', esc_attr($this->option_name), esc_attr(isset($opts['store_category']) ? $opts['store_category'] : 0));
    }

    public function field_teams() {
        $opts = get_option($this->option_name, array());
        $teams = isset($opts['teams']) && is_array($opts['teams']) ? $opts['teams'] : array(array('id' => '', 'prefix' => ''));

        echo '<div id="tec-teams-wrap">';
        echo '<table class="widefat"><thead><tr><th>Team ID</th><th>Titel-Präfix</th><th></th></tr></thead><tbody id="tec-teams-body">';
        foreach ($teams as $t) {
            $id = esc_attr($t['id']);
            $prefix = esc_attr($t['prefix']);
            echo "<tr><td><input type='text' class='team-id' value='{$id}'></td><td><input type='text' class='team-prefix' value='{$prefix}'></td><td><button class='button remove-team' type='button'>Entfernen</button></td></tr>";
        }
        echo '</tbody></table>';
        echo '<p><button id="add-team" class="button" type="button">Team hinzufügen</button></p>';

        $teams_json = esc_attr(json_encode($teams));
        printf('<input type="hidden" id="tec-teams-json" name="%s[teams_json]" value="%s">', esc_attr($this->option_name), $teams_json);

        echo "<script>(function(){const tbody=document.getElementById('tec-teams-body');const addBtn=document.getElementById('add-team');const jsonInput=document.getElementById('tec-teams-json');function saveJson(){const rows=tbody.querySelectorAll('tr');const arr=Array.from(rows).map(r=>({id:r.querySelector('.team-id').value,prefix:r.querySelector('.team-prefix').value}));jsonInput.value=JSON.stringify(arr);}addBtn.addEventListener('click',function(){const tr=document.createElement('tr');tr.innerHTML='<td><input type=\'text\' class=\'team-id\'></td><td><input type=\'text\' class=\'team-prefix\'></td><td><button class=\'button remove-team\' type=\'button\'>Entfernen</button></td>';tbody.appendChild(tr);attachRemove(tr);saveJson();});function attachRemove(tr){const btn=tr.querySelector('.remove-team');btn.addEventListener('click',()=>{tr.remove();saveJson();});['change','input'].forEach(ev=>{tr.querySelectorAll('input').forEach(i=>i.addEventListener(ev,saveJson));});}tbody.querySelectorAll('tr').forEach(r=>attachRemove(r));document.querySelector('form').addEventListener('submit',saveJson);})();</script>";
        echo '</div>';
    }

    public function admin_page() {
        if (!current_user_can('manage_options')) return;
        echo '<div class="wrap"><h1>TEC API Sync</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields('tec_sync_group');
        do_settings_sections('tec-sync');
        submit_button('Einstellungen speichern');
        echo '</form>';

        echo '<h2>Sync-Vorschau</h2><form method="post">';
        wp_nonce_field('tec_sync_preview', 'tec_sync_preview_nonce');
        echo '<p><input type="submit" name="tec_preview_run" class="button button-primary" value="Vorschau anzeigen"></p></form>';

        if (isset($_POST['tec_preview_run']) && wp_verify_nonce($_POST['tec_sync_preview_nonce'], 'tec_sync_preview')) {
            echo $this->render_preview_table();
        }
        echo '</div>';
    }

    private function fetch_api_events($team_id, $start_date, $end_date) {
        $opts = get_option($this->option_name, array());
        if (empty($opts['api_endpoint'])) return array();
        $url = add_query_arg(array('team_id'=>$team_id,'start'=>$start_date,'end'=>$end_date), $opts['api_endpoint']);
        $args = array('timeout'=>20);
        if (!empty($opts['api_key'])) $args['headers'] = array('Authorization'=>'Bearer '.$opts['api_key']);
        $res = wp_remote_get($url,$args);
        if (is_wp_error($res)) return array();
        $data = json_decode(wp_remote_retrieve_body($res), true);
        return is_array($data)?$data:array();
    }

    private function get_local_events($start_date, $end_date) {
        $args = array('post_type'=>'tribe_events','posts_per_page'=>-1,'meta_query'=>array(array('key'=>'_EventStartDate','value'=>array($start_date.' 00:00:00',$end_date.' 23:59:59'),'compare'=>'BETWEEN','type'=>'DATETIME')));
        $q = new WP_Query($args);
        $events = array();
        while($q->have_posts()){ $q->the_post(); $id=get_the_ID(); $events[] = array('id'=>$id,'title'=>get_the_title(),'start'=>get_post_meta($id,'_EventStartDate',true),'categories'=>wp_get_post_terms($id,'tribe_events_cat',array('fields'=>'names'))); }
        wp_reset_postdata();
        return $events;
    }

    private function match_events($api_event, $local_events) {
        $api_title = trim($api_event['title']);
        $api_cat = isset($api_event['category']) ? $api_event['category'] : '';
        $api_start = isset($api_event['start']) ? $api_event['start'] : '';
        foreach ($local_events as $le) {
            $title_match = (strcasecmp(trim($le['title']), $api_title) === 0);
            $ld = substr($le['start'],0,10);
            $ad = substr($api_start,0,10);
            $date_match = ($ld === $ad);
            $cat_match = in_array($api_cat, $le['categories']);
            if ($title_match && $date_match && $cat_match) return array($le);
        }
        return array();
    }

    public function render_preview_table() {
        $opts = get_option($this->option_name, array());
        $start = isset($opts['start_date']) && $opts['start_date'] ? $opts['start_date'] : date('Y-m-d');
        $end = isset($opts['end_date']) && $opts['end_date'] ? $opts['end_date'] : date('Y-m-d', strtotime('+90 days'));
        $local_events = $this->get_local_events($start, $end);

        $teams = isset($opts['teams']) ? $opts['teams'] : array();
        $api_events_all = array();
        foreach ($teams as $t) {
            if (empty($t['id'])) continue;
            $fetched = $this->fetch_api_events($t['id'], $start, $end);
            foreach ($fetched as $fe) {
                $fe['team_id'] = $t['id'];
                $fe['team_prefix'] = isset($t['prefix']) ? $t['prefix'] : '';
                $api_events_all[] = $fe;
            }
        }

        ob_start();
        echo '<table class="widefat fixed striped">';
        echo '<thead><tr><th>API Event (Team)</th><th>Datum</th><th>Kategorie</th><th>Lokales Event</th><th>Status</th></tr></thead><tbody>';
        foreach ($api_events_all as $ae) {
            $matches = $this->match_events($ae, $local_events);
            $api_title_display = esc_html($ae['title']);
            if (!empty($ae['team_prefix'])) $api_title_display = '['.esc_html($ae['team_prefix']).'] '.$api_title_display;
            $api_date = isset($ae['start']) ? esc_html(substr($ae['start'],0,10)) : '-';
            $api_cat = isset($ae['category']) ? esc_html($ae['category']) : '-';

            echo '<tr>';
            echo "<td>{$api_title_display} <br><small>Team: ".esc_html($ae['team_id'])."</small></td><td>{$api_date}</td><td>{$api_cat}</td>";
            if (!empty($matches)) {
                $m = $matches[0];
                echo '<td>' . esc_html($m['title']) . '<br><small>' . esc_html(substr($m['start'],0,10)) . '</small></td><td><mark>Gefunden</mark></td>';
            } else {
                echo '<td><em>Kein lokales Event</em></td><td><strong>Fehlt lokal</strong></td>';
            }
            echo '</tr>';
        }
        echo '</tbody></table>';
        return ob_get_clean();
    }
}

new TEC_API_Sync();
