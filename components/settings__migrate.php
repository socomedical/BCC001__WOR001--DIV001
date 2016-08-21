<?php 
class migrate_divi_theme_settings {
	function migrate_divi_theme_settings() {
		add_action('admin_menu', array(&$this, 'admin_menu'));
	}
	function admin_menu() {
		$page = add_submenu_page('admin.php', 'Migrate Settings', 'Migrate Settings', 'manage_options', 'divi-theme-settings-migration', array(&$this, 'options_page'));
		add_action("load-{$page}", array(&$this, 'import_export'));
		add_submenu_page( 'et_divi_options',__( 'Migrate Settings', 'Divi' ), __( 'Migrate Settings', 'Divi' ), 'manage_options', 'admin.php?page=divi-theme-settings-migration', 'divi-theme-settings-migration' );
	}
	function import_export() {
		if (isset($_GET['action']) && ($_GET['action'] == 'download')) {
			header("Cache-Control: public, must-revalidate");
			header("Pragma: hack");
			header("Content-Type: text/plain");
			header('Content-Disposition: attachment; filename="divi-settings-backup.txt"');
			echo serialize($this->_get_options());
			die();
		}
		if (isset($_POST['upload']) && check_admin_referer('shapeSpace_restoreOptions', 'shapeSpace_restoreOptions')) {
            $obj = $_FILES['file'];
			if ($_FILES["file"]["error"] > 0) {
				// error
			} else {
                if ( is_uploaded_file($obj['tmp_name']) && end(explode(".", $obj['name'])) == 'dat' ) {
                    $contents = file_get_contents($obj['tmp_name']) or die("Failed to open uploaded file."); //Try to open the file, if not, just die.
                    $contents = unserialize($contents) or die("Failed to unserialize data."); //Should never see this unless the content is broken
                    if ( $contents ) {
                        foreach ( $contents as $opt => $val ) {
                            update_option($opt, $val);
                        }
                    }
                }
				$options = unserialize(file_get_contents($_FILES["file"]["tmp_name"]));
				if ($options) {
					foreach ($options as $option) {
						update_option($option->option_name, unserialize($option->option_value));
					}
				}
			}
			wp_redirect(admin_url('admin.php?page=divi-theme-settings-migration&import=1'));
			exit;
		}
	}
	function options_page() {
	
             
    if ( isset($_GET['import']) && $_GET['import'] == '1' ) { ?>
	
	<div class="success">
		<div class="success-container">
		    <span id='close' onclick='this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode); return false;'>x</span>
            <h2>Woop woop!!</h2>
            <p>Your theme settings were imported. Job done!</p>
		</div>
	</div>
                <?php
                }
                ?>		

		<div class="migration-wrap">
			<h2>Migrate Settings</h2>
			<div class="container">
			<p>From here you can back up or restore your Divi Theme data, including all theme options and customizer settings.
			Always make a backup of your current settings before importing different ones.</p>
			<form action="" method="POST" enctype="multipart/form-data">
				<table id="divi-theme-settings-migration">
					<tr class="migration-table">
						<td class="import">
							<h3>Import Divi Theme Settings</h3>
							<p class="description" for="upload">Import your .txt or .dat files here. Please press 'upload file' once and wait. It can take a few minutes to install your data.</p>
							<p><input type="file" name="file" /> <input type="submit" name="upload" id="upload" class="button-primary" value="Upload file" /></p>
							<?php if (function_exists('wp_nonce_field')) wp_nonce_field('shapeSpace_restoreOptions', 'shapeSpace_restoreOptions'); ?>
						</td>
						<td class="backup">
							<h3>Backup Current Divi Theme Settings</h3>
							<p>Download your current theme settings so you can restore them in future at the touch of a button.</p>
							<p><textarea disabled class="widefat code" rows="20" cols="100" onclick="this.select()"><?php echo serialize($this->_get_options()); ?></textarea></p>
							<p><a href="?page=divi-theme-settings-migration&action=download" class="button-secondary">Download Data</a></p>
						</td>
					</tr>
				</table>
			</form>
			</div>
		</div>
		
    <style class="migration-form-styles">
		#divi-theme-settings-migration td { display: block; margin-bottom: 20px; }
		.migration-wrap { background: #f9f9f9; border-radius: 4px; box-shadow: 0px 5px 10px rgba(0,0,0,0.1); position: relative;
        width: 90%; margin: 0 auto; padding-bottom: 20px; }

        .migration-wrap h2 { background: rgb(108, 46, 185); border-radius: 4px 4px 0 0; padding: 24px 0px 26px 60px; color: #fff; 
		font-weight: 100; font-size: 24px; margin-bottom: 30px; }

        .migration-wrap h2:before { font-family: 'etModules'; content: ""; display: block; 
		position: absolute; left: 22px; font-size: 22px; }

        .migration-wrap .container { padding: 0px 30px; }

        .migration-wrap textarea.widefat.code { display: none; }

        .migration-wrap #divi-theme-settings-migration { width: 100%; }

        .migration-wrap td.import { position: relative; float: left; background: #f1f1f1;
		padding: 30px 2%; margin: 1%; min-height: 280px; }

        .migration-wrap td.backup { float: left; background: #f1f1f1; padding: 30px 2%;
		margin: 1%; min-height: 280px; position: relative; }

        .migration-wrap .button-primary { background: #303030; box-shadow: none; border: none; text-shadow: none; text-transform: uppercase;
		font-size: 14px; height: 40px; width: 200px; float: left; margin-top: 50px; position: absolute; left: 20px; bottom: 20px; }

        .migration-wrap .button-secondary { background: #303030; margin-top: 50px; float: left; text-transform: uppercase; box-shadow: none;
		border: none; text-shadow: none; color: #fff; height: 40px; width: 200px; text-align: center; position: absolute; left: 20px; bottom: 20px;
		font-size: 14px; padding-top: 6px; }
		
		.migration-wrap h3 { text-transform: uppercase; }

        .migration-wrap .container p { font-size: 16px; margin: 2%; }
		
		.migration-wrap #divi-theme-settings-migration p { margin: 0; }
		
		.migration-wrap .button-secondary:hover, .migration-wrap .button-primary:hover { background: #0EAD69; color: #fff; }
		
		.migration-wrap input[type=file] { margin-top: 20px; background: #dedede; padding: 10px; border-radius: 4px; width: 100%; }

        @media  (max-width: 980px) { 
		.migration-wrap td.import, .migration-wrap td.backup { width: 94%; }}
		
        @media  (min-width: 981px) {
        .migration-wrap td.import, .migration-wrap td.backup { width: 44%; }}	
		
		.success-container { position: absolute; display: block; width: 340px; background: #fff; padding: 20px; z-index: 10;
		border-radius: 2px; box-shadow: 0px 10px 10px rgba(0,0,0,0.3); top: 10%; left: calc(50% - 170px); border-top: 5px solid #0EAD69; }
		
		.success-container h2 { position: relative; text-transform: uppercase; font-size: 22px; color: #0EAD69; padding-left: 30px; }
		
		.success-container h2:before { position: absolute; display: block; font-family: 'ETmodules'; content: ""; left: 0; }
		
		.success-container p { font-size: 14px; }
		
		#close { float: right; font-size: 20px; font-weight: 900; cursor: pointer; color: #fff; background: #0EAD69; border-radius: 50%;
		padding-top: 0; padding-right: 5px; padding-bottom: 3px; padding-left: 5px; }
		
		p.description { font-style: normal; color: #444; }
    </style>
	
	<?php }
	function _display_options() {
		$options = unserialize($this->_get_options());
	}
	function _get_options() {
		global $wpdb;
		return $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name = 'et_divi'"); 
	}
}
new migrate_divi_theme_settings();

?>